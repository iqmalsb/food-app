@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <span>Change Password Required</span>
                </div>

                @if (auth()->check() && auth()->user()->must_change_password)
                    <div class="alert alert-warning m-3">
                        Please change your temporary password before proceeding.
                    </div>
                @elseif (session()->has('system-message'))
                    <div class="alert {{ session()->get('system-type') }} m-3">
                        {!! session()->get('system-message') !!}
                    </div>
                @endif

                <div class="card-body">
                    <p class="text-secondary mb-4">
                        For security reasons, you are required to change your password on your first login before you can access the application dashboard.
                    </p>

                    <form action="{{ route('password.change') }}" method="POST">
                        @csrf

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password" placeholder="Enter a secure new password">
                            <div class="form-text">Must be at least 8 characters long.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter the new password">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
