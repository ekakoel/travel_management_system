<?php

namespace App\Services\Activities;

use App\Models\UserLog;
use Illuminate\Http\Request;

class ActivityAuditService
{
    public function userLog(Request $request, string $action, int|string|null $activityId, string $page, string $note): UserLog
    {
        return UserLog::create([
            'action' => $action,
            'service' => 'Activity',
            'subservice' => 'Activity',
            'subservice_id' => $activityId,
            'page' => $page,
            'user_id' => $request->input('author', auth()->id()),
            'user_ip' => $request->getClientIp(),
            'note' => $note,
        ]);
    }
}
