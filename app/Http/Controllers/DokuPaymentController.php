<?php

namespace App\Http\Controllers;

use App\Exceptions\DokuException;
use App\Models\Orders;
use App\Services\Payments\DokuPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DokuPaymentController extends Controller
{
    public function checkout(int $order, DokuPaymentService $payments)
    {
        $order = Orders::query()
            ->with(['reservations.invoice.currency'])
            ->ownedBy((int) Auth::id())
            ->where(function ($query) {
                $query->accommodationService()
                    ->orWhere('service', Orders::PUBLIC_TRANSPORT_SERVICE)
                    ->orWhere('service', Orders::PUBLIC_TOUR_SERVICE)
                    ->orWhere('service', Orders::PUBLIC_ACTIVITY_SERVICE);
            })
            ->findOrFail($order);

        if (!$payments->supportsOrder($order)) {
            abort(404);
        }

        try {
            $payment = $payments->startCheckout($order, (int) Auth::id());
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        } catch (DokuException $exception) {
            Log::warning('DOKU checkout could not be started.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('error', __('messages.Online payment is temporarily unavailable. Please try again later.'));
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', __('messages.Online payment is temporarily unavailable. Please try again later.'));
        }

        if (!$payment->checkout_url) {
            return back()->with('error', __('messages.Online payment is temporarily unavailable. Please try again later.'));
        }

        return redirect()->away($payment->checkout_url);
    }
}
