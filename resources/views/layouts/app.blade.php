<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DineFlow') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @php
        $themeMode = 'dark';
        $themeColor = 'indigo';
        
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'superadmin') {
                $themeMode = $user->theme_mode ?? 'dark';
                $themeColor = $user->theme_color ?? 'indigo';
            } else {
                $activeOrgId = $user->organisation_id ?? session('current_organisation_id');
                $activeOrg = $activeOrgId ? \App\Models\Organisation::find($activeOrgId) : null;
                if ($activeOrg) {
                    $themeMode = $activeOrg->theme_mode;
                    $themeColor = $activeOrg->theme_color;
                }
            }
        }

        // Tonal palettes mapping themeColor + themeMode to Material You dynamic tokens
        $palettes = [
            'indigo' => [
                'light' => [
                    'primary' => '#6366f1',
                    'primary_glow' => 'rgba(99, 102, 241, 0.12)',
                    'bg_app' => '#f6f7ff',
                    'surface_app' => '#ffffff',
                    'surface_glass' => 'rgba(255, 255, 255, 0.85)',
                    'border_app' => '#e0e2f9',
                    'text_primary' => '#1b1b2f',
                    'text_secondary' => '#555670',
                    'rgb' => '99, 102, 241',
                ],
                'dark' => [
                    'primary' => '#818cf8',
                    'primary_glow' => 'rgba(129, 140, 248, 0.15)',
                    'bg_app' => '#0d0e15',
                    'surface_app' => '#151622',
                    'surface_glass' => 'rgba(21, 22, 34, 0.8)',
                    'border_app' => '#28293d',
                    'text_primary' => '#f1f3fe',
                    'text_secondary' => '#a3a8cc',
                    'rgb' => '129, 140, 248',
                ]
            ],
            'emerald' => [
                'light' => [
                    'primary' => '#10b981',
                    'primary_glow' => 'rgba(16, 185, 129, 0.12)',
                    'bg_app' => '#f4faf7',
                    'surface_app' => '#ffffff',
                    'surface_glass' => 'rgba(255, 255, 255, 0.85)',
                    'border_app' => '#def0e8',
                    'text_primary' => '#0c1f17',
                    'text_secondary' => '#40584d',
                    'rgb' => '16, 185, 129',
                ],
                'dark' => [
                    'primary' => '#34d399',
                    'primary_glow' => 'rgba(52, 211, 153, 0.15)',
                    'bg_app' => '#080c0a',
                    'surface_app' => '#0f1713',
                    'surface_glass' => 'rgba(15, 23, 19, 0.8)',
                    'border_app' => '#1e2f27',
                    'text_primary' => '#ecfdf5',
                    'text_secondary' => '#9bf2d2',
                    'rgb' => '52, 211, 153',
                ]
            ],
            'blue' => [
                'light' => [
                    'primary' => '#3b82f6',
                    'primary_glow' => 'rgba(59, 130, 246, 0.12)',
                    'bg_app' => '#f4f8ff',
                    'surface_app' => '#ffffff',
                    'surface_glass' => 'rgba(255, 255, 255, 0.85)',
                    'border_app' => '#dce7f9',
                    'text_primary' => '#0e1827',
                    'text_secondary' => '#42526e',
                    'rgb' => '59, 130, 246',
                ],
                'dark' => [
                    'primary' => '#60a5fa',
                    'primary_glow' => 'rgba(96, 165, 250, 0.15)',
                    'bg_app' => '#080b11',
                    'surface_app' => '#0f1520',
                    'surface_glass' => 'rgba(15, 21, 32, 0.8)',
                    'border_app' => '#1d283d',
                    'text_primary' => '#eff6ff',
                    'text_secondary' => '#a3c1d9',
                    'rgb' => '96, 165, 250',
                ]
            ],
            'rose' => [
                'light' => [
                    'primary' => '#f43f5e',
                    'primary_glow' => 'rgba(244, 63, 94, 0.12)',
                    'bg_app' => '#fff5f6',
                    'surface_app' => '#ffffff',
                    'surface_glass' => 'rgba(255, 255, 255, 0.85)',
                    'border_app' => '#fce4e7',
                    'text_primary' => '#260b0f',
                    'text_secondary' => '#6e4449',
                    'rgb' => '244, 63, 94',
                ],
                'dark' => [
                    'primary' => '#fb7185',
                    'primary_glow' => 'rgba(251, 113, 133, 0.15)',
                    'bg_app' => '#12080a',
                    'surface_app' => '#1c0d10',
                    'surface_glass' => 'rgba(28, 13, 16, 0.8)',
                    'border_app' => '#381920',
                    'text_primary' => '#fff1f2',
                    'text_secondary' => '#e5b4bc',
                    'rgb' => '251, 113, 133',
                ]
            ],
            'orange' => [
                'light' => [
                    'primary' => '#f97316',
                    'primary_glow' => 'rgba(249, 115, 22, 0.12)',
                    'bg_app' => '#fff8f4',
                    'surface_app' => '#ffffff',
                    'surface_glass' => 'rgba(255, 255, 255, 0.85)',
                    'border_app' => '#fce8dc',
                    'text_primary' => '#271305',
                    'text_secondary' => '#6c4d37',
                    'rgb' => '249, 115, 22',
                ],
                'dark' => [
                    'primary' => '#fb923c',
                    'primary_glow' => 'rgba(251, 146, 60, 0.15)',
                    'bg_app' => '#130a05',
                    'surface_app' => '#1e1008',
                    'surface_glass' => 'rgba(30, 16, 8, 0.8)',
                    'border_app' => '#3d2110',
                    'text_primary' => '#fffaf0',
                    'text_secondary' => '#e5bda1',
                    'rgb' => '251, 146, 60',
                ]
            ],
        ];

        // Fetch active theme settings
        $selectedPalette = $palettes[$themeColor][$themeMode] ?? $palettes['indigo'][$themeMode];
        $colorRgb = $selectedPalette['rgb'];
    @endphp
    
    <style>
        :root {
            --color-primary: {{ $selectedPalette['primary'] }} !important;
            --color-primary-glow: {{ $selectedPalette['primary_glow'] }} !important;
            
            --bg-app: {{ $selectedPalette['bg_app'] }} !important;
            --surface-app: {{ $selectedPalette['surface_app'] }} !important;
            --surface-glass: {{ $selectedPalette['surface_glass'] }} !important;
            --border-app: {{ $selectedPalette['border_app'] }} !important;
            --text-primary: {{ $selectedPalette['text_primary'] }} !important;
            --text-secondary: {{ $selectedPalette['text_secondary'] }} !important;
            --sidebar-active-bg: {{ $selectedPalette['primary_glow'] }} !important;
        }
    </style>
</head>

<body class="theme-{{ $themeMode }}">
    @if($themeMode === 'dark')
        <!-- Background Glowing Mesh -->
        <div class="bg-glow-container" aria-hidden="true">
            <div class="bg-glow-1" style="background: radial-gradient(circle, rgba({{ $colorRgb }}, 0.08) 0%, rgba({{ $colorRgb }}, 0) 70%);"></div>
            <div class="bg-glow-2"></div>
        </div>
    @endif
    <div id="app">
        @auth
            @if(session()->has('original_superadmin_id'))
                <div class="alert alert-warning text-center rounded-0 mb-0 py-2 border-0" style="background-color: #fef3c7; color: #92400e; font-size: 0.95rem; font-weight: bold; border-bottom: 1px solid #f59e0b !important; position: relative; z-index: 1050;">
                    <i class="bi bi-person-fill-exclamation me-1"></i> 
                    Simulating user: <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}@if(Auth::user()->organisation) - {{ Auth::user()->organisation->name }}@endif)
                    <form action="{{ route('users.stop-impersonation') }}" method="POST" class="d-inline ms-3">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning text-dark border-dark py-0 px-2" style="font-size: 0.8rem; font-weight: bold;">
                            Exit Simulation
                        </button>
                    </form>
                </div>
            @endif
            <div id="wrapper">
                <!-- Sidebar -->
                <div id="sidebar-wrapper">
                    <div class="sidebar-heading">DINEFLOW</div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('home') }}" class="list-group-item list-group-item-action {{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a href="{{ route('food.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('food.*') ? 'active' : '' }}">
                            <i class="bi bi-egg-fried"></i> Food Items
                        </a>
                        <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i> Categories
                        </a>
                        <a href="{{ route('tables.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                            <i class="bi bi-grid-3x3-gap"></i> Table Layout
                        </a>
                        @if (in_array(Auth::user()->role, ['superadmin', 'org_admin', 'admin']))
                            <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="bi bi-people"></i> Users
                            </a>
                            <a href="{{ route('organisation.settings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organisation.settings') ? 'active' : '' }}">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        @endif
                        @if (Auth::user()->role === 'superadmin')
                            <a href="{{ route('organisations.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organisations.*') ? 'active' : '' }}">
                                <i class="bi bi-building"></i> Organisations
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Page Content Wrapper -->
                <div id="page-content-wrapper">
                    <!-- Top Navbar -->
                    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm py-2">
                        <div class="container-fluid">
                            <button class="menu-toggle-btn me-3" id="menu-toggle">
                                <i class="bi bi-justify"></i>
                            </button>
                            <span class="navbar-brand d-none d-md-inline-block">{{ config('app.name', 'DineFlow') }}</span>

                            <div class="ms-auto d-flex align-items-center">
                                @if(Auth::user()->role === 'superadmin')
                                    <div class="me-3 d-inline-block d-flex align-items-center gap-2">
                                        @php
                                            $selectedOrgId = session('current_organisation_id');
                                            $selectedOrg = $selectedOrgId ? \App\Models\Organisation::find($selectedOrgId) : null;
                                        @endphp
                                        @if($selectedOrg && $selectedOrg->banner_image)
                                            <img src="{{ asset('/storage/' . $selectedOrg->banner_image) }}" alt="Banner" class="rounded" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid var(--border-app);">
                                        @endif
                                        <form id="switch-org-form" action="{{ route('organisations.switch') }}" method="POST" class="d-flex align-items-center m-0">
                                            @csrf
                                            <label for="active_org_select" class="me-2 text-secondary d-none d-sm-inline" style="font-size: 0.9rem; white-space: nowrap;">
                                                <i class="bi bi-building text-primary"></i> Active Tenant:
                                            </label>
                                            <select name="organisation_id" id="active_org_select" onchange="document.getElementById('switch-org-form').submit()" class="form-select form-select-sm border-primary" style="max-width: 200px;">
                                                <option value="">-- All (Global View) --</option>
                                                @foreach(\App\Models\Organisation::all() as $org)
                                                    <option value="{{ $org->id }}" {{ session('current_organisation_id') == $org->id ? 'selected' : '' }}>
                                                        {{ $org->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                @else
                                    <span class="me-3 text-secondary d-none d-sm-inline d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                        @if(Auth::user()->organisation)
                                            @if(Auth::user()->organisation->banner_image)
                                                <img src="{{ asset('/storage/' . Auth::user()->organisation->banner_image) }}" alt="Banner" class="rounded" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid var(--border-app);">
                                            @endif
                                            <i class="bi bi-building"></i> {{ Auth::user()->organisation->name }}
                                        @else
                                            <i class="bi bi-shield-lock"></i> Global Admin
                                        @endif
                                    </span>
                                @endif

                                <div class="dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle font-weight-bold" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <main class="py-4 px-3">
                        @yield('content')
                    </main>
                </div>
            </div>
        @else
            <!-- Guest Layout (Login, Register, Welcome) -->
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'DineFlow') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto">
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>
        @endauth
    </div>

    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menu-toggle');
            const wrapper = document.getElementById('sidebar-wrapper');

            // Apply persisted sidebar state
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                wrapper.classList.add('collapsed');
            }

            menuToggle.addEventListener('click', function (e) {
                e.preventDefault();
                wrapper.classList.toggle('collapsed');
                // Persist state
                const isCollapsed = wrapper.classList.contains('collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed);
            });
        });
    </script>
    @endauth

    <!-- Floating dynamic toast alerts -->
    @if (session()->has('alert-message'))
        <div class="floating-toast-container">
            @php
                $alertType = session()->get('alert-type', 'alert-success');
                $toastClass = 'toast-success';
                $toastIcon = 'bi-check-circle-fill';
                
                if (str_contains($alertType, 'danger')) {
                    $toastClass = 'toast-danger';
                    $toastIcon = 'bi-exclamation-octagon-fill';
                } elseif (str_contains($alertType, 'warning')) {
                    $toastClass = 'toast-warning';
                    $toastIcon = 'bi-exclamation-triangle-fill';
                } elseif (str_contains($alertType, 'info')) {
                    $toastClass = 'toast-info';
                    $toastIcon = 'bi-info-circle-fill';
                }
            @endphp
            
            <div id="global-app-toast" class="floating-toast {{ $toastClass }}">
                <div class="floating-toast-content">
                    <i class="bi {{ $toastIcon }}" style="font-size: 1.25rem;"></i>
                    <span>{{ session()->get('alert-message') }}</span>
                </div>
                <button class="floating-toast-close" onclick="closeToast()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

        <script>
            function closeToast() {
                const toast = document.getElementById('global-app-toast');
                if (toast) {
                    toast.classList.add('fade-out');
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.parentElement.remove();
                        }
                    }, 500);
                }
            }
            
            // Auto close after 10 seconds (10000ms)
            setTimeout(() => {
                closeToast();
            }, 10000);
        </script>
    @endif
</body>

</html>
