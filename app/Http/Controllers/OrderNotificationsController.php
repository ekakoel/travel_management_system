<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class OrderNotificationsController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()
            ->where('data->kind', 'order')
            ->latest()
            ->paginate(20);

        return view('frontend.home.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $ownedNotification = $request->user()->notifications()
            ->where('data->kind', 'order')
            ->whereKey($notification)
            ->firstOrFail();

        if ($ownedNotification instanceof DatabaseNotification && !$ownedNotification->read_at) {
            $ownedNotification->markAsRead();
        }

        return redirect()->route('order-notifications.index');
    }
}
