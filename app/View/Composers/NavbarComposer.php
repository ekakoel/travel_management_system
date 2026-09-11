<?php

namespace App\View\Composers;

use App\Models\BusinessProfile;
use Illuminate\View\View;

class NavbarComposer
{
    public function compose(View $view): void
    {
        $companyProfile = BusinessProfile::first();

        $view->with('companyProfile', $companyProfile);
    }
}