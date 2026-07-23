<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Backend\Operations\Activities\ActivityAdminController;
use App\Http\Requests\StoreActivityAdminRequest;
use App\Http\Requests\UpdateActivityAdminRequest;
use App\Services\Activities\ActivityAssetService;
use App\Services\Activities\ActivityAuditService;
use App\Services\Activities\ActivityInventoryService;

class ActivitiesAdminController extends ActivityAdminController
{
    public function view_detail_activity($id)
    {
        return $this->show($id, app(ActivityInventoryService::class));
    }

    public function view_edit_activity($id)
    {
        return $this->edit($id, app(ActivityInventoryService::class));
    }

    public function view_add_activity()
    {
        return $this->create(app(ActivityInventoryService::class));
    }

    public function func_add_activity(StoreActivityAdminRequest $request)
    {
        return $this->store($request, app(ActivityAssetService::class), app(ActivityAuditService::class));
    }

    public function func_update_activity(UpdateActivityAdminRequest $request, $id)
    {
        return $this->update($request, $id, app(ActivityAssetService::class), app(ActivityAuditService::class));
    }
}
