@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @php
                $org = Auth::user()->organisation;
            @endphp
            @if($org && $org->banner_image)
                <div class="org-banner-container">
                    <img src="{{ asset('/storage/' . $org->banner_image) }}" class="org-banner-img" alt="Organisation Banner">
                </div>
            @endif

            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        <div class="col-md-8 mt-3">
            <div class="card">
                <div class="card-header">Food Management</div>
                <div class="card-body">
                    <a href="{{ route('food.index') }}" type="button" class="btn btn-dark">Manage Food Items</a>
                </div>
            </div>
        </div>

        <div class="col-md-8 mt-3">
            <div class="card">
                <div class="card-header">Category Management</div>
                <div class="card-body">
                    <a href="{{ route('categories.index') }}" type="button" class="btn btn-dark">Manage Categories</a>
                </div>
            </div>
        </div>

        <div class="col-md-8 mt-3">
            <div class="card">
                <div class="card-header">Table Management</div>
                <div class="card-body">
                    <a href="{{ route('tables.index') }}" type="button" class="btn btn-dark">Manage Tables</a>
                </div>
            </div>
        </div>

        @if (in_array(Auth::user()->role, ['superadmin', 'org_admin', 'admin']))
            <div class="col-md-8 mt-3">
                <div class="card" style="border: 1px solid rgba(99, 102, 241, 0.4);">
                    <div class="card-header" style="background-color: rgba(99, 102, 241, 0.05); font-weight: bold;">
                        User Management ({{ Auth::user()->role === 'superadmin' ? 'Superadmin' : 'Organisation Admin' }})
                    </div>
                    <div class="card-body">
                        <a href="{{ route('users.index') }}" type="button" class="btn btn-primary" style="background-color: #6366f1; border-color: #6366f1;">Manage System Users</a>
                    </div>
                </div>
            </div>
        @endif

        @if (Auth::user()->role === 'superadmin')
            <div class="col-md-8 mt-3">
                <div class="card" style="border: 1px solid rgba(16, 185, 129, 0.4);">
                    <div class="card-header" style="background-color: rgba(16, 185, 129, 0.05); font-weight: bold; color: #10b981;">
                        Organisation Management (Superadmin)
                    </div>
                    <div class="card-body">
                        <a href="{{ route('organisations.index') }}" type="button" class="btn btn-success" style="background-color: #10b981; border-color: #10b981;">Manage Tenant Organisations</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
