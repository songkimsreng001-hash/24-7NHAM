@extends('driver.layout')

@section('title', 'Courier Dashboard - 24/7 NHAM')

@section('content')
<div class="row g-3">
    
    <!-- Driver Stats Strip -->
    <div class="col-12">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="text-muted small fw-semibold mb-1">Today's Earnings</div>
                    <div class="fs-4 fw-bolder text-success mb-0">${{ number_format($todayEarnings, 2) }}</div>
                    <div class="text-muted" style="font-size: 0.72rem;">Base $2.50 + 10% fee</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="text-muted small fw-semibold mb-1">Today's Deliveries</div>
                    <div class="fs-4 fw-bolder text-dark mb-0">{{ $todayDeliveries }}</div>
                    <div class="text-muted" style="font-size: 0.72rem;">Completed today</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="text-muted small fw-semibold mb-1">Lifetime Jobs</div>
                    <div class="fs-4 fw-bolder text-primary mb-0">{{ $totalCompleted }}</div>
                    <div class="text-muted" style="font-size: 0.72rem;">All-time delivered</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="text-muted small fw-semibold mb-1">Driver Rating</div>
                    <div class="fs-4 fw-bolder text-warning mb-0">
                        <i class="bi bi-star-fill text-warning"></i> {{ number_format($averageRating, 1) }}
                    </div>
                    <div class="text-muted" style="font-size: 0.72rem;">From customer reviews</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Delivery Banner (if assigned) -->
    @if($activeOrder)
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #15803d 0%, #0f172a 100%);">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <div class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">
                                <i class="bi bi-broadcast me-1"></i> ACTIVE JOB IN PROGRESS
                            </div>
                            <h4 class="fw-bolder mb-1">Order #{{ $activeOrder->id }} — {{ $activeOrder->deliveryStatusLabel() }}</h4>
                            <p class="mb-0 text-white-50 small">
                                <i class="bi bi-geo-alt me-1 text-warning"></i> Destination: {{ $activeOrder->shipping_address }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('driver.active') }}" class="btn btn-warning fw-bold rounded-pill px-4 py-2 shadow">
                                Open Active Job Screen <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Available Orders Feed -->
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-radar me-1 text-success"></i> Available Delivery Requests
                <span class="badge bg-secondary rounded-pill ms-1" style="font-size: 0.75rem;">{{ $availableOrders->count() }}</span>
            </h5>
            @if(!$driver->is_online)
                <span class="text-danger small fw-semibold">
                    <i class="bi bi-exclamation-circle me-1"></i> You are currently Offline
                </span>
            @endif
        </div>

        @if($availableOrders->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                <div class="card-body py-4">
                    <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width: 60px; height: 60px; font-size: 1.8rem;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Orders Waiting for Pick-up</h6>
                    <p class="text-muted small mb-0">Stay online. New customer orders from 24/7 NHAM will appear here automatically.</p>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach($availableOrders as $order)
                    @php 
                        $payout = 2.50 + round($order->amount * 0.10, 2);
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Header: ID + Payout -->
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div>
                                        <span class="badge bg-light text-dark fw-bold border">Order #{{ $order->id }}</span>
                                        <div class="fw-bold text-dark mt-1">{{ $order->user ? $order->user->name : 'Customer' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted" style="font-size: 0.7rem;">Est. Earnings</div>
                                        <div class="fw-bolder text-success fs-5">${{ number_format($payout, 2) }}</div>
                                    </div>
                                </div>

                                <!-- Route / Address -->
                                <div class="bg-light p-2 rounded-3 mb-2 small text-secondary">
                                    <div class="d-flex align-items-start gap-2 mb-1">
                                        <i class="bi bi-shop text-success mt-0.5"></i>
                                        <span class="text-truncate"><strong>Store:</strong> 24/7 NHAM Central Hub</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-geo-alt-fill text-danger mt-0.5"></i>
                                        <span class="text-break"><strong>Drop-off:</strong> {{ $order->shipping_address }}</span>
                                    </div>
                                </div>

                                <!-- Distance & ETA -->
                                <div class="d-flex align-items-center gap-3 small text-muted mb-2">
                                    <span><i class="bi bi-signpost me-1"></i>{{ $order->distance_km ?? 2.5 }} km</span>
                                    <span><i class="bi bi-clock me-1"></i>~{{ $order->eta_minutes ?? 15 }} mins</span>
                                    <span>
                                        @if($order->isCod())
                                            <span class="badge bg-warning text-dark"><i class="bi bi-cash me-1"></i>Collect COD ${{ number_format($order->amount, 2) }}</span>
                                        @else
                                            <span class="badge bg-success"><i class="bi bi-credit-card me-1"></i>Card Paid</span>
                                        @endif
                                    </span>
                                </div>

                                <!-- Items Preview -->
                                <div class="small text-muted border-top pt-2 mb-3">
                                    <span class="fw-semibold text-dark">{{ $order->orderItems->count() }} items:</span>
                                    <span class="text-truncate d-inline-block" style="max-width: 220px;">
                                        {{ $order->orderItems->pluck('product.name')->join(', ') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Accept Button -->
                            <div>
                                @if($driver->is_online)
                                    <form action="{{ route('driver.accept', $order->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="bi bi-check2-circle me-1"></i> Accept Job (${{ number_format($payout, 2) }})
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary w-100 rounded-pill fw-semibold py-2" disabled>
                                        Go Online to Accept
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

