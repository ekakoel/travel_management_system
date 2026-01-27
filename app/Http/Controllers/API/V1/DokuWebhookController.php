<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use App\Models\Orders;
use App\Models\InvoiceAdmin;
use Illuminate\Http\Request;
use App\Models\DokuVirtualAccount;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DokuWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('DOKU Webhook Data:', $request->all());
        $now = Carbon::now();
        $status = $request->input('transaction.status');
        $invNoString = $request->input('order.invoice_number');
        if (str_starts_with($invNoString, "RE-")) {
            $invoice_number = preg_replace('/^RE-|-\d+$/', '', $invNoString);
        }else{
            $invoice_number = $request->input('order.invoice_number');
        }
        $paymentMethod = $request->input('service.id');
        $provider = $request->input('acquirer.id');
        $payment_date = $now;
        if ($paymentMethod === "VIRTUAL_ACCOUNT") {
            $payment_method = "Virtual Account";
        }elseif ($paymentMethod === "CREDIT_CARD") {
            $payment_method = "Credit Card";
        }elseif ($paymentMethod === "ONLINE_TO_OFFLINE") {
            $payment_method = "Convenience Store";
        }elseif ($paymentMethod === "EMONY") {
            $payment_method = "E-Wallet";
        }elseif ($paymentMethod === "DIRECT_DEBIT") {
            $payment_method = "Direct Debit";
        }elseif ($paymentMethod === "PEER_TO_PEER") {
            $payment_method = "Paylater";
        }
        if (!$invoice_number) {
            Log::error('Invoice Number is missing');
            return response()->json(['message' => 'Invoice Number is missing'], 400);
        }
        $invoice = InvoiceAdmin::where('inv_no', $invoice_number)->first();
        if (!$invoice) {
            Log::error('Invoice not found', ['invoice_number' => $invoice_number]);
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $order = Orders::where('rsv_id', $invoice->rsv_id)->first();
        if (!$order) {
            Log::error('Order not found', ['rsv_id' => $invoice->rsv_id]);
            return response()->json(['message' => 'Order not found'], 404);
        }
        $doku_va = DokuVirtualAccount::where('invoice_id',$invoice->id)->first();
        if ($status === 'SUCCESS') {
            $order->status = 'Paid';
            $invoice->balance = 0;
            $doku_va->status = 'Paid';
            $doku_va->payment_method = $payment_method;
            $doku_va->provider = $provider;
            $doku_va->payment_date = $payment_date;
        }
        $order->save();
        $invoice->save();
        $doku_va->save();
        Log::info('Order Updated', ['order_id' => $order->id, 'status' => $order->status]);
        return response()->json(['message' => 'Order status updated']);
    }
}
