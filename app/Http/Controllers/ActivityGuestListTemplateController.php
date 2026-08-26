<?php

namespace App\Http\Controllers;

use App\Services\Activities\ActivityGuestListService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityGuestListTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function __invoke(string $format, ActivityGuestListService $guestLists): Response|StreamedResponse
    {
        $format = strtolower($format);

        if ($format === 'csv') {
            return response($guestLists->csvTemplateContent(), 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="activity-guest-list-template.csv"',
            ]);
        }

        if ($format === 'xlsx') {
            return response($guestLists->xlsxTemplateContent(), 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="activity-guest-list-template.xlsx"',
            ]);
        }

        abort(404);
    }
}
