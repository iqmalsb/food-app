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
}
