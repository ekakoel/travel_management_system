<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use App\Mail\ApprovalUserMail;
use App\Mail\RegistrationUserMail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    private const MANAGER_POSITIONS = [
        'developer' => 'Developer',
        'administrator' => 'Administrator',
        'reservation' => 'Reservation',
        'staff' => 'Staff',
        'agent' => 'Agent',
    ];

    private const MANAGER_STATUSES = [
        'Active' => 'Active',
        'Block' => 'Blocked',
    ];

    private const MANAGER_TYPES = [
        'admin' => 'Admin',
        'user' => 'User',
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'min:2', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:25', 'unique:users'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function index()
    {
        $adminusers = User::where('type', '=', 'admin')->paginate(8);
        $userusers = User::where('type', '=', 'user')->get();
        return view('backend.admin.users.index', compact('adminusers'), [
            "userusers" => User::where('type', '=', "user"),
            "adminusers" => User::where('type', '=', "admin"),
            "adminusers" => $adminusers,
            "userusers" => $userusers,

        ]);
    }
    // VIEW PROFILE =============================================================================================================>
    public function userdetail($id)
    {
        $duser = User::find($id);
        return view('backend.admin.users.show', [
            'dusers' => $duser,
        ]);
    }
    // VIEW PROFILE =============================================================================================================>
    public function new_register()
    {
        $now = Carbon::now();
        $user = User::where('id', 1)->first();
        return view('emails.newUserRegister', [
            'user' => $user,
            'now' => $now,
        ]);
    }
    // FUNCTION UPDATE PROFILE =============================================================================================================>
    public function func_update_profile(Request $request, $id)
    {
        abort_unless((int) $id === (int) Auth::id(), 403);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'job_title' => ['required', 'string', 'max:120'],
            'office' => ['required', 'string', 'max:255'],
            'company_legal_name' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:120'],
            'state_region' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'country' => ['required', 'string', 'max:120'],
            'website' => ['nullable', 'url', 'max:255'],
            'preferred_language' => ['required', Rule::in(['en', 'zh', 'zh-CN'])],
            'timezone' => ['nullable', 'timezone'],
            'company_registration_number' => ['nullable', 'string', 'max:120'],
            'contact_channels' => ['nullable', 'array', 'max:10'],
            'contact_channels.*.platform' => ['nullable', Rule::in(User::supportedContactChannelPlatforms())],
            'contact_channels.*.value' => ['nullable', 'string', 'max:180'],
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ((array) $request->input('contact_channels', []) as $index => $channel) {
                if (!is_array($channel)) {
                    continue;
                }

                $platform = trim((string) ($channel['platform'] ?? ''));
                $value = trim((string) ($channel['value'] ?? ''));

                if ($platform === '' && $value === '') {
                    continue;
                }

                if ($platform === '') {
                    $validator->errors()->add("contact_channels.$index.platform", __('messages.Select a social or chat platform.'));
                }

                if ($value === '') {
                    $validator->errors()->add("contact_channels.$index.value", __('messages.Enter the profile link, username, or phone number.'));
                }
            }
        });

        $validated = $validator->validateWithBag('profileUpdate');
        $contactChannels = User::sanitizeContactChannels($validated['contact_channels'] ?? []);
        unset($validated['contact_channels']);

        $user = User::findOrFail($id);
        $now = Carbon::now();
        $user->update(array_merge($validated, User::syncLegacyContactChannelAttributes($contactChannels)));
        Mail::to(config('app.reservation_mail'))
            ->send(new RegistrationUserMail($id, $now));
        return redirect("/profile")->with('success', __('messages.Profile has been updated successfully.'));
    }
    // FUNCTION VERIFIED USER =============================================================================================================>
    public function func_verified_user(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $ferivied = Carbon::parse($request->verified)->format('Y-m-d H:i:s');
        $user->update([
            "email_verified_at" => $ferivied,
            "status" => "Active",
        ]);
        return redirect()->route('user-manager')->with('success', 'User has been verified');
    }

    // FUNCTION UPDATE PROFILE IMAGE =============================================================================================================>
    public function func_update_profileimg(Request $request, $id)
    {
        abort_unless((int) $id === (int) Auth::id(), 403);

        $request->validateWithBag('profilePhoto', [
            'profileimg' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = User::findOrFail($id);
        if ($request->hasFile("profileimg")) {
            if (File::exists("storage/user/profile/" . $user->profileimg)) {
                File::delete("storage/user/profile/" . $user->profileimg);
            }
            $file = $request->file("profileimg");
            $user->profileimg = time() . "_" . $file->getClientOriginalName();
            $file->move("storage/user/profile/", $user->profileimg);
            $request['profileimg'] = $user->profileimg;
        }
        $user->update([
            "profileimg" => $user->profileimg,
        ]);
        return redirect("/profile")->with('success', __('messages.Profile picture has been updated successfully.'));
    }

    // VIEW USER MANAGER =============================================================================================================>
    public function user_manager(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'position' => ['nullable', Rule::in(array_keys(self::MANAGER_POSITIONS))],
            'status' => ['nullable', Rule::in(array_keys(self::MANAGER_STATUSES))],
            'approval' => ['nullable', Rule::in(['approved', 'pending'])],
        ]);

        $notifications = [];
        if (auth()->check()) {
            $user = auth()->user();
            if (method_exists($user, 'notifications')) {
                $notifications = $user->notifications()->latest()->limit(8)->get();
            }
        }
        $now = Carbon::now();
        $userQuery = User::query()
            ->select([
                'id',
                'name',
                'username',
                'email',
                'type',
                'code',
                'profileimg',
                'position',
                'phone',
                'office',
                'address',
                'country',
                'status',
                'is_approved',
                'approved_at',
                'comment',
                'session_id',
                'email_verified_at',
                'created_at',
                'updated_at',
            ])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('office', 'like', "%{$search}%");
                });
            })
            ->when($filters['position'] ?? null, fn($query, string $position) => $query->where('position', $position))
            ->when($filters['status'] ?? null, fn($query, string $status) => $query->where('status', $status))
            ->when($filters['approval'] ?? null, function ($query, string $approval) {
                $approval === 'approved'
                    ? $query->where('is_approved', true)
                    : $query->where('is_approved', false);
            })
            ->orderBy('name')
            ->orderBy('username')
            ->orderBy('id');

        $users = $userQuery->paginate(15)->withQueryString();
        $summary = [
            'total' => User::count(),
            'active' => User::where('status', 'Active')->count(),
            'blocked' => User::where('status', 'Block')->count(),
            'pendingApproval' => User::where('is_approved', false)->count(),
            'online' => User::whereNotNull('session_id')->where('session_id', '>=', $now->copy()->subMinutes(5))->count(),
        ];

        return view('backend.admin.users.manager', [
            'users' => $users,
            'now' => $now,
            'notifications' => $notifications,
            'summary' => $summary,
            'filters' => $filters,
            'positions' => self::MANAGER_POSITIONS,
            'statuses' => self::MANAGER_STATUSES,
            'types' => self::MANAGER_TYPES,
        ]);
    }

    // FUNCTION EDIT USER =============================================================================================================>
    public function func_edit_user(Request $request, $id)
    {
        $targetUserId = (int) $id;

        if ($request->filled('managed_user_id') && (int) $request->input('managed_user_id') !== $targetUserId) {
            return redirect()->route('user-manager')->with('invalid', 'User update target does not match the submitted form.');
        }

        $validated = $this->validateManagedUser($request, (int) $id);
        $users = User::findOrFail($targetUserId);
        $status = $validated['status'];
        $isApproved = $status === 'Active' ? (bool) $request->boolean('is_approved') : false;

        if ($targetUserId === (int) Auth::id() && ($status !== 'Active' || !$isApproved)) {
            return redirect()->route('user-manager')->with('invalid', 'You cannot deactivate or unapprove your own account.');
        }

        $updates = [
            "type" => $validated['type'],
            "code" => strtoupper((string) ($validated['code'] ?? '')),
            "position" => $validated['position'],
            "name" => $validated['name'],
            "username" => $validated['username'],
            "status" => $status,
            "is_approved" => $isApproved,
            "approved_at" => $isApproved ? ($users->approved_at ?: now()) : null,
            "email" => $validated['email'],
        ];

        foreach (['phone', 'office', 'address', 'country', 'comment'] as $optionalProfileField) {
            if (array_key_exists($optionalProfileField, $validated)) {
                $updates[$optionalProfileField] = $validated[$optionalProfileField];
            }
        }

        $users->update($updates);

        $this->recordUserManagerLog($request, 'Update User', $id, 'Update User: ' . $id);

        return redirect()->route('user-manager')->with('success', 'User has been successfully updated!');
    }
    // FUNCTION APPROVE USER =============================================================================================================>
    public function func_approve_user(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $now = Carbon::now();
        $is_approved = 1;
        $approved_at = $now->format('Y-m-d H:i:s');
        $user->update([
            "is_approved" => $is_approved,
            "approved_at" => $approved_at,
            "status" => "Active",
        ]);

        Mail::to($user->email)
            ->send(new ApprovalUserMail($id, $now));
        $this->recordUserManagerLog($request, 'Approve User', $id, 'Approve User: ' . $id);

        return redirect()->route('user-manager')->with('success', 'User has been approved!');
    }

    // FUNCTION UPDATE PASSWORD =============================================================================================================>
    public function updatePassword(Request $request)
    {
        # Validation
        $request->validateWithBag('profilePassword', [
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        #Match The Old Password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return redirect("/profile")
                ->withErrors(['old_password' => "Old Password Doesn't match!"], 'profilePassword')
                ->withInput();
        }
        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return redirect("/profile")->with("status", "Password changed successfully!");
    }

    // FUNCTION ADD USER =============================================================================================================>
    public function func_create_user(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:2|string|max:255',
            'username' => 'required|string|max:25|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'position' => ['required', Rule::in(array_keys(self::MANAGER_POSITIONS))],
            'type' => ['nullable', Rule::in(array_keys(self::MANAGER_TYPES))],
            'code' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:50'],
            'office' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);
        $now = Carbon::now();
        $password = Hash::make('1234567890');
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $password,
            'type' => $validated['type'] ?? 'user',
            'position' => $validated['position'],
            'status' => 'Active',
            'is_approved' => true,
            'is_subscribed' => true,
            'subscriber' => true,
            'approved_at' => $now,
            'address' => $validated['address'] ?? null,
            'office' => $validated['office'] ?? null,
            'country' => $validated['country'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'email_verified_at' => $now,
            'code' => strtoupper((string) ($validated['code'] ?? '')),
        ]);

        $this->recordUserManagerLog($request, 'Create User', $user->id, 'Create User: ' . $user->id);

        return redirect()->route('user-manager')->with("success", "New User has been added successfully!");
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ((int) $user->id === (int) Auth::id()) {
            return redirect()->route('user-manager')->with('invalid', 'You cannot remove your own account.');
        }

        if ($user->position === 'developer' && User::where('position', 'developer')->where('id', '!=', $user->id)->count() === 0) {
            return redirect()->route('user-manager')->with('invalid', 'At least one developer account must remain active.');
        }

        try {
            $user->delete();
            $this->recordUserManagerLog($request, 'Delete User', $id, 'Delete User: ' . $id);

            return redirect()->route('user-manager')->with('success', 'User has been removed successfully.');
        } catch (QueryException $exception) {
            $user->update([
                'status' => 'Block',
                'is_approved' => false,
                'approved_at' => null,
                'session_id' => null,
            ]);

            $this->recordUserManagerLog($request, 'Block User', $id, 'Blocked user because permanent delete is protected by related records: ' . $id);

            return redirect()->route('user-manager')->with('success', 'User has related records, so the account was blocked instead of permanently deleted.');
        }
    }

    private function validateManagedUser(Request $request, int $userId): array
    {
        return $request->validate([
            'managed_user_id' => ['nullable', 'integer'],
            'type' => ['required', Rule::in(array_keys(self::MANAGER_TYPES))],
            'position' => ['required', Rule::in(array_keys(self::MANAGER_POSITIONS))],
            'status' => ['required', Rule::in(array_keys(self::MANAGER_STATUSES))],
            'code' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'min:2', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:25', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:50'],
            'office' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:500'],
            'is_approved' => ['nullable', 'boolean'],
        ]);
    }

    private function recordUserManagerLog(Request $request, string $action, int $targetUserId, string $note): void
    {
        UserLog::create([
            "action" => $action,
            "service" => "User Manager",
            "subservice" => "User Manager",
            "subservice_id" => $targetUserId,
            "page" => "user-manager",
            "user_id" => Auth::id(),
            "user_ip" => $request->getClientIp(),
            "note" => $note,
        ]);
    }
}
