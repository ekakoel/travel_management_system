<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Payments\DokuPaymentService;
use App\Services\Payments\DokuService;
use Illuminate\Http\Request;

class DokuNotificationController extends Controller
{
    public function handle(Request $request, DokuService $doku, DokuPaymentService $payments)
    {
        if (!$doku->notificationIsValid($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        try {
            $payments->applyNotification($payload, (string) $request->header('Request-Id', '') ?: null);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Notification processing failed'], 500);
        }

        return response()->json(['message' => 'OK']);
    }
}
