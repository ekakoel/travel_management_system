<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Models\Activities;
use App\Models\Partners;
use App\Models\Transports;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PartnersController extends Controller
{
    private const MANAGE_GATES = ['posDev', 'posAdm', 'posAuthor'];

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'type:admin']);
    }

    public function index()
    {
        $partners = Partners::query()
            ->notRemoved()
            ->withCount([
                'activity as activities_count' => fn ($query) => $query->where('status', '!=', 'Removed'),
                'transports as transports_count' => fn ($query) => $query->where('status', '!=', 'Removed'),
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $partnerStats = [
            'total' => Partners::query()->notRemoved()->count(),
            'active' => Partners::query()->where('status', Partners::STATUS_ACTIVE)->count(),
            'draft' => Partners::query()->where('status', Partners::STATUS_DRAFT)->count(),
            'transport' => Partners::query()
                ->notRemoved()
                ->where(function ($query) {
                    $query->where('type', 'Transport')
                        ->orWhere('type', 'Activity & Transport');
                })
                ->count(),
        ];

        return view('backend.operations.partners.index', [
            'partners' => $partners,
            'partnerStats' => $partnerStats,
        ]);
    }

    public function store(StorePartnerRequest $request)
    {
        $validated = $request->validated();
        $coverPath = $request->file('cover')->store('partners/covers', 'public');

        try {
            DB::transaction(function () use ($request, $validated, $coverPath) {
                $partner = Partners::create([
                    'name' => $validated['name'],
                    'address' => $validated['address'],
                    'location' => $validated['location'],
                    'map' => $validated['map'],
                    'type' => $validated['type'],
                    'phone' => $validated['phone'],
                    'contact_person' => $validated['contact_person'],
                    'status' => Partners::STATUS_DRAFT,
                    'cover' => $coverPath,
                    'author_id' => $request->user()->id,
                    'description' => $validated['description'] ?? null,
                ]);

                $this->recordPartnerLog($request, $partner, 'Add Partner', "Add new Partner id : {$partner->id}");
            });

            return redirect()
                ->route('admin.partners.index')
                ->with('success', 'Partner created successfully.');
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($coverPath);
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Failed to create partner.');
        }
    }

    public function update(UpdatePartnerRequest $request, $id)
    {
        $validated = $request->validated();
        $partner = Partners::findOrFail($id);
        $oldCoverPath = $partner->coverStoragePath();
        $newCoverPath = $request->hasFile('cover')
            ? $request->file('cover')->store('partners/covers', 'public')
            : null;

        try {
            DB::transaction(function () use ($request, $partner, $validated, $newCoverPath) {
                $status = $validated['status'] ?? $partner->status ?? Partners::STATUS_DRAFT;

                if ($status === Partners::STATUS_DRAFT) {
                    Activities::where('partners_id', $partner->id)->update(['status' => Partners::STATUS_DRAFT]);
                    Transports::where('partner_id', $partner->id)->update(['status' => Partners::STATUS_DRAFT]);
                }

                $partner->update([
                    'status' => $status,
                    'name' => $validated['name'],
                    'address' => $validated['address'],
                    'location' => $validated['location'],
                    'map' => $validated['map'],
                    'type' => $validated['type'],
                    'phone' => $validated['phone'],
                    'contact_person' => $validated['contact_person'],
                    'cover' => $newCoverPath ?: $partner->cover,
                    'description' => $validated['description'] ?? null,
                ]);

                $this->recordPartnerLog($request, $partner, 'Update Partner', "Update Partner id : {$partner->id}");
            });

            if ($newCoverPath && $oldCoverPath && $oldCoverPath !== $newCoverPath) {
                Storage::disk('public')->delete($oldCoverPath);
            }

            return redirect()
                ->route('admin.partners.index')
                ->with('success', 'Partner updated successfully.');
        } catch (\Throwable $e) {
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Failed to update partner.');
        }
    }

    public function destroy(Request $request, $id)
    {
        if (Gate::allows('posDev')) {
            $partner = Partners::findOrFail($id);

            DB::transaction(function () use ($request, $partner) {
                Activities::where('partners_id', $partner->id)->update([
                    'status' => Partners::STATUS_DRAFT,
                    'partners_id' => null,
                ]);

                Transports::where('partner_id', $partner->id)->update([
                    'status' => Partners::STATUS_DRAFT,
                    'partner_id' => null,
                ]);

                $partner->update(['status' => Partners::STATUS_REMOVED]);

                $this->recordPartnerLog($request, $partner, 'Archive Partner', "Archive Partner id : {$partner->id}");
            });

            return redirect()
                ->route('admin.partners.index')
                ->with('success', 'Partner archived successfully.');
        }else{
            return redirect()
                ->route('admin.partners.index')
                ->with('error', __('messages.unauthorized_access'));
        }
    }

    private function recordPartnerLog(Request $request, Partners $partner, string $action, string $note): void
    {
        UserLog::create([
            'action' => $action,
            'service' => 'Partner',
            'subservice' => 'Partner',
            'subservice_id' => $partner->id,
            'page' => 'partners',
            'user_id' => $request->user()->id,
            'user_ip' => $request->ip(),
            'note' => $note,
        ]);
    }
}
