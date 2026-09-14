@extends('layout.frontend')
@section('page-title', 'Track Order #' . $order->id . ' — 24/7 NHAM')

@section('content')
<div class="container py-4 py-lg-5">

    <!-- BREADCRUMB & TOP ACTIONS -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}" class="text-decoration-none text-muted">My Orders</a></li>
                    <li class="breadcrumb-item active text-success fw-semibold" aria-current="page">Order #{{ $order->id }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <h1 class="h3 fw-bold mb-0 text-dark">Order #{{ $order->id }}</h1>
                <span class="badge rounded-pill px-3 py-2 fw-semibold {{ $order->deliveryStatusBadgeClass() }}">
                    <i class="bi bi-truck me-1"></i>{{ $order->deliveryStatusLabel() }}
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">Placed on {{ $order->created_at->format('M d, Y · h:i A') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Re-Order Shortcut -->
            <form action="{{ route('customer.orders.reorder', $order->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success rounded-pill px-3 py-2 small fw-semibold">
                    <i class="bi bi-arrow-repeat me-1"></i> Re-Order
                </button>
            </form>
            <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Orders
            </a>
            <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-3 py-2 small fw-semibold d-none d-md-inline-block">
                <i class="bi bi-printer me-1"></i> Print Receipt
            </button>
        </div>
    </div>

    <!-- LIVE TRACKING BANNER & 5-STAGE PROGRESS STEPPER -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-4 mb-4 pb-3 border-bottom">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="text-uppercase tracking-wider small fw-bold text-success">
                            <i class="bi bi-geo-alt-fill me-1"></i> Live Shipment Tracking
                        </span>
                    </div>
                    <h2 class="h4 fw-bold text-dark mb-1">
                        @if($order->isDelivered())
                            Package Delivered Successfully!
                        @elseif($order->isCancelled())
                            Order Has Been Cancelled
                        @elseif($order->delivery_status === \App\Models\Order::DELIVERY_OUT_FOR_DELIVERY)
                            Driver is on the way to your door!
                        @elseif($order->delivery_status === \App\Models\Order::DELIVERY_PICKED_UP)
                            Driver has picked up your food from 24/7 NHAM!
                        @elseif($order->delivery_status === \App\Models\Order::DELIVERY_PREPARING || $order->delivery_status === \App\Models\Order::DELIVERY_CONFIRMED)
                            Kitchen is preparing your order!
                        @else
                            Order placed & awaiting store confirmation
                        @endif
                    </h2>
                    <p class="text-muted small mb-0">
                        @if($order->isDelivered())
                            Delivered at {{ $order->delivered_at ? $order->delivered_at->format('h:i A on M d, Y') : 'recently' }}. Enjoy your meal!
                        @elseif($order->isCancelled())
                            Cancelled. Reason: {{ $order->cancellation_reason ?: 'Customer requested cancellation.' }}
                        @elseif($order->estimated_delivery_at)
                            Estimated arrival: <strong class="text-success">{{ $order->estimated_delivery_at->format('h:i A') }}</strong> ({{ $order->estimated_delivery_at->diffForHumans() }})
                        @else
                            Estimated delivery: 30 to 45 minutes.
                        @endif
                    </p>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="d-inline-block bg-light rounded-3 p-3 border text-start">
                        <div class="text-muted small fw-semibold mb-1">Tracking Number</div>
                        <div class="d-flex align-items-center gap-2">
                            <code class="fs-6 fw-bold text-dark font-monospace">{{ $order->tracking_number ?? 'N/A' }}</code>
                            @if($order->tracking_number)
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copyTrackingCode('{{ $order->tracking_number }}')" title="Copy tracking code">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5-STAGE VISUAL STEPPER -->
            @if(!$order->isCancelled())
                @php $currentStep = $order->deliveryStep(); @endphp
                <div class="tracking-stepper-wrapper py-3">
                    <div class="tracking-stepper">
                        <!-- Step 1: Placed -->
                        <div class="stepper-step {{ $currentStep >= 1 ? 'completed' : '' }} {{ $currentStep == 1 ? 'active' : '' }}">
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="bi bi-receipt-cutoff"></i></div>
                            </div>
                            <div class="step-content">
                                <div class="step-title">Placed</div>
                                <div class="step-desc text-muted">{{ $order->created_at->format('h:i A') }}</div>
                            </div>
                        </div>

                        <!-- Step 2: Confirmed / Preparing -->
                        <div class="stepper-step {{ $currentStep >= 2 ? 'completed' : '' }} {{ $currentStep == 2 ? 'active' : '' }}">
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="bi bi-fire"></i></div>
                            </div>
                            <div class="step-content">
                                <div class="step-title">Confirmed</div>
                                <div class="step-desc text-muted">Kitchen preparing</div>
                            </div>
                        </div>

                        <!-- Step 3: Picked Up -->
                        <div class="stepper-step {{ $currentStep >= 3 ? 'completed' : '' }} {{ $currentStep == 3 ? 'active' : '' }}">
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="bi bi-bag-check-fill"></i></div>
                            </div>
                            <div class="step-content">
                                <div class="step-title">Picked Up</div>
                                <div class="step-desc text-muted">Courier collected</div>
                            </div>
                        </div>

                        <!-- Step 4: On the Way -->
                        <div class="stepper-step {{ $currentStep >= 4 ? 'completed' : '' }} {{ $currentStep == 4 ? 'active' : '' }}">
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="bi bi-bicycle"></i></div>
                            </div>
                            <div class="step-content">
                                <div class="step-title">On The Way</div>
                                <div class="step-desc text-muted">En route to door</div>
                            </div>
                        </div>

                        <!-- Step 5: Delivered -->
                        <div class="stepper-step {{ $currentStep >= 5 ? 'completed' : '' }} {{ $currentStep == 5 ? 'active' : '' }}">
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="bi bi-check2-circle"></i></div>
                            </div>
                            <div class="step-content">
                                <div class="step-title">Delivered</div>
                                <div class="step-desc text-muted">{{ $order->delivered_at ? $order->delivered_at->format('h:i A') : 'Completed' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-danger d-flex align-items-center gap-3 rounded-4 p-3 mb-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-3 text-danger"></i>
                    <div>
                        <div class="fw-bold">Order Cancelled</div>
                        <div class="small text-muted">{{ $order->cancellation_reason ?: 'This order has been cancelled.' }}</div>
                    </div>
                </div>
            @endif

            <!-- CANCEL ORDER BUTTON (IF STILL ELIGIBLE) -->
            @if($order->canBeCancelled())
                <div class="mt-3 pt-3 border-top text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        <i class="bi bi-x-circle me-1"></i> Cancel Order
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- LIVE LEAFLET MAP TRACKING (IF NOT CANCELLED) -->
    @if(!$order->isCancelled())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-map-fill text-success me-2"></i>Live Delivery Route & Courier Position
                </h6>
                <span class="badge bg-success-subtle text-success small">
                    <i class="bi bi-broadcast me-1"></i> Live GPS Map
                </span>
            </div>
            <div class="card-body p-0">
                <div id="liveTrackingMap" style="height: 320px; width: 100%;"></div>
            </div>
            <div class="card-footer bg-light-subtle py-2 px-4 d-flex justify-content-between align-items-center small text-muted">
                <div>
                    <i class="bi bi-shop text-success me-1"></i> Store: 24/7 NHAM Hub
                </div>
                <div>
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> Destination: {{ Str::limit($order->delivery_address, 45) }}
                </div>
            </div>
        </div>
    @endif

    <!-- DETAILS ROW -->
    <div class="row g-4">
        <!-- LEFT COLUMN: ORDER ITEMS & CHAT -->
        <div class="col-lg-7">
            <!-- ITEMS CARD -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 fs-6">
                        <i class="bi bi-box-seam text-success me-2"></i>Ordered Items ({{ $order->orderItems->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th class="ps-4">PRODUCT</th>
                                    <th class="text-center">UNIT</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-end pe-4">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="ps-4">
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
                                                <div>
                                                    <div class="fw-bold text-dark small">{{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}</div>
                                                    @if($item->product && $item->product->category)
                                                        <span class="badge bg-light text-muted border" style="font-size: .7rem;">{{ $item->product->category->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center small">${{ number_format($item->amount, 2) }}</td>
                                        <td class="text-center small fw-bold">×{{ $item->quantity }}</td>
                                        <td class="text-end pe-4 small fw-bold text-dark">
                                            ${{ number_format($item->amount * $item->quantity, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light-subtle py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($order->discount_amount > 0)
                                <div class="text-success small">Promo ({{ $order->promo_code }}): -${{ number_format($order->discount_amount, 2) }}</div>
                            @endif
                            <span class="fw-bold text-dark">Total:</span>
                        </div>
                        <span class="h4 fw-bold text-success mb-0">${{ number_format($order->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- IN-APP CHAT WITH DRIVER / SUPPORT -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-chat-dots-fill text-primary me-2"></i>In-App Chat with Driver
                    </h6>
                    <span class="badge bg-light text-muted border">Live Chat</span>
                </div>
                <div class="card-body p-4">
                    <div id="orderChatBox" class="border rounded-3 p-3 bg-light overflow-auto mb-3" style="height: 200px;">
                        <div class="text-center text-muted small py-3">Loading messages...</div>
                    </div>
                    <form id="orderChatForm" onsubmit="sendOrderChatMessage(event)">
                        <div class="input-group">
                            <input type="text" id="orderChatMessageInput" class="form-control rounded-start-3" placeholder="Type a message to your courier..." required autocomplete="off">
                            <button class="btn btn-success px-4" type="submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: DRIVER CARD & DELIVERY DESTINATION -->
        <div class="col-lg-5">
            <!-- DRIVER CARD WITH PRIVACY-MASKED CALL -->
            @if($order->driver)
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-start border-success border-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 52px; height: 52px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="badge bg-success small">Assigned Courier</span>
                                <div class="h6 fw-bold text-dark mb-0">{{ $order->driver->name }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-bicycle me-1"></i>{{ ucfirst($order->driver->vehicle_type ?: 'Motorbike') }} · 
                                    <span class="fw-semibold">{{ $order->driver->license_plate ?: 'NHAM-EXPRESS' }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($order->driver->averageDriverRating(), 1) }}
                                </div>
                            </div>
                        </div>

                        <!-- PHONE CALL BUTTON (MASKED CALL) -->
                        <div class="d-grid gap-2">
                            @php
                                $rawPhone = $order->driver->phone ?: '+85512987654';
                                // Mask for privacy: e.g. +855 12 ••• 654
                                $maskedPhone = strlen($rawPhone) > 7 ? substr($rawPhone, 0, 5) . ' ••• ' . substr($rawPhone, -3) : $rawPhone;
                            @endphp
                            <a href="tel:{{ $rawPhone }}" class="btn btn-outline-success rounded-pill py-2 small fw-semibold">
                                <i class="bi bi-telephone-fill me-1"></i> Call Driver (<span class="font-monospace">{{ $maskedPhone }}</span>)
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-light">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-hourglass-split text-muted fs-3 mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">Assigning Nearest Courier</h6>
                        <p class="small text-muted mb-0">Our dispatch system is matching your order with an available driver.</p>
                    </div>
                </div>
            @endif

            <!-- DELIVERY DESTINATION CARD -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Delivery Destination
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="text-muted small fw-semibold">Address</div>
                        <div class="text-dark small bg-light p-2 rounded-3 border mt-1">
                            {{ $order->delivery_address ?: 'Registered Customer Address' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small fw-semibold">Contact Phone</div>
                        <div class="text-dark small mt-1">
                            <i class="bi bi-telephone text-success me-1"></i>{{ $order->customer_phone ?: 'N/A' }}
                        </div>
                    </div>

                    @if($order->delivery_notes)
                        <div class="mb-3">
                            <div class="text-muted small fw-semibold">Delivery Notes</div>
                            <div class="text-muted small fst-italic bg-light p-2 rounded-3 border mt-1">
                                "{{ $order->delivery_notes }}"
                            </div>
                        </div>
                    @endif

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Payment Method:</span>
                            <span class="fw-bold text-dark">{{ strtoupper($order->payment_method) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Status:</span>
                            <span class="badge bg-success-subtle text-success">Verified</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DISPUTE / COMPLAINT LINK -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        <i class="bi bi-shield-exclamation text-warning me-1"></i> Issue with this order?
                    </div>
                    @if($order->dispute)
                        <span class="badge bg-warning-subtle text-dark border">Dispute: {{ ucfirst($order->dispute->status) }}</span>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#disputeModal">
                            File Complaint
                        </button>
                    @endif
                </div>
            </div>

            <!-- REVIEW CARD (IF DELIVERED) -->
            @if($order->isDelivered())
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" id="reviewSection">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-star-fill text-warning me-2"></i>Rating & Feedback
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @if($order->review)
                            <div class="mb-2">
                                <div class="small fw-semibold text-dark">Food Rating:</div>
                                <div class="text-warning">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i class="bi bi-star{{ $s <= $order->review->food_rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="small fw-semibold text-dark">Courier Rating:</div>
                                <div class="text-warning">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i class="bi bi-star{{ $s <= $order->review->driver_rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($order->review->comment)
                                <div class="text-muted small fst-italic mt-2 p-2 bg-light rounded border">
                                    "{{ $order->review->comment }}"
                                </div>
                            @endif
                        @else
                            <p class="small text-muted mb-3">How was your meal and delivery experience? Let us know!</p>
                            <button type="button" class="btn btn-warning w-100 rounded-pill fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                <i class="bi bi-star me-1"></i> Rate Food & Courier
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- REVIEW MODAL -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark">Rate Your Experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('order.review.store', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Food Quality Rating (1 to 5 Stars)</label>
                        <select name="food_rating" class="form-select rounded-3" required>
                            <option value="5">⭐⭐⭐⭐⭐ — Excellent Food</option>
                            <option value="4">⭐⭐⭐⭐ — Good</option>
                            <option value="3">⭐⭐⭐ — Average</option>
                            <option value="2">⭐⭐ — Poor</option>
                            <option value="1">⭐ — Terrible</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Courier Delivery Rating (1 to 5 Stars)</label>
                        <select name="driver_rating" class="form-select rounded-3" required>
                            <option value="5">⭐⭐⭐⭐⭐ — Fast & Friendly Driver</option>
                            <option value="4">⭐⭐⭐⭐ — Good Service</option>
                            <option value="3">⭐⭐⭐ — Okay</option>
                            <option value="2">⭐⭐ — Slow or Unpolite</option>
                            <option value="1">⭐ — Poor Service</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Comments / Suggestions</label>
                        <textarea name="comment" class="form-control rounded-3" rows="3" placeholder="Tell us more about your experience..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CANCEL ORDER MODAL -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-danger">Cancel Order #{{ $order->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Are you sure you want to cancel this order? Please select a reason:</p>
                    <select name="reason" class="form-select rounded-3 mb-3" required>
                        <option value="Changed my mind">Changed my mind</option>
                        <option value="Ordered by mistake">Ordered by mistake</option>
                        <option value="Delivery time too long">Delivery time too long</option>
                        <option value="Incorrect address entered">Incorrect address entered</option>
                    </select>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Keep Order</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Confirm Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DISPUTE MODAL -->
<div class="modal fade" id="disputeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark">File a Complaint / Dispute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('order.dispute.store', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject</label>
                        <select name="subject" class="form-select rounded-3" required>
                            <option value="Missing Items in Order">Missing Items in Order</option>
                            <option value="Cold or Spilled Food">Cold or Spilled Food</option>
                            <option value="Order Never Arrived">Order Never Arrived</option>
                            <option value="Courier Behavior Issue">Courier Behavior Issue</option>
                            <option value="Incorrect Charge">Incorrect Charge</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Detailed Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Explain the issue clearly..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold text-dark">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.tracking-stepper {
    display: flex;
    justify-content: space-between;
    position: relative;
    min-width: 540px;
}
.tracking-stepper::before {
    content: '';
    position: absolute;
    top: 24px;
    left: 36px;
    right: 36px;
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
.step-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 3px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 1.25rem;
    margin-bottom: 6px;
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
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, .4); }
    70% { box-shadow: 0 0 0 10px rgba(22, 163, 74, 0); }
    100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
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
    font-size: .72rem;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const orderId = {{ $order->id }};
const storeCoords = [11.5564, 104.9282];
const customerCoords = [
    {{ $order->delivery_latitude ?: 11.5684 }},
    {{ $order->delivery_longitude ?: 104.9182 }}
];

let liveMap = null;
let driverMarker = null;

document.addEventListener('DOMContentLoaded', function () {
    initLiveTrackingMap();
    loadOrderChatMessages();
    setInterval(loadOrderChatMessages, 5000); // Poll chat every 5s
});

function initLiveTrackingMap() {
    const mapEl = document.getElementById('liveTrackingMap');
    if (!mapEl) return;

    liveMap = L.map('liveTrackingMap').setView(storeCoords, 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(liveMap);

    // Store Marker (Hub)
    const storeIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });
    L.marker(storeCoords, { icon: storeIcon }).addTo(liveMap).bindPopup('<strong>24/7 NHAM Store Hub</strong>');

    // Customer Destination Marker
    const customerIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });
    L.marker(customerCoords, { icon: customerIcon }).addTo(liveMap).bindPopup('<strong>Your Delivery Destination</strong>');

    // Route line
    const routeLine = L.polyline([storeCoords, customerCoords], { color: '#16a34a', weight: 4, dashArray: '6, 8' }).addTo(liveMap);
    liveMap.fitBounds(routeLine.getBounds(), { padding: [40, 40] });

    // Moving Driver Marker (Bicycle / Courier)
    @php
        $step = $order->deliveryStep();
        // Calculate interpolation factor along route based on stage
        $t = $step == 5 ? 1.0 : ($step == 4 ? 0.65 : ($step == 3 ? 0.25 : 0.05));
    @endphp
    const t = {{ $t }};
    const driverLat = storeCoords[0] + (customerCoords[0] - storeCoords[0]) * t;
    const driverLng = storeCoords[1] + (customerCoords[1] - storeCoords[1]) * t;

    const driverIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    driverMarker = L.marker([driverLat, driverLng], { icon: driverIcon }).addTo(liveMap)
        .bindPopup('<strong>{{ $order->driver ? $order->driver->name : "Courier" }} (Moving)</strong>')
        .openPopup();
}

/* ========================================================
   CHAT FUNCTIONS
======================================================== */
function loadOrderChatMessages() {
    const chatBox = document.getElementById('orderChatBox');
    if (!chatBox) return;

    fetch(`/orders/${orderId}/chat/messages`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.messages) {
                if (data.messages.length === 0) {
                    chatBox.innerHTML = '<div class="text-center text-muted small py-4"><i class="bi bi-chat-text d-block fs-4 mb-1"></i>No messages yet. Send a note to your courier below!</div>';
                    return;
                }

                let html = '';
                const currentUserId = {{ auth()->id() }};

                data.messages.forEach(msg => {
                    const isMe = msg.sender_id === currentUserId;
                    html += `
                        <div class="d-flex mb-2 ${isMe ? 'justify-content-end' : 'justify-content-start'}">
                            <div class="p-2 px-3 rounded-3 small ${isMe ? 'bg-success text-white' : 'bg-white border text-dark'}" style="max-width: 80%;">
                                <div class="fw-bold" style="font-size: .72rem; opacity: .85;">${msg.sender ? msg.sender.name : 'User'}</div>
                                <div>${escapeHtml(msg.message)}</div>
                            </div>
                        </div>
                    `;
                });

                chatBox.innerHTML = html;
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        })
        .catch(console.error);
}

function sendOrderChatMessage(e) {
    e.preventDefault();
    const input = document.getElementById('orderChatMessageInput');
    const msg = input.value.trim();
    if (!msg) return;

    fetch(`/orders/${orderId}/chat/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: msg })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadOrderChatMessages();
        }
    })
    .catch(console.error);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}

function copyTrackingCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Tracking number copied!', showConfirmButton: false, timer: 2000});
    });
}
</script>
@endpush
