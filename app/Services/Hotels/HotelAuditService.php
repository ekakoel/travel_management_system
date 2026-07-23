<?php

namespace App\Services\Hotels;

use App\Models\ActionLog;
use App\Models\UserLog;
use Illuminate\Http\Request;

class HotelAuditService
{
    public function userLog(
        Request $request,
        string $action,
        string $subservice,
        int|string|null $subserviceId,
        string $page,
        string $note,
        string $service = 'Hotel'
    ): UserLog {
        return UserLog::create([
            'action' => $action,
            'service' => $service,
            'subservice' => $subservice,
            'subservice_id' => $subserviceId,
            'page' => $page,
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => $note,
        ]);
    }

    public function actionLog(array $attributes): ActionLog
    {
        return ActionLog::create($attributes);
    }
}
