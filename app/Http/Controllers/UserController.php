<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Mail;
use App\Mail\SupportInquiryMail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin'])->except([
            'stopImpersonation',
            'changePasswordForm',
            'changePassword',
            'submitSupportInquiry'
        ]);
    }

    public function index()
    {
        return view('users.index');
    }

    public function create()
    {
        $currentUser = auth()->user();
        $organisations = [];

        if ($currentUser->role === 'superadmin') {
            $organisations = Organisation::all();
        }

        return view('users.create', compact('organisations'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();

        $roleOptions = ['org_admin', 'manager', 'staff'];
        if ($currentUser->role === 'superadmin') {
            $roleOptions[] = 'superadmin';
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', Rule::in($roleOptions)],
            'contact_no' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
        ];

        // Conditional password validation rules
        $generatePassword = $request->boolean('generate_password');
        if (!$generatePassword) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        // If superadmin, they must assign an organisation unless creating another superadmin
        if ($currentUser->role === 'superadmin') {
            $rules['organisation_id'] = [
                Rule::requiredIf($request->role !== 'superadmin'),
                'nullable',
                'exists:organisations,id',
            ];
        }

        $validated = $request->validate($rules);

        // Determine the target organisation ID
        $orgId = null;
        if ($currentUser->role === 'superadmin') {
            $orgId = $request->role === 'superadmin' ? null : $validated['organisation_id'];
        } else {
            $orgId = $currentUser->organisation_id;
            $validated['organisation_id'] = $orgId;
        }

        // Seat limit check
        if ($orgId) {
            $organisation = Organisation::findOrFail($orgId);
            $activeSeats = User::withoutGlobalScopes()->where('organisation_id', $orgId)->count();
            if ($activeSeats >= $organisation->seats_limit) {
                throw ValidationException::withMessages([
                    'role' => "This organisation has reached its seat limit of {$organisation->seats_limit} users.",
                ]);
            }
        }

        $rawPassword = null;
        if ($generatePassword) {
            $rawPassword = \Illuminate\Support\Str::random(12);
            $validated['password'] = bcrypt($rawPassword);
            $validated['must_change_password'] = true;
        } else {
            $validated['password'] = bcrypt($validated['password']);
            $validated['must_change_password'] = $request->boolean('force_password_change');
        }

        $user = User::create($validated);

        // Fire standard Laravel Registered event to trigger email verification notification via Mailpit
        event(new \Illuminate\Auth\Events\Registered($user));

        $msg = 'User created successfully.';
        if ($generatePassword) {
            $msg .= " Generated password: <strong>{$rawPassword}</strong>. Click 'Copy Password' below to copy and dismiss.";
            session()->flash('alert-copyable-password', $rawPassword);
        }

        return to_route('users.index')->with([
            'alert-type' => 'alert-success',
            'alert-message' => $msg,
        ]);
    }

    public function show(User $user)
    {
        $currentUser = auth()->user();

        // Prevent non-superadmins from accessing users from other organisations
        if ($currentUser->role !== 'superadmin' && $user->organisation_id !== $currentUser->organisation_id) {
            abort(404, 'User not found.');
        }

        $organisations = [];
        if ($currentUser->role === 'superadmin') {
            $organisations = Organisation::all();
        }

        return view('users.show', compact('user', 'organisations'));
    }

    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // Prevent non-superadmins from accessing users from other organisations
        if ($currentUser->role !== 'superadmin' && $user->organisation_id !== $currentUser->organisation_id) {
            abort(404, 'User not found.');
        }

        $roleOptions = ['org_admin', 'manager', 'staff'];
        if ($currentUser->role === 'superadmin') {
            $roleOptions[] = 'superadmin';
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in($roleOptions)],
            'contact_no' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
        ];

        if ($currentUser->role === 'superadmin') {
            $rules['organisation_id'] = [
                Rule::requiredIf($request->role !== 'superadmin'),
                'nullable',
                'exists:organisations,id',
            ];
        }

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        // Determine target organisation
        $orgId = null;
        if ($currentUser->role === 'superadmin') {
            $orgId = $request->role === 'superadmin' ? null : $validated['organisation_id'];
        } else {
            $orgId = $currentUser->organisation_id;
            $validated['organisation_id'] = $orgId;
        }

        // Seat limit check if switching to/setting a new organisation
        if ($orgId && $user->organisation_id !== $orgId) {
            $organisation = Organisation::findOrFail($orgId);
            $activeSeats = User::withoutGlobalScopes()->where('organisation_id', $orgId)->count();
            if ($activeSeats >= $organisation->seats_limit) {
                throw ValidationException::withMessages([
                    'role' => "This organisation has reached its seat limit of {$organisation->seats_limit} users.",
                ]);
            }
        }

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return to_route('users.index')->with([
            'alert-type' => 'alert-success',
            'alert-message' => 'User updated successfully',
        ]);
    }

    public function delete(User $user)
    {
        $currentUser = auth()->user();

        // Prevent non-superadmins from deleting users from other organisations
        if ($currentUser->role !== 'superadmin' && $user->organisation_id !== $currentUser->organisation_id) {
            abort(404, 'User not found.');
        }

        // Don't allow users to delete their own account
        if ($currentUser->id === $user->id) {
            return to_route('users.index')->with([
                'alert-type' => 'alert-danger',
                'alert-message' => 'You cannot delete your own account',
            ]);
        }

        $user->delete();

        return to_route('users.index')->with([
            'alert-type' => 'alert-danger',
            'alert-message' => 'User deleted successfully',
        ]);
    }

    public function impersonate(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        if ($user->id === $currentUser->id || $user->role === 'superadmin') {
            return back()->with([
                'alert-type' => 'alert-danger',
                'alert-message' => 'Cannot simulate yourself or another superadmin',
            ]);
        }

        // Store the original superadmin ID
        session(['original_superadmin_id' => $currentUser->id]);

        // Login as the target user
        auth()->login($user);

        return to_route('home')->with([
            'alert-type' => 'alert-success',
            'alert-message' => "Logged in as {$user->name} ({$user->role})",
        ]);
    }

    public function stopImpersonation()
    {
        $originalSuperadminId = session('original_superadmin_id');

        if (!$originalSuperadminId) {
            abort(403, 'No simulation session active.');
        }

        $superadmin = User::findOrFail($originalSuperadminId);

        // Login back as superadmin
        auth()->login($superadmin);

        // Forget the original session ID
        session()->forget('original_superadmin_id');

        return to_route('users.index')->with([
            'alert-type' => 'alert-success',
            'alert-message' => 'Simulation ended. Returned to Superadmin context.',
        ]);
    }

    public function changePasswordForm()
    {
        return view('auth.change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = bcrypt($request->password);
        $user->must_change_password = false;
        $user->save();

        return to_route('home')->with([
            'alert-type' => 'alert-success',
            'alert-message' => 'Password updated successfully. Welcome to DineFlow!',
        ]);
    }

    public function submitSupportInquiry(Request $request)
    {
        $request->validate([
            'inquiry_type' => ['required', 'string', 'in:upgrade_package,bug_report,enhancement,general_idea,other'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $user = auth()->user();

        // Retrieve all superadmins
        $superadmins = User::withoutGlobalScopes()->where('role', 'superadmin')->get();

        if ($superadmins->isEmpty()) {
            return redirect()->back()->with([
                'alert-type' => 'alert-danger',
                'alert-message' => 'No support administrators found. Please try again later.',
            ]);
        }

        foreach ($superadmins as $superadmin) {
            Mail::to($superadmin->email)->send(
                new SupportInquiryMail($user, $request->inquiry_type, $request->subject, $request->message)
            );
        }

        return redirect()->back()->with([
            'alert-type' => 'alert-success',
            'alert-message' => 'Your inquiry has been submitted successfully to support!',
        ]);
    }
}
