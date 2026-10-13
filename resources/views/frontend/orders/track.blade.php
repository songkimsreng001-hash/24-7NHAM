@extends('layout.frontend')


@section('content')
<div class="container py-4 py-lg-5">

    <!-- SEARCH HERO HEADER -->
    <div class="row justify-content-center text-center mb-4">
        <div class="col-lg-7">
            <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                <i class="bi bi-truck fs-2"></i>
            </div>
            <h1 class="h3 fw-bold text-dark mb-2">Track Your Shipment</h1>
            <p class="text-muted small">Enter your Tracking Number or Order ID to see real-time delivery status and arrival estimate.</p>

            <!-- TRACKING SEARCH FORM -->
            <form action="{{ route('order.track') }}" method="GET" class="mt-4 position-relative" id="trackSearchForm" autocomplete="off">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0 ps-4 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           id="trackSearchInput"
                           name="tracking" 
                           class="form-control border-0 py-3 px-2 font-monospace fs-6" 
                           placeholder="e.g. TRK-20260914-0001 or Order #1" 
                           value="{{ $trackingQuery ?? '' }}" 
                           required 
                           autocomplete="off">
                    <button class="btn btn-success px-4 px-md-5 fw-semibold" type="submit">
                        Track Now
                    </button>
                </div>
                <div id="trackSearchResults" class="navbar-search-results shadow-lg text-start" style="margin-top: 6px; border-radius: 14px;"></div>
            </form>
        </div>
    </div>

    <!-- SEARCH RESULTS SECTION -->
    @if($searched && !$order)
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3">
                    <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-exclamation-triangle fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No shipment found</h5>
                    <p class="text-muted small mb-3">
                        We couldn't find any order matching <code class="fw-bold">{{ $trackingQuery }}</code>.
                    </p>
                    <p class="small text-muted mb-4" style="max-width: 460px; margin: 0 auto;">
                        Please verify your tracking number in your confirmation email or order history. You can also track by your numeric Order ID.
                    </p>
                    @auth
                        <div>
                            <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-success rounded-pill px-4 py-2 small fw-semibold">
                                <i class="bi bi-clock-history me-1"></i> View All My Orders
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    @elseif($order)
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- SHIPMENT RESULT CARD -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light-subtle border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark fs-6">Order #{{ $order->id }}</span>
                            <span class="text-muted small">|</span>
                            <code class="font-monospace text-dark fw-bold">{{ $order->tracking_number }}</code>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 fw-semibold {{ $order->deliveryStatusBadgeClass() }}">
                            <i class="bi bi-truck me-1"></i>{{ $order->deliveryStatusLabel() }}
                        </span>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <!-- PROGRESS STEPPER -->
                        @if(!$order->isCancelled())
                            @php $step = $order->deliveryStep(); @endphp
                            <div class="tracking-stepper-wrapper mb-5">
                                <div class="tracking-stepper">
                                    <div class="stepper-step {{ $step >= 1 ? 'completed' : '' }} {{ $step == 1 ? 'active' : '' }}">
                                        <div class="step-icon-wrap">
                                            <div class="step-icon"><i class="bi bi-receipt-cutoff"></i></div>
                                        </div>
                                        <div class="step-title">Order Placed</div>
                                        <div class="step-desc text-muted">{{ $order->created_at->format('M d, h:i A') }}</div>
                                    </div>

                                    <div class="stepper-step {{ $step >= 2 ? 'completed' : '' }} {{ $step == 2 ? 'active' : '' }}">
                                        <div class="step-icon-wrap">
                                            <div class="step-icon"><i class="bi bi-fire"></i></div>
                                        </div>
                                        <div class="step-title">Preparing</div>
                                        <div class="step-desc text-muted">Store kitchen</div>
                                    </div>

                                    <div class="stepper-step {{ $step >= 3 ? 'completed' : '' }} {{ $step == 3 ? 'active' : '' }}">
                                        <div class="step-icon-wrap">
                                            <div class="step-icon"><i class="bi bi-bicycle"></i></div>
                                        </div>
                                        <div class="step-title">On The Way</div>
                                        <div class="step-desc text-muted">
                                            {{ $order->driver ? $order->driver->name : 'Courier dispatched' }}
                                        </div>
                                    </div>

                                    <div class="stepper-step {{ $step >= 4 ? 'completed' : '' }} {{ $step == 4 ? 'active' : '' }}">
                                        <div class="step-icon-wrap">
                                            <div class="step-icon"><i class="bi bi-check2-circle"></i></div>
                                        </div>
                                        <div class="step-title">Delivered</div>
                                        <div class="step-desc text-muted">
                                            {{ $order->delivered_at ? $order->delivered_at->format('h:i A') : 'Completed' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger rounded-4 mb-4">
                                <i class="bi bi-x-circle me-1"></i> This order was cancelled.
                            </div>
                        @endif

                        <!-- SUMMARY DETAILS -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-3 border h-100">
                                    <div class="small fw-bold text-muted text-uppercase mb-2">Delivery Details</div>
                                    <div class="small mb-1"><strong>Destination:</strong> {{ $order->delivery_address ?: 'Customer Address' }}</div>
                                    @if($order->estimated_delivery_at && !$order->isDelivered())
                                        <div class="small text-success">
                                            <i class="bi bi-clock me-1"></i><strong>Estimated:</strong> {{ $order->estimated_delivery_at->format('h:i A') }} ({{ $order->estimated_delivery_at->diffForHumans() }})
                                        </div>
                                    @endif
                                    @if($order->driver)
                                        <div class="small mt-2 pt-2 border-top">
                                            <strong>Courier:</strong> {{ $order->driver->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-3 border h-100">
                                    <div class="small fw-bold text-muted text-uppercase mb-2">Order Summary</div>
                                    <div class="small mb-1"><strong>Items:</strong> {{ $order->orderItems->count() }} item(s)</div>
                                    <div class="small mb-1"><strong>Total Amount:</strong> <span class="text-success fw-bold">${{ number_format($order->amount, 2) }}</span></div>
                                    <div class="small"><strong>Status:</strong> {{ $order->deliveryStatusLabel() }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTONS -->
                        @auth
                            <div class="mt-4 text-center">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-success rounded-pill px-4 py-2 small fw-semibold">
                                    <i class="bi bi-receipt me-1"></i> View Full Order Details
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.tracking-stepper-wrapper {
    overflow-x: auto;
}
.tracking-stepper {
    display: flex;
    justify-content: space-between;
    position: relative;
    min-width: 480px;
}
.tracking-stepper::before {
    content: '';
    position: absolute;
    top: 24px;
    left: 40px;
    right: 40px;
    height: 4px;
    background: #e2e8f0;
    z-index: 1;
}
.stepper-step {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}
.step-icon-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}
.step-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 3px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 1.25rem;
    transition: all .3s ease;
}
.stepper-step.completed .step-icon {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
}
.stepper-step.active .step-icon {
    background: #16a34a;
    border-color: #bbf7d0;
    color: #ffffff;
    box-shadow: 0 0 0 6px rgba(22, 163, 74, .2);
}
.step-title {
    font-size: .85rem;
    font-weight: 700;
    color: #1e293b;
}
.stepper-step:not(.completed):not(.active) .step-title {
    color: #94a3b8;
}
.step-desc {
    font-size: .75rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('trackSearchInput');
    const results = document.getElementById('trackSearchResults');
    if (!input || !results) return;

    let timer = null;

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(timer);

        if (!q) {
            results.innerHTML = '';
            results.classList.remove('show');
            return;
        }

        results.innerHTML = `
            <div class="py-3 text-center text-muted small">
                <i class="bi bi-arrow-repeat me-1 spin"></i> Searching orders...
            </div>`;
        results.classList.add('show');

        timer = setTimeout(function () {
            fetch(`{{ route('order.track') }}?tracking=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(items => {
                results.innerHTML = '';
                if (!items.length) {
                    results.innerHTML = `
                        <div class="py-3 text-center text-muted small">
                            <i class="bi bi-search d-block mb-1"></i> No matching orders found
                        </div>`;
                    results.classList.add('show');
                    return;
                }

                items.forEach(o => {
                    const row = document.createElement('a');
                    row.href = o.url;
                    row.className = 'navbar-search-result text-decoration-none d-flex align-items-center gap-3 p-3';
                    row.innerHTML = `
                        <div class="navbar-search-result-icon bg-success-subtle text-success">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark font-monospace">${escapeHtml(o.tracking_number)}</div>
                            <div class="small text-muted">${escapeHtml(o.status)} • $${escapeHtml(o.amount)} • ${escapeHtml(o.date)}</div>
                        </div>
                        <span class="badge bg-success text-white">Track</span>
                    `;
                    results.appendChild(row);
                });
                results.classList.add('show');
            })
            .catch(() => {
                results.classList.remove('show');
            });
        }, 120);
    });

    input.addEventListener('focus', function () {
        if (this.value.trim().length >= 1 && results.innerHTML.trim()) {
            results.classList.add('show');
        }
    });

    document.addEventListener('click', function (e) {
        if (!document.getElementById('trackSearchForm')?.contains(e.target)) {
            results.classList.remove('show');
        }
    });
});
</script>
@endpush

