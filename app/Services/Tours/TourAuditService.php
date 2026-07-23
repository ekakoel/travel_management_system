<?php

namespace App\Services\Tours;

use App\Models\UserLog;
use Illuminate\Http\Request;

class TourAuditService
{
    public function userLog(Request $request, string $action, string $subservice, int|string|null $subserviceId, string $page, string $note, string $service = 'Tour'): UserLog
    {
        return UserLog::create([
            'action' => $action,
            'service' => $service,
            'subservice' => $subservice,
            'subservice_id' => $subserviceId,
            'page' => $page,
            'user_id' => $request->input('author', auth()->id()),
            'user_ip' => $request->getClientIp(),
            'note' => $note,
        ]);
    }
}
