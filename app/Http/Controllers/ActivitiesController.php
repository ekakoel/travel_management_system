<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{   
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {
        return redirect()->route('view.activities-service');
    }
// Search Activities =========================================================================================>
    public function search_activities(Request $request)
    {
        return redirect()->route('view.activities-service', array_filter([
            'search_name' => $request->input('search_name'),
            'search_type' => $request->input('activities_type'),
            'search_location' => $request->input('location'),
        ], static fn ($value) => filled($value)));
    }
// View Activities Detail =========================================================================================>
    public function activitydetail(string $code)
    {
        return redirect()->route('view.activity-public-detail', ['code' => $code]);
    }

    public function activity_check_code(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
            'bookingcode' => ['nullable', 'string', 'max:100'],
        ]);
        $activity = Activities::query()->findOrFail($validated['activity_id']);

        return redirect()->route('view.activity-public-detail', array_filter([
            'code' => $activity->code,
            'booking_code' => $validated['bookingcode'] ?? null,
        ], static fn ($value) => filled($value)));
    }
// View Activities Detail with code =========================================================================================>
    public function activitydetail_bookingcode(string $code, string $bcode)
    {
        return redirect()->route('view.activity-public-detail', [
            'code' => $code,
            'booking_code' => $bcode,
        ]);
    }
}
