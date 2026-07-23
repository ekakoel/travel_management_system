<?php

namespace App\Http\Controllers;

use App\Models\FooterLink;
use App\Models\FooterSetting;
use App\Services\FooterContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FooterManagerController extends Controller
{
    protected FooterContentService $footerContentService;

    public function __construct(FooterContentService $footerContentService)
    {
        $this->middleware(['auth', 'verified', 'type:admin']);
        $this->footerContentService = $footerContentService;
    }

    public function index()
    {
        $settings = FooterSetting::query()
            ->orderBy('key')
            ->get();

        $links = FooterLink::query()
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->groupBy('group');

        $summary = [
            'settings' => $settings->count(),
            'activeSettings' => $settings->where('status', true)->count(),
            'groups' => $links->count(),
            'links' => $links->flatten(1)->count(),
            'activeLinks' => $links->flatten(1)->where('status', true)->count(),
        ];

        return view('backend.admin.footer-manager.index', compact('settings', 'links', 'summary'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'settings' => ['nullable', 'array'],
            'settings.*.value' => ['nullable', 'string', 'max:5000'],
            'settings.*.value_traditional' => ['nullable', 'string', 'max:5000'],
            'settings.*.value_simplified' => ['nullable', 'string', 'max:5000'],
            'settings.*.status' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['settings'] ?? [] as $id => $settingData) {
                $setting = FooterSetting::find($id);

                if (!$setting) {
                    continue;
                }

                $setting->update([
                    'value' => $settingData['value'] ?? null,
                    'value_traditional' => $settingData['value_traditional'] ?? null,
                    'value_simplified' => $settingData['value_simplified'] ?? null,
                    'status' => (bool) ($settingData['status'] ?? false),
                ]);
            }
        });

        $this->footerContentService->forget();

        return redirect()
            ->route('admin.footer-manager.index')
            ->with('success', 'Footer settings have been updated successfully.');
    }

    public function storeLink(Request $request)
    {
        FooterLink::create($this->validatedLinkData($request));
        $this->footerContentService->forget();

        return redirect()
            ->route('admin.footer-manager.index')
            ->with('success', 'Footer link has been added successfully.');
    }

    public function updateLink(Request $request, FooterLink $footerLink)
    {
        $footerLink->update($this->validatedLinkData($request, $footerLink));
        $this->footerContentService->forget();

        return redirect()
            ->route('admin.footer-manager.index')
            ->with('success', 'Footer link has been updated successfully.');
    }

    public function destroyLink(FooterLink $footerLink)
    {
        $footerLink->delete();
        $this->footerContentService->forget();

        return redirect()
            ->route('admin.footer-manager.index')
            ->with('success', 'Footer link has been removed successfully.');
    }

    protected function validatedLinkData(Request $request, ?FooterLink $footerLink = null): array
    {
        $request->merge([
            'group' => trim((string) $request->input('group')),
            'label' => trim((string) $request->input('label')),
        ]);

        $validated = $request->validate([
            'group' => ['required', 'string', 'max:100'],
            'label' => [
                'required',
                'string',
                'max:255',
                Rule::unique('footer_links', 'label')
                    ->where(fn ($query) => $query->where('group', $request->input('group')))
                    ->ignore($footerLink?->id),
            ],
            'label_traditional' => ['nullable', 'string', 'max:255'],
            'label_simplified' => ['nullable', 'string', 'max:255'],
            'route_name' => ['nullable', 'required_without:url', 'string', 'max:255'],
            'url' => ['nullable', 'required_without:route_name', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'open_new_tab' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        $validated['group'] = trim($validated['group']);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['open_new_tab'] = $request->boolean('open_new_tab');
        $validated['status'] = $request->boolean('status');

        return $validated;
    }
}
