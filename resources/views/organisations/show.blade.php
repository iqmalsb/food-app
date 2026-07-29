@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Organisation Details / Edit</span>
                    <a href="{{ route('organisations.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('organisations.update', $organisation) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Organisation Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $organisation->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">URL Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $organisation->slug) }}" required>
                            <div class="form-text">Only lowercase letters, numbers, and dashes are allowed. Used in subdomains or paths.</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="seats_limit" class="form-label">Seating/Users Limit</label>
                            <input type="number" class="form-control @error('seats_limit') is-invalid @enderror" id="seats_limit" name="seats_limit" value="{{ old('seats_limit', $organisation->seats_limit) }}" required min="1">
                            <div class="form-text">The maximum number of users (staff/managers) this organisation can register.</div>
                            @error('seats_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Organisation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
