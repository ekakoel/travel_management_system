<?php

namespace App\View\Composers;

use App\Services\Navigation\BackendNavigationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BackendNavigationComposer
{
    public function __construct(
        private readonly BackendNavigationService $navigation,
        private readonly Request $request,
    ) {
    }

    public function compose(View $view): void
    {
        if (array_key_exists('backendNavigation', $view->getData())) {
            return;
        }

        $view->with('backendNavigation', $this->navigation->data($this->request));
    }
}
