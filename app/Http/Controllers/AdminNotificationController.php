<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->get()
            ->filter(function ($notification) {
                $agentId = $notification->data['agent_id'] ?? null;
                if (!$agentId) return false;

                $agent = Agent::find($agentId);
                return $agent && $agent->status === 'pending';
            });

        // Manual pagination (karena sudah pakai filter collection)
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $pagedData = $notifications->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $notifications->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.notifications.index', ['notifications' => $paginated]);
    }
}
