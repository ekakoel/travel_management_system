<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\DokuWebhookController;
use App\Http\Controllers\ReviewController;
use App\Models\TemporaryReviewLink;
use App\Models\SubmittedReview;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('version', function () {
    return response()->json(['version' => config('app.version')]);
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    Log::debug('User:' . serialize($request->user()));
    return $request->user();
});


Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () {
    Route::get('profile', 'ProfileController@profile');
    Route::put('profile', 'ProfileController@updateProfile');
    Route::post('change-password', 'ProfileController@changePassword');
    Route::get('tag/list', 'TagController@list');
    Route::get('category/list', 'CategoryController@list');
    Route::post('product/upload', 'ProductController@upload');

    Route::apiResources([
        'user' => 'UserController',
        'product' => 'ProductController',
        'category' => 'CategoryController',
        'tag' => 'TagController',
    ]);
});

Route::post('/doku/webhook', [DokuWebhookController::class, 'handleWebhook']);

Route::post('/submit-review', [ReviewController::class, 'store']);
Route::post('/submit-wedding-review', [ReviewController::class, 'store_wedding_review']);

Route::get('/validate-review-link/{booking_code}/{jumlah_review}', [ReviewController::class, 'validateLink']);
Route::get('/validate-wedding-review-link/{booking_code}/{jumlah_review}', [ReviewController::class, 'validate_wedding_review_link']);

Route::get('/validate-review-link', function (Request $request) {
    $booking_code = $request->booking_code;
    $jumlah_review = $request->jumlah_review;

    $link = TemporaryReviewLink::where('booking_code', $booking_code)
        ->where('jumlah_review', $jumlah_review)
        ->where('expires_at', '>', now())
        ->first();

    if (!$link) {
        return response()->json(['valid' => false, 'reason' => 'expired_or_not_found']);
    }

    $review_count = SubmittedReview::where('booking_code', $booking_code)->count();

    if ($review_count >= $link->jumlah_review) {
        return response()->json(['valid' => false, 'reason' => 'quota_reached']);
    }

    return response()->json(['valid' => true]);
});

Route::get('/validate-wedding-review-link', function (Request $request) {
    $booking_code = $request->booking_code;
    $jumlah_review = $request->jumlah_review;

    $link = TemporaryWeddingReviewLink::where('booking_code', $booking_code)
        ->where('jumlah_review', $jumlah_review)
        ->where('expires_at', '>', now())
        ->first();

    if (!$link) {
        return response()->json(['valid' => false, 'reason' => 'expired_or_not_found']);
    }

    $review_count = SubmittedWeddingReview::where('booking_code', $booking_code)->count();

    if ($review_count >= $link->jumlah_review) {
        return response()->json(['valid' => false, 'reason' => 'quota_reached']);
    }

    return response()->json(['valid' => true]);
});
Route::post('/generate-review-link', [ReviewLinkController::class, 'generateReviewLink']);
Route::post('/generate-wedding-review-link', [ReviewLinkController::class, 'generateWeddingReviewLink']);

// Route::post('/doku/webhook', function (Request $request) {
//     // Log request untuk debugging
//     Log::info('DOKU Webhook Data:', $request->all());

//     // Ambil data dari request
//     $status = $request->input('transaction.status'); // Status transaksi
//     $invoice_number = $request->input('order.invoice_number'); // ID pesanan
//     $invoice = InvoiceAdmin::where('inv_no',$invoice_number)->first();
//     // Cari pesanan berdasarkan invoice_number
//     $order = Orders::where('rsv_id', $invoice->rsv_id)->first();
//     $doku_va = DokuVirtualAccount::where('invoice_id',$invoice->id)->first();
//     if (!$order) {
//         return response()->json(['message' => 'Order not found'], 404);
//     }

//     // Perbarui status pesanan berdasarkan status dari DOKU
//     if ($status === 'SUCCESS') {
//         $order->status = 'Paid'; // Pesanan berhasil dibayar
//         $doku_va->status = 'Paid'; // Pesanan berhasil dibayar
//     } elseif ($status === 'FAILED' || $status === 'EXPIRED') {
//         $order->status = 'Approved'; // Pembayaran gagal atau kedaluwarsa
//         $doku_va->status = 'Pending'; // Pembayaran gagal atau kedaluwarsa
//     } else {
//         $order->status = 'Approved'; // Status lain
//         $doku_va->status = 'Pending'; // Pembayaran gagal atau kedaluwarsa
//     }
//     $order->save();
//     $doku_va->save();
//     return response()->json(['message' => 'Order status updated']);
// });

// Route::post('/doku/webhook', function (Request $request) {
//     // Log seluruh request dari DOKU
//     Log::info('DOKU Webhook Data:', $request->all());

//     // Ambil data dari request
//     $status = $request->input('transaction.status'); // Status transaksi
//     $invoice_number = $request->input('order.invoice_number'); // ID pesanan
//     $amount = $request->input('order.amount'); // Jumlah pembayaran

//     // Cek apakah invoice_number ada
//     if (!$invoice_number) {
//         Log::error('Invoice Number is missing');
//         return response()->json(['message' => 'Invoice Number is missing'], 400);
//     }

//     // Cari invoice berdasarkan invoice_number
//     $invoice = InvoiceAdmin::where('inv_no', $invoice_number)->first();
//     if (!$invoice) {
//         Log::error('Invoice not found', ['invoice_number' => $invoice_number]);
//         return response()->json(['message' => 'Invoice not found'], 404);
//     }

//     // Cari pesanan berdasarkan rsv_id dari invoice
//     $order = Orders::where('rsv_id', $invoice->rsv_id)->first();
//     if (!$order) {
//         Log::error('Order not found', ['rsv_id' => $invoice->rsv_id]);
//         return response()->json(['message' => 'Order not found'], 404);
//     }
//     if ($status === 'SUCCESS') {
//         $order->status = 'Paid'; // Pesanan berhasil dibayar
//         $doku_va->status = 'Paid'; // Pesanan berhasil dibayar
//     } elseif ($status === 'FAILED' || $status === 'EXPIRED') {
//         $order->status = 'Approved'; // Pembayaran gagal atau kedaluwarsa
//         $doku_va->status = 'Pending'; // Pembayaran gagal atau kedaluwarsa
//     } else {
//         $order->status = 'Approved'; // Status lain
//         $doku_va->status = 'Pending'; // Pembayaran gagal atau kedaluwarsa
//     }
//     $order->save();
//     $doku_va->save();
//     // Update status pesanan berdasarkan status transaksi dari DOKU

//     Log::info('Order Updated', ['order_id' => $order->id, 'status' => $order->status]);

//     return response()->json(['message' => 'Order status updated']);
// });
