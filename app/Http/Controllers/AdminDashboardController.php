<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profile.complete', 'approve', 'internal']);
    }

    public function index(Request $request, AdminDashboardService $dashboardService)
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:today,week,month,year'],
        ]);

        return view('backend.admin.dashboard.index', $dashboardService->build([
            'period' => $validated['period'] ?? 'month',
        ]));
    }
}
