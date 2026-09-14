@extends('layout.frontend')
@section('page-title', 'My Order History — 24/7 NHAM')

@section('content')
<div class="container py-4 py-lg-5">

    <!-- BREADCRUMB & HEADER -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item active text-success fw-semibold" aria-current="page">My Orders</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-0 text-dark">
                <i class="bi bi-bag-check text-success me-2"></i>Order History & Tracking
            </h1>
            <p class="text-muted small mb-0 mt-1">Review your past purchases, re-order your favorites with 1-click, and track live shipments.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('order.track') }}" class="btn btn-outline-success rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-search me-1"></i> Track by Number
            </a>
            <a href="{{ url('/list') }}" class="btn btn-success rounded-pill px-4 py-2 small fw-semibold">
                <i class="bi bi-cart-plus me-1"></i> Continue Shopping
            </a>
        </div>
    </div>

    <!-- STATS SUMMARY CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary-subtle text-primary p-2 fs-5">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Orders</div>
                        <div class="h5 fw-bold text-dark mb-0">{{ $totalCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-warning-subtle text-warning p-2 fs-5">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">In-Transit</div>
                        <div class="h5 fw-bold text-dark mb-0">{{ $activeCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success-subtle text-success p-2 fs-5">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Delivered</div>
                        <div class="h5 fw-bold text-dark mb-0">{{ $deliveredCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-danger-subtle text-danger p-2 fs-5">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Cancelled</div>
                        <div class="h5 fw-bold text-dark mb-0">{{ $cancelledCount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER TABS -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-0 px-3 px-md-4">
            <ul class="nav nav-tabs border-0 gap-2" id="orderFilterTabs">
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-2 fw-semibold {{ $tab === 'all' ? 'active bg-success text-white' : 'text-secondary' }}" 
                       href="{{ route('customer.orders.index', ['tab' => 'all']) }}">
                        All Orders ({{ $totalCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-2 fw-semibold {{ $tab === 'active' ? 'active bg-success text-white' : 'text-secondary' }}" 
                       href="{{ route('customer.orders.index', ['tab' => 'active']) }}">
                        Active Deliveries ({{ $activeCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-2 fw-semibold {{ $tab === 'delivered' ? 'active bg-success text-white' : 'text-secondary' }}" 
                       href="{{ route('customer.orders.index', ['tab' => 'delivered']) }}">
                        Delivered ({{ $deliveredCount }})
                    </a>
                </li>
                @if($cancelledCount > 0)
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-2 fw-semibold {{ $tab === 'cancelled' ? 'active bg-success text-white' : 'text-secondary' }}" 
                       href="{{ route('customer.orders.index', ['tab' => 'cancelled']) }}">
                        Cancelled ({{ $cancelledCount }})
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>

    <!-- ORDERS LIST -->
    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3">
            <div class="py-4">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-box-seam fs-1 text-muted"></i>
                </div>
                <h4 class="fw-bold text-dark">No orders found</h4>
                <p class="text-muted small max-w-sm mx-auto mb-4" style="max-width: 420px;">
                    @if($tab !== 'all')
                        You don't have any orders under the "{{ ucfirst($tab) }}" filter right now.
                    @else
                        You haven't placed any orders yet. Discover our fresh meals, drinks, and snacks!
                    @endif
                </p>
                <a href="{{ url('/list') }}" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-basket me-1"></i> Start Shopping
                </a>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden order-card">
                    <!-- CARD HEADER -->
                    <div class="card-header bg-light-subtle border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <span class="fw-bold text-dark fs-6">Order #{{ $order->id }}</span>
                            <span class="text-muted small">|</span>
                            <span class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('M d, Y · h:i A') }}
                            </span>
                            @if($order->tracking_number)
                                <span class="badge bg-white text-dark border font-monospace px-2 py-1">
                                    <i class="bi bi-upc-scan text-muted me-1"></i>{{ $order->tracking_number }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Delivery Status Badge -->
                            <span class="badge rounded-pill px-3 py-2 fw-semibold {{ $order->deliveryStatusBadgeClass() }}">
                                <i class="bi bi-truck me-1"></i>{{ $order->deliveryStatusLabel() }}
                            </span>
                            <!-- Payment Badge -->
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-semibold">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </div>
                    </div>

                    <!-- CARD BODY -->
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center">
                            <!-- ITEMS LIST -->
                            <div class="col-lg-7">
                                <div class="d-flex flex-column gap-2">
                                    @foreach($order->orderItems as $item)
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ $item->product->image_url }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="rounded-3 object-fit-cover border" 
                                                     style="width: 48px; height: 48px;"
                                                     onerror="this.src='https://placehold.co/48x48?text=Food'">
                                            @else
                                                <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted" 
                                                     style="width: 48px; height: 48px;">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-semibold text-dark text-truncate" style="font-size: .9rem;">
                                                    {{ $item->product ? $item->product->name : 'Item #' . $item->product_id }}
                                                </div>
                                                <div class="text-muted small">
                                                    Qty: <span class="fw-medium text-dark">{{ $item->quantity }}</span> × ${{ number_format($item->amount, 2) }}
                                                </div>
                                            </div>
                                            <div class="fw-bold text-dark text-end" style="font-size: .9rem;">
                                                ${{ number_format($item->amount * $item->quantity, 2) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($order->delivery_address)
                                    <div class="mt-3 pt-2 border-top small text-muted d-flex align-items-center gap-2">
                                        <i class="bi bi-geo-alt text-danger"></i>
                                        <span class="text-truncate"><strong>Deliver to:</strong> {{ $order->delivery_address }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- RIGHT SIDE: TOTAL & ACTIONS -->
                            <div class="col-lg-5 text-lg-end">
                                <div class="mb-3">
                                    <span class="text-muted small d-block">Total Paid</span>
                                    <span class="h4 fw-bold text-success mb-0">${{ number_format($order->amount, 2) }}</span>
                                    @if($order->discount_amount > 0)
                                        <span class="d-block text-muted small">(Saved ${{ number_format($order->discount_amount, 2) }} with {{ $order->promo_code }})</span>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                                    <!-- Re-Order Shortcut -->
                                    <form action="{{ route('customer.orders.reorder', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success rounded-pill px-3 py-2 small fw-semibold" title="Re-order all items from this purchase">
                                            <i class="bi bi-arrow-repeat me-1"></i> Re-Order
                                        </button>
                                    </form>

                                    <!-- Track Live Delivery Button -->
                                    <a href="{{ route('customer.orders.show', $order->id) }}" 
                                       class="btn btn-success rounded-pill px-4 py-2 small fw-semibold shadow-sm">
                                        <i class="bi bi-truck me-1"></i> Track Order
                                    </a>
                                </div>

                                @if($order->review)
                                    <div class="mt-2 text-warning small">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="bi bi-star{{ $s <= $order->review->food_rating ? '-fill' : '' }}"></i>
                                        @endfor
                                        <span class="text-muted ms-1">(Reviewed)</span>
                                    </div>
                                @elseif($order->isDelivered())
                                    <div class="mt-2">
                                        <a href="{{ route('customer.orders.show', $order->id) }}#reviewSection" class="small text-decoration-none text-primary fw-semibold">
                                            <i class="bi bi-star me-1"></i> Leave a Review
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- PAGINATION -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
