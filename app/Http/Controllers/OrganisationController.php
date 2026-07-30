<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganisationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', function ($request, $next) {
            if ($request->route() && in_array($request->route()->getName(), ['organisation.settings', 'organisation.update-settings'])) {
                if (in_array(auth()->user()->role, ['superadmin', 'org_admin', 'admin'])) {
                    return $next($request);
                }
            }
            if (auth()->user()->role === 'superadmin') {
                return $next($request);
            }
            abort(403, 'Unauthorized action.');
        }]);
    }

    public function index(Request $request)
    {
        $keyword = $request->keyword;
        if ($keyword) {
            $organisations = Organisation::query()
                ->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                ->paginate(10);
        } else {
            $organisations = Organisation::paginate(10);
        }

        return view('organisations.index', compact('organisations'));
    }

    public function create()
    {
        return view('organisations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:organisations,slug', 'alpha_dash'],
            'seats_limit' => ['required', 'integer', 'min:1'],
        ]);

        Organisation::create($validated);

        return to_route('organisations.index')->with([
            'alert-type' => 'alert-success',
            'alert-message' => 'Organisation created successfully',
        ]);
    }

    public function show(Organisation $organisation)
    {
        return view('organisations.show', compact('organisation'));
    }

    public function update(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('organisations', 'slug')->ignore($organisation->id), 'alpha_dash'],
            'seats_limit' => ['required', 'integer', 'min:1'],
        ]);

        $organisation->update($validated);

        return to_route('organisations.index')->with([
            'alert-type' => 'alert-success',
            'alert-message' => 'Organisation updated successfully',
        ]);
    }

    public function delete(Organisation $organisation)
    {
        $organisation->delete();

        return to_route('organisations.index')->with([
            'alert-type' => 'alert-danger',
            'alert-message' => 'Organisation deleted successfully',
        ]);
    }

    public function settings()
    {
        $orgId = auth()->user()->organisation_id ?? session('current_organisation_id');
        if (!$orgId) {
            $organisation = Organisation::first();
            if (!$organisation) {
                abort(404, 'No organisation found.');
            }
        } else {
            $organisation = Organisation::findOrFail($orgId);
        }

        return view('organisations.settings', compact('organisation'));
    }

    public function updateSettings(Request $request)
    {
        $orgId = auth()->user()->organisation_id ?? session('current_organisation_id');
        if (!$orgId) {
            $organisation = Organisation::first();
            if (!$organisation) {
                abort(404, 'No organisation found.');
            }
        } else {
            $organisation = Organisation::findOrFail($orgId);
        }

        $validated = $request->validate([
            'theme_color' => ['required', 'string', 'in:indigo,emerald,blue,rose,orange'],
            'theme_mode' => ['required', 'string', 'in:light,dark'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('banners', 'public');
            $organisation->banner_image = $path;
        }

        $organisation->theme_color = $validated['theme_color'];
        $organisation->theme_mode = $validated['theme_mode'];
        $organisation->save();

        return back()->with([
            'alert-type' => 'alert-success',
            'alert-message' => 'Organisation settings updated successfully',
        ]);
    }
}
