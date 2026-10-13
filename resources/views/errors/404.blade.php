@extends('layout.frontend')
@section('content')
<div class="container py-5">
    <div class="text-center py-5">
        <div class="display-1 fw-bold text-success">404</div>
        <h1 class="h3 fw-bold mt-3">Page not found</h1>
        <p class="text-secondary">The page you are looking for does not exist or has moved.</p>
        <a href="{{ url('/') }}" class="btn btn-success rounded-3 px-4">
            <i class="bi bi-house-door me-1"></i> Back Home
        </a>
    </div>
</div>
@endsection
