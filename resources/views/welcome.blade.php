@extends('layout.frontend')


@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <div class="mb-4">
                    <img src="{{ asset('img/logo.png') }}" alt="24/7 NHAM" style="height: 120px; width: auto; object-fit: contain; filter: drop-shadow(0 8px 20px rgba(0,0,0,0.08));">
                </div>
                <h1 class="display-6 fw-bold mb-3 text-dark">Welcome to 24/7 NHAM</h1>
                <p class="lead text-secondary mb-4">
                    Phnom Penh's round-the-clock delivery platform. Delicious food, cold refreshments, and daily essentials delivered to your doorstep in minutes.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                    <a href="{{ url('/') }}" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-cart-check me-2"></i>Order Now
                    </a>
                    <a href="{{ url('/list') }}" class="btn btn-outline-success rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-grid me-2"></i>Explore Menu
                    </a>
                    <a href="{{ route('order.track') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-truck me-2"></i>Track Order
                    </a>
                </div>

                <div class="row g-3 pt-4 border-top text-start">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-success fs-4"></i>
                            <div>
                                <div class="fw-bold small text-dark">24/7 Service</div>
                                <div class="text-muted" style="font-size: .75rem;">Fast delivery any hour</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt text-success fs-4"></i>
                            <div>
                                <div class="fw-bold small text-dark">Pin-Drop Map</div>
                                <div class="text-muted" style="font-size: .75rem;">Live distance &amp; ETA</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success fs-4"></i>
                            <div>
                                <div class="fw-bold small text-dark">COD &amp; Card</div>
                                <div class="text-muted" style="font-size: .75rem;">Cash or Stripe card</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
