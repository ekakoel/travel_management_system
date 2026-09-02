<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHomeSliderRequest;
use App\Http\Requests\UpdateHomeSliderRequest;
use App\Models\HomeSlider;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeSliderController extends Controller
{
    private const MANAGE_GATES = [
        'posDev',
        'posAdm',
        'posAuthor',
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'type:admin']);
    }

    public function index()
    {
        $sliders = HomeSlider::query()
            ->orderBy('sort_order')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $sliderStats = [
            'total' => HomeSlider::count(),
            'active' => HomeSlider::where('is_active', true)->count(),
            'inactive' => HomeSlider::where('is_active', false)->count(),
        ];

        return view('backend.operations.home-sliders.index', [
            'sliders' => $sliders,
            'sliderStats' => $sliderStats,
        ]);
    }

    public function store(StoreHomeSliderRequest $request)
    {
        $validated = $request->validated();

        $desktopPath = $request->file('image')
            ->store('home-sliders', 'public');

        $mobilePath = $request->hasFile('mobile_image')
            ? $request->file('mobile_image')->store('home-sliders', 'public')
            : null;

        try {
            DB::transaction(function () use (
                $request,
                $validated,
                $desktopPath,
                $mobilePath
            ) {
                $slider = HomeSlider::create([
                    'title' => $validated['title'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'image' => $desktopPath,
                    'mobile_image' => $mobilePath,
                    'button_text' => $validated['button_text'] ?? null,
                    'button_url' => $validated['button_url'] ?? null,
                    'sort_order' => $validated['sort_order'],
                    'is_active' => $request->boolean('is_active'),
                    'start_at' => $validated['start_at'] ?? null,
                    'end_at' => $validated['end_at'] ?? null,
                ]);

                $this->recordSliderLog(
                    $request,
                    $slider,
                    'Add Home Slider',
                    "Add new Home Slider id : {$slider->id}"
                );
            });

            return redirect()
                ->route('admin.home-sliders.index')
                ->with('success', 'Home slider created successfully.');
        } catch (\Throwable $e) {
            Storage::disk('public')->delete([
                $desktopPath,
                $mobilePath,
            ]);

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Failed to create home slider.');
        }
    }

    public function update(
        UpdateHomeSliderRequest $request,
        $id
    ) {
        $validated = $request->validated();

        $slider = HomeSlider::findOrFail($id);

        $oldDesktopPath = $slider->image;
        $oldMobilePath = $slider->mobile_image;

        $newDesktopPath = $request->hasFile('image')
            ? $request->file('image')->store('home-sliders', 'public')
            : null;

        $newMobilePath = $request->hasFile('mobile_image')
            ? $request->file('mobile_image')->store('home-sliders', 'public')
            : null;

        try {
            DB::transaction(function () use (
                $request,
                $slider,
                $validated,
                $newDesktopPath,
                $newMobilePath
            ) {
                $slider->update([
                    'title' => $validated['title'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'image' => $newDesktopPath ?: $slider->image,
                    'mobile_image' => $newMobilePath ?: $slider->mobile_image,
                    'button_text' => $validated['button_text'] ?? null,
                    'button_url' => $validated['button_url'] ?? null,
                    'sort_order' => $validated['sort_order'],
                    'is_active' => $request->boolean('is_active'),
                    'start_at' => $validated['start_at'] ?? null,
                    'end_at' => $validated['end_at'] ?? null,
                ]);

                $this->recordSliderLog(
                    $request,
                    $slider,
                    'Update Home Slider',
                    "Update Home Slider id : {$slider->id}"
                );
            });

            if ($newDesktopPath && $oldDesktopPath) {
                Storage::disk('public')->delete($oldDesktopPath);
            }

            if ($newMobilePath && $oldMobilePath) {
                Storage::disk('public')->delete($oldMobilePath);
            }

            return redirect()
                ->route('admin.home-sliders.index')
                ->with('success', 'Home slider updated successfully.');
        } catch (\Throwable $e) {
            Storage::disk('public')->delete([
                $newDesktopPath,
                $newMobilePath,
            ]);

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Failed to update home slider.');
        }
    }

    public function destroy(Request $request, $id)
    {
        if (! $request->user()->can('posDev')) {
            return redirect()
                ->route('admin.home-sliders.index')
                ->with('error', __('messages.unauthorized_access'));
        }

        $slider = HomeSlider::findOrFail($id);

        DB::transaction(function () use ($request, $slider) {
            $this->recordSliderLog(
                $request,
                $slider,
                'Delete Home Slider',
                "Delete Home Slider id : {$slider->id}"
            );

            $slider->delete();
        });

        Storage::disk('public')->delete([
            $slider->image,
            $slider->mobile_image,
        ]);

        return redirect()
            ->route('admin.home-sliders.index')
            ->with('success', 'Home slider deleted successfully.');
    }

    private function recordSliderLog(
        Request $request,
        HomeSlider $slider,
        string $action,
        string $note
    ): void {
        UserLog::create([
            'action' => $action,
            'service' => 'Home Slider',
            'subservice' => 'Home Slider',
            'subservice_id' => $slider->id,
            'page' => 'home-sliders',
            'user_id' => $request->user()->id,
            'user_ip' => $request->ip(),
            'note' => $note,
        ]);
    }
}