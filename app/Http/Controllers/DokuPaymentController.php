<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Orders;
use App\Models\Reservation;
use App\Models\InvoiceAdmin;
use Illuminate\Http\Request;
use App\Services\DokuService;
use App\Services\PaymentServices;
use App\Models\DokuVirtualAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DokuPaymentController extends Controller
{
    protected $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }



    public function createPayment(Request $request,$id)
    {
        $order = Orders::find($id);
        $invoice = InvoiceAdmin::where('rsv_id',$order->rsv_id)->first();
        $agent = User::find($order->sales_agent);
        $paymentResponse = $this->dokuService->createPayment($order, $invoice, $agent);
        return response()->json($paymentResponse);
    }

    public function storeResponse(Request $request)
    {
        $data = $request->all();
        $va = DokuVirtualAccount::create([
            'invoice_number'         => $data['order']['invoice_number'],
            'virtual_account_number' => $data['virtual_account_info']['virtual_account_number'],
            'amount'                 => 41745591, // Gantilah dengan data dari request jika tersedia
            'created_date'           => now(),
            'expired_date'           => $data['virtual_account_info']['expired_date'],
            'created_date_utc'       => $data['virtual_account_info']['created_date_utc'],
            'expired_date_utc'       => $data['virtual_account_info']['expired_date_utc'],
            'how_to_pay_page'        => $data['virtual_account_info']['how_to_pay_page'],
            'how_to_pay_api'         => $data['virtual_account_info']['how_to_pay_api'],
            'status'                 => 'pending',
        ]);

        return response()->json(['message' => 'Data saved successfully'], 200);
    }

    /**
     * Menampilkan halaman pembayaran berdasarkan invoice
     */
    public function showPaymentPage($invoice_number)
    {
        $va = DokuVirtualAccount::where('invoice_number', $invoice_number)->firstOrFail();
        return view('doku.doku-payment', compact('va'));
    }

    /**
     * Webhook dari DOKU untuk mengupdate status pembayaran
     */
    public function updateStatus(Request $request)
    {
        $data = $request->all();

        $va = DokuVirtualAccount::where('invoice_number', $data['order']['invoice_number'])->first();

        if ($va) {
            $va->update([
                'status' => $data['transaction']['status'] === 'SUCCESS' ? 'paid' : 'failed'
            ]);

            return response()->json(['message' => 'Status updated'], 200);
        }

        return response()->json(['message' => 'Invoice not found'], 404);
    }
}