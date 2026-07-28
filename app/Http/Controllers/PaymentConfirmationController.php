<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\User;
use App\Models\Orders;
use App\Models\OrderLog;
use App\Models\Reservation;
use App\Models\InvoiceAdmin;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Models\PaymentConfirmation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StorePaymentConfirmationRequest;
use App\Http\Requests\UpdatePaymentConfirmationRequest;
use App\Services\AccommodationFinancialFileService;

class PaymentConfirmationController extends Controller
{
    private const CUSTOMER_PAYMENT_ALLOWED_STATUS = 'Approved';
    private const EDITABLE_CUSTOMER_RECEIPT_STATUS = 'Pending';

    private function resolveOrderDetailRedirect(Orders $order): string
    {
        if ($order->service == "Hotel" || $order->service == "Hotel Promo" || $order->service == "Hotel Package") {
            return "/detail-order-hotel/$order->id";
        }

        if ($order->service == "Private Villa") {
            return "/detail-order-villa/$order->id";
        }

        if ($order->service == "Transport") {
            return "/detail-order-transport/$order->id";
        }

        if ($order->service == "Activity") {
            return "/detail-order-hotel/$order->id";
        }

        return "/detail-order-tour/$order->id";
    }

    private function getInvoicePaymentDeadline(?InvoiceAdmin $invoice): ?Carbon
    {
        if (!$invoice || !$invoice->due_date) {
            return null;
        }

        return Carbon::parse($invoice->due_date);
    }

    private function hasPaymentSubmission(?InvoiceAdmin $invoice): bool
    {
        if (!$invoice) {
            return false;
        }

        return $invoice->payment()->whereIn('status', ['Pending', 'Valid', 'Paid'])->exists();
    }

    private function findOwnedPublicPaymentOrder(int $id): Orders
    {
        return Orders::with(['reservations.invoice.payment'])
            ->ownedBy(Auth::id())
            ->where(function ($query) {
                $query->accommodationService()
                    ->orWhere('service', Orders::PUBLIC_TRANSPORT_SERVICE)
                    ->orWhere('service', Orders::PUBLIC_TOUR_SERVICE)
                    ->orWhere('service', Orders::PUBLIC_ACTIVITY_SERVICE);
            })
            ->findOrFail($id);
    }

    private function resolveInvoiceForOrder(Orders $order): ?InvoiceAdmin
    {
        $reservation = $order->reservations;

        if (!$reservation || (int) $reservation->id !== (int) $order->rsv_id) {
            return null;
        }

        $invoice = $reservation->invoice;

        if (!$invoice || (int) $invoice->rsv_id !== (int) $reservation->id) {
            return null;
        }

        return $invoice;
    }

    private function orderIsEligibleForCustomerPayment(Orders $order, ?InvoiceAdmin $invoice): bool
    {
        return $order->status === self::CUSTOMER_PAYMENT_ALLOWED_STATUS
            && $invoice
            && (float) $invoice->balance > 0;
    }

    private function receiptPath(string $path): string
    {
        return Storage::disk(AccommodationFinancialFileService::DISK)->path($path);
    }

    private function deleteReceiptFile(?string $path): void
    {
        if ($path && (
            Str::startsWith($path, AccommodationFinancialFileService::PAYMENT_ROOT.'/')
            || Str::startsWith($path, AccommodationFinancialFileService::TRANSPORT_PAYMENT_ROOT.'/')
            || Str::startsWith($path, AccommodationFinancialFileService::TOUR_PAYMENT_ROOT.'/')
            || Str::startsWith($path, AccommodationFinancialFileService::ACTIVITY_PAYMENT_ROOT.'/')
        )) {
            Storage::disk(AccommodationFinancialFileService::DISK)->delete($path);
        }
    }

    private function makeSafeReceiptFilename(InvoiceAdmin $invoice, $file): string
    {
        $invoiceNumber = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $invoice->inv_no) ?: 'invoice';
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension());

        return $invoiceNumber.'_'.Carbon::now()->format('YmdHis').'_'.Str::random(16).'.'.$extension;
    }

    private function storeReceiptFile($file, InvoiceAdmin $invoice, Orders $order): string
    {
        $receiptName = $this->makeSafeReceiptFilename($invoice, $file);
        $receiptPath = app(AccommodationFinancialFileService::class)->privateReceiptPath($order, $receiptName);
        Storage::disk(AccommodationFinancialFileService::DISK)->putFileAs(dirname($receiptPath), $file, basename($receiptPath));

        return $receiptPath;
    }

    private function autoCancelExpiredOrder(Orders $order, ?InvoiceAdmin $invoice, Request $request): Orders
    {
        $deadline = $this->getInvoicePaymentDeadline($invoice);

        if (
            $order->status !== 'Approved'
            || !$deadline
            || !$deadline->isPast()
            || $this->hasPaymentSubmission($invoice)
        ) {
            return $order;
        }

        $order->update([
            'status' => 'Canceled',
            'msg' => 'Automatically canceled because no payment confirmation was submitted within 48 hours after approval.',
        ]);

        if ($order->rsv_id) {
            Reservation::where('id', $order->rsv_id)->update([
                'status' => 'Canceled',
            ]);
        }

        OrderLog::create([
            'order_id' => $order->id,
            'action' => 'Auto Cancel Payment Deadline',
            'url' => $request->getClientIp(),
            'method' => 'Update',
            'agent' => $order->name,
            'admin' => Auth::id(),
        ]);

        $order->status = 'Canceled';

        return $order;
    }

    public function payment_confirmation(StorePaymentConfirmationRequest $request,$id)
    {
        $now = Carbon::now();
        $order = $this->findOwnedPublicPaymentOrder((int) $id);
        $invoice = $this->resolveInvoiceForOrder($order);

        $order = $this->autoCancelExpiredOrder($order, $invoice, $request);
        $order->loadMissing('reservations.invoice.payment');
        $invoice = $this->resolveInvoiceForOrder($order);

        if (!$this->orderIsEligibleForCustomerPayment($order, $invoice)) {
            return redirect($this->resolveOrderDetailRedirect($order))->with('error', 'This order is no longer available for payment confirmation.');
        }

        $receiptName = $this->storeReceiptFile($request->file('receipt_name'), $invoice, $order);

        try {
            DB::transaction(function () use ($request, $order, $invoice, $receiptName) {
                PaymentConfirmation::create([
                    'kurs_id' => $invoice->currency_id ?: 1,
                    'receipt_img' => $receiptName,
                    'inv_id' => $invoice->id,
                    'status' => 'Pending',
                ]);

                OrderLog::create([
                    'order_id' => $order->id,
                    'action' => 'Upload Receipt',
                    'url' => $request->getClientIp(),
                    'method' => 'Upload',
                    'agent' => $order->name,
                    'admin' => Auth::id(),
                ]);
            });
        } catch (\Throwable $exception) {
            $this->deleteReceiptFile($receiptName);

            throw $exception;
        }

        $agent = User::where('id',$order->user_id)->first();
        $reservation = $order->reservations;
        $title = "Payment Confirmation ".$reservation->rsv_no;
        $order_link = 'https://online.balikamitour.com/orders-admin-'.$order->id;
        $data = [
            'now'=>$now,
            'agent'=>$agent,
            'title'=>$title,
            'order'=>$order,
            'order_link'=>$order_link,
        ];
        $receipt = $this->receiptPath($receiptName);
        Mail::send('emails.paymentConfirmation', $data, function($message)use($data, $receipt) {
            $message->to(config('app.reservation_mail'))
                ->subject($data["title"])
                ->attach($receipt);
        });

        return redirect($this->resolveOrderDetailRedirect($order))->with('success','Payment proof has been sent.');
    }
    // WEDDING
    public function wedding_payment_confirmation(Request $request,$id)
    {
        $now = Carbon::now();
        $orderWedding = OrderWedding::findOrFail($id);
        $reservation = Reservation::where('id',$orderWedding->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        if($request->hasFile("receipt_name")){
            $file=$request->file("receipt_name");
            $receipt_name=$invoice->inv_no.'_'.time().'_'.$file->getClientOriginalName();
            $file->move("storage/receipt/weddings/",$receipt_name);
            $agent = User::where('id',$orderWedding->agent_id)->first();
            $status="Pending";
            $payment =new PaymentConfirmation([
                "receipt_img"=>$receipt_name,
                "inv_id"=>$invoice->id,
                "status"=>$status,
            ]);
            $payment->save();
            
            $order_log =new OrderLog([
                "order_id"=>$orderWedding->id,
                "action"=>"Upload Receipt",
                "url"=>$request->getClientIp(),
                "method"=>"Upload",
                "agent"=>$agent->name,
                "admin"=>Auth::user()->id,
            ]);
            
            $order_log->save();
            $title = "Wedding Payment Confirmation ".$reservation->rsv_no;
            $order_link = 'https://online.balikamitour.com/orders-admin-'.$orderWedding->id;
            $data = [
                'now'=>$now,
                'agent'=>$agent,
                'title'=>$title,
                'orderWedding'=>$orderWedding,
                'reservation'=>$reservation,
                'invoice'=>$invoice,
                'order_link'=>$order_link,
            ];
            if (config('filesystems.default') == 'public'){
                $receipt =realpath("storage/receipt/weddings/".$receipt_name);
            }else {
                $receipt = storage::url("storage/receipt/weddings/".$receipt_name);
            }
            Mail::send('emails.paymentConfirmationWedding', $data, function($message)use($data, $receipt) {
                $message->to(config('app.reservation_mail'))
                    ->subject($data["title"])
                    ->attach($receipt);
            });

            return redirect("/detail-order-wedding-$orderWedding->orderno")->with('success','Payment proof has been sent.');
        }else{
            return redirect("/detail-order-wedding-$orderWedding->orderno")->with('error','Please try again');
        }
    }
    public function update_payment_confirmation(UpdatePaymentConfirmationRequest $request,$id)
    {
        $order = $this->findOwnedPublicPaymentOrder((int) $id);
        $invoice = $this->resolveInvoiceForOrder($order);

        if (!$this->orderIsEligibleForCustomerPayment($order, $invoice)) {
            return redirect($this->resolveOrderDetailRedirect($order))->with('error','This order is no longer available for payment confirmation.');
        }

        $receipt = PaymentConfirmation::where('inv_id', $invoice->id)
            ->where('status', self::EDITABLE_CUSTOMER_RECEIPT_STATUS)
            ->latest('id')
            ->first();

        if (!$receipt) {
            return redirect($this->resolveOrderDetailRedirect($order))->with('error','This payment confirmation cannot be updated.');
        }

        $oldReceiptName = $receipt->receipt_img;
        $receiptName = $this->storeReceiptFile($request->file('activity_receipt_name'), $invoice, $order);

        try {
            DB::transaction(function () use ($request, $order, $receipt, $receiptName) {
                $receipt->update([
                    'receipt_img' => $receiptName,
                    'status' => self::EDITABLE_CUSTOMER_RECEIPT_STATUS,
                    'note' => null,
                ]);

                OrderLog::create([
                    'order_id' => $order->id,
                    'action' => 'Change Receipt',
                    'url' => $request->getClientIp(),
                    'method' => 'Update',
                    'agent' => $order->name,
                    'admin' => Auth::id(),
                ]);
            });
        } catch (\Throwable $exception) {
            $this->deleteReceiptFile($receiptName);

            throw $exception;
        }

        $this->deleteReceiptFile($oldReceiptName);

        return redirect($this->resolveOrderDetailRedirect($order))->with('success','Payment proof has been updated.');
    }

}
