<?php

namespace App\Http\Controllers;

use App\Models\Spks;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class SpkReportController extends Controller
{
    public function show(string $token): View
    {
        $spk = Spks::query()
            ->where('public_token', $token)
            ->with([
                'reservation',
                'driver',
                'vehicle',
                'destinations',
            ])
            ->firstOrFail();
        $expired_date = Carbon::now()->subDay()->format('Y-m-d');

        // return view('frontend.spks.report', compact('spk','expired_date'));
        return view('admin.transportmanagement.spks.report_spk', compact('spk','expired_date'));
    }
}