@extends('driver.layout')

@section('title', 'Active Job - 24/7 NHAM Courier')

@section('content')
@if(!$order)
    <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
        <div class="card-body py-5">
            <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                <i class="bi bi-bicycle"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">No Active Delivery Job</h5>
            <p class="text-muted small mb-3">You do not have any ongoing delivery tasks right now.</p>
            <a href="{{ route('driver.dashboard') }}" class="btn btn-success rounded-pill px-4 fw-bold">
                <i class="bi bi-radar me-1"></i> Check Available Orders
            </a>
        </div>
    </div>
@else
    <div class="row g-3">
        
        <!-- Left Column: Delivery Details & Actions -->
        <div class="col-lg-7">
            
            <!-- Order Header Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-4 mb-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                            {{ $order->deliveryStatusLabel() }}
                        </span>
                        <h4 class="fw-bolder text-dark mb-0 mt-2">Order #{{ $order->id }}</h4>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Amount to Collect</div>
                        <div class="fw-bolder {{ $order->isCod() ? 'text-danger' : 'text-success' }} fs-4">
                            @if($order->isCod())
                                ${{ number_format($order->amount, 2) }} (COD)
                            @else
                                Card Paid ($0.00)
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 1-Tap Action Progression Button -->
                <div class="mb-4">
                    @if(in_array($order->delivery_status, ['confirmed', 'preparing', 'pending']))
                        <form action="{{ route('driver.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="picked_up">
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bolder fs-5 shadow d-flex align-items-center justify-content-center gap-2" style="background-color: #2563eb;">
                                <i class="bi bi-bag-check-fill"></i> 1-Tap: Confirm Order Picked Up
                            </button>
                        </form>
                    @elseif($order->delivery_status === 'picked_up')
                        <form action="{{ route('driver.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="out_for_delivery">
                            <button type="submit" class="btn btn-warning w-100 py-3 rounded-4 fw-bolder fs-5 shadow text-dark d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-bicycle"></i> 1-Tap: Start Trip (Courier On The Way)
                            </button>
                        </form>
                    @elseif($order->delivery_status === 'out_for_delivery')
                        <form action="{{ route('driver.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="btn btn-success w-100 py-3 rounded-4 fw-bolder fs-5 shadow d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-check2-circle"></i> 1-Tap: Confirm Delivered to Customer
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Customer Info & Calling -->
                <div class="bg-light rounded-4 p-3 mb-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center text-success fw-bold" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $order->user ? $order->user->name : 'Customer' }}</h6>
                                <div class="text-muted small">
                                    <i class="bi bi-telephone-fill me-1"></i> {{ $order->shipping_phone ?: ($order->user ? $order->user->phone : 'N/A') }}
                                </div>
                            </div>
                        </div>

                        <!-- Direct Call Button -->
                        @php $phoneNum = $order->shipping_phone ?: ($order->user ? $order->user->phone : null); @endphp
                        @if($phoneNum)
                            <a href="tel:{{ $phoneNum }}" class="btn btn-success rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm">
                                <i class="bi bi-telephone-fill"></i> Call Customer
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Drop-off Address & Navigation -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Delivery Destination</span>
                        @php
                            $lat = $order->latitude ?? 11.5564;
                            $lng = $order->longitude ?? 104.9282;
                            $navUrl = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($lat . ',' . $lng);
                        @endphp
                        <a href="{{ $navUrl }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                            <i class="bi bi-compass-fill me-1"></i> Open Google Maps
                        </a>
                    </div>
                    <div class="p-3 bg-light rounded-3">
                        <div class="fw-semibold text-dark mb-1">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $order->shipping_address }}
                        </div>
                        <div class="small text-muted d-flex gap-3 mt-2">
                            <span><i class="bi bi-signpost me-1"></i>{{ $order->distance_km ?? 2.5 }} km</span>
                            <span><i class="bi bi-clock me-1"></i>~{{ $order->eta_minutes ?? 15 }} mins</span>
                        </div>
                    </div>
                </div>

                <!-- Items in Order -->
                <div class="border-top pt-3">
                    <h6 class="fw-bold text-dark mb-2">Order Items:</h6>
                    <ul class="list-group list-group-flush small">
                        @foreach($order->orderItems as $item)
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark">{{ $item->quantity }}x</span>
                                    <span class="text-secondary ms-1">{{ $item->product ? $item->product->name : 'Product' }}</span>
                                </div>
                                <span class="fw-semibold text-dark">${{ number_format($item->amount * $item->quantity, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            <!-- Quick Presets to Customer -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-3">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> 1-Tap Quick Alerts to Customer
                </h6>
                <div class="d-flex flex-wrap gap-2">
                    <form action="{{ route('driver.preset', $order->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="preset" value="5_mins">
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                            🕒 5 mins away
                        </button>
                    </form>
                    <form action="{{ route('driver.preset', $order->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="preset" value="arrived">
                        <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                            📍 I have arrived outside
                        </button>
                    </form>
                    <form action="{{ route('driver.preset', $order->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="preset" value="gate">
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                            🚪 At the front gate
                        </button>
                    </form>
                    <form action="{{ route('driver.preset', $order->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="preset" value="traffic">
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                            🚗 Road traffic delay
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Right Column: Live Map & In-App Chat -->
        <div class="col-lg-5">
            
            <!-- Live Route Map -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                <div class="card-header bg-white py-2 px-3 fw-bold small text-dark d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-map-fill text-success me-1"></i> Live Delivery Route</span>
                    <span class="badge bg-success-subtle text-success">GPS Active</span>
                </div>
                <div id="driverMap" style="height: 250px; width: 100%;"></div>
            </div>

            <!-- In-App Chat with Customer -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white py-2 px-3 fw-bold d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-chat-dots-fill me-1"></i> Chat with {{ $order->user ? $order->user->name : 'Customer' }}</span>
                    <span class="small text-white-50">Order #{{ $order->id }}</span>
                </div>
                <div id="driverChatBox" class="card-body p-3 bg-light" style="height: 280px; overflow-y: auto;">
                    <div id="driverMessagesList" class="d-flex flex-column gap-2"></div>
                </div>
                <div class="card-footer bg-white p-2">
                    <form id="driverChatForm" class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="text" id="driverMessageInput" class="form-control form-control-sm rounded-pill px-3" 
                               placeholder="Type a message to customer..." required autocomplete="off">
                        <button type="submit" class="btn btn-success btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" 
                                style="width: 34px; height: 34px; flex-shrink: 0;">
                            <i class="bi bi-send-fill text-white" style="font-size: 0.8rem;"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Map Setup
        const storeLat = 11.5564;
        const storeLng = 104.9282;
        const destLat  = {{ $order->latitude ?? 11.5621 }};
        const destLng  = {{ $order->longitude ?? 104.9160 }};

        const map = L.map('driverMap').setView([(storeLat + destLat) / 2, (storeLng + destLng) / 2], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Store Marker
        L.marker([storeLat, storeLng]).addTo(map).bindPopup('<b>Store: 24/7 NHAM</b>');
        // Customer Marker
        L.marker([destLat, destLng]).addTo(map).bindPopup('<b>Customer Destination</b>');

        // Route Polyline
        const polyline = L.polyline([[storeLat, storeLng], [destLat, destLng]], { color: '#16a34a', weight: 4, dashArray: '6, 8' }).addTo(map);
        map.fitBounds(polyline.getBounds(), { padding: [30, 30] });

        // Driver Chat logic
        const chatBox = document.getElementById('driverChatBox');
        const messagesList = document.getElementById('driverMessagesList');
        const chatForm = document.getElementById('driverChatForm');
        const msgInput = document.getElementById('driverMessageInput');
        const currentUserId = {{ auth()->id() }};
        let lastCount = 0;

        function fetchDriverChat() {
            fetch('{{ route("chat.order.messages", $order->id) }}')
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        let html = '';
                        if (data.messages.length === 0) {
                            html = '<div class="text-center text-muted small py-4">No messages yet. Send a message or use quick presets above.</div>';
                        } else {
                            data.messages.forEach(m => {
                                const isMe = m.sender_id === currentUserId;
                                const time = new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                                if (isMe) {
                                    html += `
                                        <div class="d-flex justify-content-end">
                                            <div class="bg-success text-white px-3 py-1.5 rounded-3 shadow-sm text-start" style="max-width: 85%; font-size: 0.82rem;">
                                                ${escapeHtml(m.message)}
                                                <div class="text-white-50 text-end" style="font-size: 0.65rem;">${time}</div>
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    html += `
                                        <div class="d-flex justify-content-start">
                                            <div class="bg-white border text-dark px-3 py-1.5 rounded-3 shadow-sm" style="max-width: 85%; font-size: 0.82rem;">
                                                <div class="fw-bold text-success" style="font-size: 0.7rem;">${escapeHtml(m.sender ? m.sender.name : 'Customer')}</div>
                                                ${escapeHtml(m.message)}
                                                <div class="text-muted" style="font-size: 0.65rem;">${time}</div>
                                            </div>
                                        </div>
                                    `;
                                }
                            });
                        }
                        messagesList.innerHTML = html;
                        if (data.messages.length > lastCount) {
                            chatBox.scrollTop = chatBox.scrollHeight;
                            lastCount = data.messages.length;
                        }
                    }
                })
                .catch(err => console.error(err));
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return (text || '').replace(/[&<>"']/g, m => map[m]);
        }

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const txt = msgInput.value.trim();
            if (!txt) return;
            msgInput.value = '';

            fetch('{{ route("chat.order.send", $order->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: txt })
            })
            .then(r => r.json())
            .then(d => { if (d.success) fetchDriverChat(); });
        });

        fetchDriverChat();
        setInterval(fetchDriverChat, 3500);
    });
    </script>
    @endpush
@endif
@endsection

