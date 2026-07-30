@extends('layouts.app')

@section('content')
@php
    $currentColor = auth()->user()->role === 'superadmin' ? auth()->user()->theme_color : ($organisation ? $organisation->theme_color : 'indigo');
    $currentMode = auth()->user()->role === 'superadmin' ? auth()->user()->theme_mode : ($organisation ? $organisation->theme_mode : 'dark');
@endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ auth()->user()->role === 'superadmin' ? 'Settings' : 'Organisation Settings' }}</span>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
                </div>

                @if (session()->has('alert-message'))
                    <div class="alert {{ session()->get('alert-type') }} m-3">
                        {{ session()->get('alert-message') }}
                    </div>
                @endif

                <div class="card-body">
                    <form action="{{ route('organisation.update-settings') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if(auth()->user()->role !== 'superadmin')
                            <!-- Current Banner Preview -->
                            <div class="mb-4">
                                <label class="form-label">Current Organisation Banner</label>
                                @if($organisation && $organisation->banner_image)
                                    <div class="org-banner-container">
                                        <img src="{{ asset('/storage/' . $organisation->banner_image) }}" class="org-banner-img" alt="Current banner image">
                                    </div>
                                @else
                                    <div class="p-4 text-center rounded border bg-light text-muted" style="border-style: dashed !important;">
                                        <i class="bi bi-image" style="font-size: 2rem;"></i>
                                        <p class="mt-1 mb-0">No banner uploaded yet. Default theme color will be used.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Banner Image Upload -->
                            <div class="mb-4">
                                <label for="banner_image" class="form-label">Upload New Banner</label>
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror" id="banner_image" name="banner_image" accept="image/*">
                                <div class="form-text">Recommended dimension is 1200x300 pixels (Max size 2MB).</div>
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Brand/Theme Color Choice -->
                        <div class="mb-4">
                            <label class="form-label">{{ auth()->user()->role === 'superadmin' ? 'Theme Color' : 'Default Theme Color' }}</label>
                            <div class="d-flex gap-3 mt-1">
                                @foreach(['indigo' => '#6366f1', 'emerald' => '#10b981', 'blue' => '#3b82f6', 'rose' => '#f43f5e', 'orange' => '#f97316'] as $color => $hex)
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input d-none" type="radio" name="theme_color" id="color_{{ $color }}" value="{{ $color }}" {{ old('theme_color', $currentColor) === $color ? 'checked' : '' }}>
                                        <label class="d-flex align-items-center justify-content-center rounded-circle" for="color_{{ $color }}" style="width: 38px; height: 38px; background-color: {{ $hex }}; cursor: pointer; border: 3px solid {{ old('theme_color', $currentColor) === $color ? 'var(--text-primary)' : 'transparent' }}; box-shadow: 0 0 5px rgba(0,0,0,0.1); transition: all 0.2s;" onclick="selectColor(this)">
                                            @if(old('theme_color', $currentColor) === $color)
                                                <i class="bi bi-check-lg text-white" style="font-size: 1.2rem; -webkit-text-stroke: 1px;"></i>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('theme_color')
                                <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Light/Dark Mode Preference -->
                        <div class="mb-4">
                            <label for="theme_mode" class="form-label">Theme Mode Preference</label>
                            <select class="form-select @error('theme_mode') is-invalid @enderror" id="theme_mode" name="theme_mode" required>
                                <option value="dark" {{ old('theme_mode', $currentMode) === 'dark' ? 'selected' : '' }}>🌙 Dark Mode (Default / Landing Page Scheme)</option>
                                <option value="light" {{ old('theme_mode', $currentMode) === 'light' ? 'selected' : '' }}>☀️ Light Mode</option>
                            </select>
                            <div class="form-text">
                                {{ auth()->user()->role === 'superadmin' ? 'Choose your theme mode preference.' : 'Choose the default color palette mode for all users under your organisation.' }}
                            </div>
                            @error('theme_mode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function selectColor(element) {
        // Clear all check marks and borders
        document.querySelectorAll('[id^="color_"]').forEach(radio => {
            const label = radio.nextElementSibling;
            label.style.borderColor = 'transparent';
            const icon = label.querySelector('i');
            if (icon) icon.remove();
        });

        // Set border for clicked element and add check icon
        element.style.borderColor = 'var(--text-primary)';
        const checkIcon = document.createElement('i');
        checkIcon.className = 'bi bi-check-lg text-white';
        checkIcon.style.fontSize = '1.2rem';
        checkIcon.style.webkitTextStroke = '1px';
        element.appendChild(checkIcon);
    }
</script>
@endsection
