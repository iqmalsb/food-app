<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $currentUser = auth()->user();

        // If superadmin, they can query across all organisations
        if ($currentUser->role === 'superadmin') {
            $query = User::withoutGlobalScopes();
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            }
            $users = $query->paginate(10);
        } else {
            $query = User::where('organisation_id', $currentUser->organisation_id);
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            }
            $users = $query->paginate(5);
        }

        return view('users.index', compact('users'));
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in($roleOptions)],
            'contact_no' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
        ];

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

        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return to_route('users.index')->with([
            'alert-type' => 'alert-success',
            'alert-message' => 'User created successfully',
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
}
