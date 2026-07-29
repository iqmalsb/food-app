@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Organisation Management (Superadmin)</span>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
                </div>

                @if (session()->has('alert-message'))
                    <div class="alert {{ session()->get('alert-type') }} m-3">
                        {{ session()->get('alert-message') }}
                    </div>
                @endif
                
                <div class="card-body">
                    <form action="{{ route('organisations.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" name="keyword" value="{{ request()->get('keyword') }}" placeholder="Search by name or slug...">
                            <button type="submit" class="btn btn-primary">Search</button>
                            @if(request()->get('keyword'))
                                <a href="{{ route('organisations.index') }}" class="btn btn-outline-secondary">Clear</a>
                            @endif
                        </div>
                    </form>
                    <a href="{{ route('organisations.create') }}" class="btn btn-dark mt-3">Add New Organisation</a>
                </div>

                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Seats Limit</th>
                            <th scope="col">Active Users</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                            @forelse ($organisations as $org)
                                <tr>
                                    <th scope="row">{{ $loop->iteration + ($organisations->firstItem() - 1) }}</th>
                                    <td><strong>{{ $org->name }}</strong></td>
                                    <td><code>{{ $org->slug }}</code></td>
                                    <td>{{ $org->seats_limit }}</td>
                                    <td>
                                        @php
                                            $activeUsersCount = \App\Models\User::withoutGlobalScopes()->where('organisation_id', $org->id)->count();
                                        @endphp
                                        <span class="badge {{ $activeUsersCount >= $org->seats_limit ? 'bg-danger' : 'bg-success' }}">
                                            {{ $activeUsersCount }} / {{ $org->seats_limit }}
                                        </span>
                                    </td>
                                    <td>{{ $org->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('organisations.show', $org) }}" class="btn btn-sm btn-info">Details</a>
                                        <a href="{{ route('organisations.delete', $org) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this organisation? All associated users, foods, tables and orders will be deleted permanently.')">Delete</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No organisations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                      </table>
                      
                      <div class="d-flex justify-content-center">
                          {{ $organisations->appends(request()->query())->links() }}
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
