@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>User Management ({{ auth()->user()->role === 'superadmin' ? 'Superadmin' : 'Organisation Admin' }})</span>
                    @if (auth()->user()->role === 'org_admin' && auth()->user()->organisation)
                        @php
                            $org = auth()->user()->organisation;
                            $activeSeats = \App\Models\User::where('organisation_id', $org->id)->count();
                            $remainingSeats = $org->seats_limit - $activeSeats;
                        @endphp
                        <span class="badge {{ $remainingSeats <= 0 ? 'bg-danger' : 'bg-success' }} px-3 py-2" style="font-size: 0.85rem;">
                            <i class="bi bi-person-fill-lock"></i> {{ $remainingSeats }} / {{ $org->seats_limit }} Seats Remaining
                        </span>
                    @endif
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
                </div>

                @if (session()->has('alert-message'))
                    <div class="alert {{ session()->get('alert-type') }} m-3">
                        {{ session()->get('alert-message') }}
                    </div>
                @endif
                
                <div class="card-body">
                    <form action="{{ route('users.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" name="keyword" value="{{ request()->get('keyword') }}" placeholder="Search by name or email...">
                            <button type="submit" class="btn btn-primary">Search</button>
                            @if(request()->get('keyword'))
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Clear</a>
                            @endif
                        </div>
                    </form>
                    <a href="{{ route('users.create') }}" class="btn btn-dark mt-3">Add New User</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                @if(auth()->user()->role === 'superadmin')
                                    <th scope="col">Organisation</th>
                                @endif
                                <th scope="col">Position</th>
                                <th scope="col">Created At</th>
                                <th scope="col">Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration + ($users->firstItem() - 1) }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge {{ $user->role === 'superadmin' ? 'bg-dark' : ($user->role === 'org_admin' ? 'bg-danger' : ($user->role === 'manager' ? 'bg-warning text-dark' : 'bg-success')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->role === 'superadmin')
                                            <td>
                                                @if($user->organisation)
                                                    <span class="badge bg-secondary">{{ $user->organisation->name }}</span>
                                                @else
                                                    <span class="badge bg-dark">Global System</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>{{ $user->position ?: '-' }}</td>
                                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">Details</a>
                                            @if(auth()->id() !== $user->id)
                                                <a href="{{ route('users.delete', $user) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role === 'superadmin' ? 8 : 7 }}" class="text-center">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
