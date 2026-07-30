@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-md-6 text-center">
            <div class="card p-5 shadow-sm border" style="background-color: var(--surface-glass); border-color: var(--border-app) !important; border-radius: 16px; backdrop-filter: blur(10px);">
                <div class="mb-4">
                    <i class="bi bi-shield-lock text-danger" style="font-size: 5rem; text-shadow: 0 0 20px rgba(220, 53, 69, 0.2);"></i>
                </div>
                
                <h1 class="font-weight-bold mb-2" style="font-size: 2.5rem; color: var(--text-primary); font-family: 'Outfit', sans-serif;">403</h1>
                <h4 class="mb-3 text-secondary" style="color: var(--text-secondary) !important; font-family: 'Outfit', sans-serif;">Access Forbidden</h4>
                
                <p class="text-muted mb-4" style="font-size: 1.05rem;">
                    {{ $exception->getMessage() ?: 'You do not have permission to access this resource.' }}
                </p>

                <div>
                    @if (auth()->check())
                        <a id="redirect-btn" href="{{ route('home') }}" class="btn btn-primary px-4 py-2 font-weight-bold" style="border-radius: 8px;">
                            <i class="bi bi-speedometer2 me-1"></i> Back to Dashboard
                        </a>
                    @else
                        <a id="redirect-btn" href="{{ url('/') }}" class="btn btn-primary px-4 py-2 font-weight-bold" style="border-radius: 8px;">
                            <i class="bi bi-house-door me-1"></i> Back to Landing Page
                        </a>
                    @endif
                </div>

                <div class="text-muted mt-3" style="font-size: 0.9rem;">
                    Redirecting automatically in <span id="countdown-sec" class="font-weight-bold text-primary">5</span> seconds...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let count = 5;
        const countdownEl = document.getElementById('countdown-sec');
        const redirectBtn = document.getElementById('redirect-btn');
        const redirectUrl = redirectBtn ? redirectBtn.getAttribute('href') : "{{ auth()->check() ? route('home') : url('/') }}";
        
        const timer = setInterval(function () {
            count--;
            if (countdownEl) {
                countdownEl.textContent = count;
            }
            if (count <= 0) {
                clearInterval(timer);
                window.location.href = redirectUrl;
            }
        }, 1000);
    });
</script>
@endsection
