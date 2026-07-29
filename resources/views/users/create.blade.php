@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Create User</span>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">System Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="" disabled selected>Select a role...</option>
                                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="org_admin" {{ old('role') === 'org_admin' ? 'selected' : '' }}>Organisation Admin</option>
                                @if (auth()->user()->role === 'superadmin')
                                    <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                @endif
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if (auth()->user()->role === 'superadmin')
                            <div class="mb-3" id="organisation-select-group">
                                <label for="organisation_id" class="form-label">Organisation</label>
                                <select class="form-select @error('organisation_id') is-invalid @enderror" id="organisation_id" name="organisation_id">
                                    <option value="" selected>None (Global / Superadmin only)</option>
                                    @foreach ($organisations as $org)
                                        <option value="{{ $org->id }}" {{ old('organisation_id') == $org->id ? 'selected' : '' }}>
                                            {{ $org->name }} (Seats: {{ $org->seats_limit }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Required if user is not a Superadmin.</div>
                                @error('organisation_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="position" class="form-label">Job Position (Optional)</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact_no" class="form-label">Contact Number (Optional)</label>
                            <input type="text" class="form-control @error('contact_no') is-invalid @enderror" id="contact_no" name="contact_no" value="{{ old('contact_no') }}">
                            @error('contact_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'superadmin')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const orgGroup = document.getElementById('organisation-select-group');
        const orgSelect = document.getElementById('organisation_id');

        function toggleOrgSelect() {
            if (roleSelect.value === 'superadmin') {
                orgSelect.value = '';
                orgSelect.removeAttribute('required');
                orgGroup.style.opacity = '0.5';
            } else {
                orgSelect.setAttribute('required', 'required');
                orgGroup.style.opacity = '1';
            }
        }

        roleSelect.addEventListener('change', toggleOrgSelect);
        toggleOrgSelect();
    });
</script>
@endif
@endsection
