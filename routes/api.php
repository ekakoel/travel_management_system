<?php

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WhatsAppController;
use App\Models\SubmittedReview;
use App\Models\SubmittedWeddingReview;
use App\Models\TemporaryReviewLink;
use App\Models\TemporaryWeddingReviewLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
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
Route::post('/generate-review-link', [ReviewController::class, 'generateReviewLink']);
Route::post('/generate-wedding-review-link', [ReviewController::class, 'generateWeddingReviewLink']);
