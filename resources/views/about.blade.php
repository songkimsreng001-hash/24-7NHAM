@extends('layout.frontend')
@section('page-title', 'About Us — 24/7 NHAM')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="{{ asset('img/logo.png') }}" alt="24/7 NHAM" style="height: 64px; width: auto; object-fit: contain; background: #fff; padding: 4px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,.08); border: 1px solid #e2e8f0;">
                        <div>
                            <div class="text-success fw-bold text-uppercase small" style="letter-spacing: 1px;">24/7 NHAM Delivery</div>
                            <h1 class="h3 fw-bold mb-0 text-dark">About Our Delivery Platform</h1>
                        </div>
                    </div>
                    
                    <p class="lead text-secondary mb-4">
                        Welcome to <strong>24/7 NHAM</strong>, your friendly and reliable on-demand delivery partner in Phnom Penh. We connect you with freshly prepared dishes, cool beverages, ice cream, beer, and daily essentials delivered directly to your doorstep 24 hours a day, 7 days a week.
                    </p>

                    <hr class="my-4 text-muted opacity-25">

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <div class="d-flex align-items-center gap-2 mb-2 text-success">
                                    <i class="bi bi-geo-alt-fill fs-4"></i>
                                    <h5 class="fw-bold mb-0 text-dark">Smart Location &amp; Pin-Drop</h5>
                                </div>
                                <p class="small text-secondary mb-0">
                                    Our interactive Leaflet map allows you to drop a pin precisely at your home, office, or condominium. We automatically calculate accurate distance and delivery ETA from our central hub.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <div class="d-flex align-items-center gap-2 mb-2 text-success">
                                    <i class="bi bi-truck fs-4"></i>
                                    <h5 class="fw-bold mb-0 text-dark">Real-Time 5-Stage Tracking</h5>
                                </div>
                                <p class="small text-secondary mb-0">
                                    Track your order every step of the journey: <em>Placed &rarr; Confirmed &rarr; Picked Up &rarr; On the Way &rarr; Delivered</em>, complete with moving courier map visualization and live arrival alerts.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <div class="d-flex align-items-center gap-2 mb-2 text-success">
                                    <i class="bi bi-shield-lock-fill fs-4"></i>
                                    <h5 class="fw-bold mb-0 text-dark">Secure COD &amp; Stripe Payments</h5>
                                </div>
                                <p class="small text-secondary mb-0">
                                    Pay conveniently via <strong>Cash on Delivery (COD)</strong> when your package arrives, or securely pay upfront with credit/debit card powered by Stripe Payment Gateway.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <div class="d-flex align-items-center gap-2 mb-2 text-success">
                                    <i class="bi bi-chat-dots-fill fs-4"></i>
                                    <h5 class="fw-bold mb-0 text-dark">Direct Courier Chat &amp; Privacy</h5>
                                </div>
                                <p class="small text-secondary mb-0">
                                    Communicate directly with your assigned driver through in-app live chat and masked phone calls to protect your personal privacy while ensuring seamless handoff.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 pt-2">
                        <a href="{{ url('/list') }}" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi bi-grid me-1"></i> Browse Products
                        </a>
                        <a href="{{ route('order.track') }}" class="btn btn-outline-success rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi bi-truck me-1"></i> Track Any Order
                        </a>
                        <a href="{{ url('/contact') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi bi-envelope me-1"></i> Contact Support
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
