<?php

namespace App\View\Composers;

use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NavbarComposer
{
    public function compose(View $view): void
    {
        $companyProfile = BusinessProfile::first();
        $unreadOrderNotifications = Auth::user()?->unreadNotifications()
            ->where('data->kind', 'order')
            ->count() ?? 0;

        $view->with('companyProfile', $companyProfile)
            ->with('unreadOrderNotifications', $unreadOrderNotifications);
    }
}
