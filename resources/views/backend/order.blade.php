@extends('layout.backend')
@section('title', 'ORDERS & SHIPMENT MANAGEMENT')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="m-0 font-weight-bold text-success">
            <i class="bi bi-truck me-2"></i>Orders & Shipment Tracking
        </h5>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-semibold">
            Total Orders: {{ $orders->count() }}
        </span>
    </div>
    
    <div class="card-body p-0">
        <div class="px-3 py-2 border-bottom bg-light bg-opacity-50 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="input-group input-group-sm" style="max-width: 420px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="orderTableFilter" class="form-control border-start-0" placeholder="Filter orders by customer, tracking, address, driver..." autocomplete="off">
            </div>
            <span id="orderTableFilterCount" class="badge bg-secondary-subtle text-secondary border fw-medium px-2 py-1">
                {{ $orders->count() }} orders
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="orderId">
                <thead class="table-light small">
                    <tr>
                        <th class="text-center" width="50">#</th>
                        <th>CUSTOMER</th>
                        <th>TRACKING / ID</th>
                        <th>ORDER ITEMS</th>
                        <th>DELIVERY INFO</th>
                        <th>TOTAL</th>
                        <th class="text-center">PAYMENT</th>
                        <th class="text-center">DELIVERY STATUS</th>
                        <th class="text-center" width="160">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="tbl">
                    @foreach ($orders as $order)
                    <tr>
                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold text-dark">
                                <i class="bi bi-person me-1 text-muted"></i>{{ $order->user ? $order->user->name : 'N/A' }}
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-envelope me-1 text-muted"></i>{{ $order->user ? $order->user->email : 'N/A' }}
                            </div>
                            @if($order->customer_phone)
                                <div class="small text-muted">
                                    <i class="bi bi-telephone me-1 text-muted"></i>{{ $order->customer_phone }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">Order #{{ $order->id }}</div>
                            @if($order->tracking_number)
                                <a href="{{ route('order.track.search', $order->tracking_number) }}" 
                                   target="_blank" 
                                   class="badge bg-light text-dark border text-decoration-none font-monospace" 
                                   title="View live shipment tracker">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $order->tracking_number }}
                                </a>
                            @endif
                            <div class="small text-muted mt-1">
                                {{ $order->created_at->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            <ul class="list-unstyled mb-0" style="max-width: 200px;">
                                @foreach ($order->orderItems as $item)
                                    <li class="small text-truncate mb-1">
                                        <i class="bi bi-box-seam me-1 text-secondary"></i>
                                        <span class="fw-medium text-dark">{{ $item->product ? $item->product->name : 'Item #' . $item->product_id }}</span> 
                                        <span class="badge bg-secondary-subtle text-dark ms-1">×{{ $item->quantity }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @if($order->delivery_address)
                                <div class="small text-muted mb-1 text-truncate" style="max-width: 200px;" title="{{ $order->delivery_address }}">
                                    <i class="bi bi-pin-map text-danger me-1"></i>{{ $order->delivery_address }}
                                </div>
                            @endif
                            
                            <!-- Driver Assignment Form -->
                            <form action="{{ route('admin.orders.assignDriver', $order->id) }}" method="POST" class="d-flex align-items-center gap-1 mt-1">
                                @csrf
                                <select name="driver_id" class="form-select form-select-sm py-0 px-2" style="font-size: .75rem; width: 140px;" onchange="this.form.submit()">
                                    <option value="">-- Assign Driver --</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" {{ $order->driver_id == $driver->id ? 'selected' : '' }}>
                                            {{ $driver->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            @if($order->driver)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1" style="font-size: .7rem;">
                                    <i class="bi bi-person-check me-1"></i>{{ $order->driver->name }}
                                </span>
                            @endif
                        </td>
                        <td class="fw-bold text-success">${{ number_format($order->amount, 2) }}</td>
                        <td class="text-center">
                            @if($order->status == \App\Models\Order::STATUS_APPROVED)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Approved</span>
                            @elseif($order->status == \App\Models\Order::STATUS_REJECTED)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Declined</span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle">Paid</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <!-- Quick Delivery Status Updater -->
                            <form action="{{ route('admin.orders.updateDeliveryStatus', $order->id) }}" method="POST">
                                @csrf
                                <select name="delivery_status" 
                                        class="form-select form-select-sm fw-semibold rounded-pill text-center py-1 {{ $order->deliveryStatusBadgeClass() }}" 
                                        style="font-size: .78rem;" 
                                        onchange="this.form.submit()">
                                    <option value="pending" {{ $order->delivery_status === 'pending' ? 'selected' : '' }}>Order Placed</option>
                                    <option value="preparing" {{ $order->delivery_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                    <option value="out_for_delivery" {{ $order->delivery_status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                    <option value="delivered" {{ $order->delivery_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->delivery_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                            @if($order->delivered_at)
                                <div class="small text-muted mt-1" style="font-size: .7rem;">
                                    {{ $order->delivered_at->format('M d, h:i A') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Confirm / Approve -->
                                <form action="{{ route('admin.approve', $order->id) }}" method="POST" class="d-inline confirm-form">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-outline-success btn-confirm" title="Approve Order">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>

                                <!-- Reject -->
                                <form action="{{ route('admin.reject', $order->id) }}" method="POST" class="d-inline reject-form">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-reject" title="Reject Order">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>

                                <!-- Public Track View Link -->
                                <a href="{{ route('customer.orders.show', $order->id) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="View Tracking Stepper">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-confirm').forEach(button => {
            button.addEventListener('click', function (e) {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Approve Order?',
                    text: "Are you sure you want to approve this order?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Yes, Approve!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        document.querySelectorAll('.btn-reject').forEach(button => {
            button.addEventListener('click', function (e) {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Reject Order?',
                    text: "This action will reject the order and set delivery to cancelled.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-x-lg me-1"></i>Yes, Reject!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
        // Automatic Instant Table Search as soon as characters are typed
        const orderTableFilter = document.getElementById('orderTableFilter');
        const orderTableFilterCount = document.getElementById('orderTableFilterCount');
        const orderRows = document.querySelectorAll('#tbl tr');

        if (orderTableFilter) {
            orderTableFilter.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                let matched = 0;

                orderRows.forEach(row => {
                    if (row.id === 'orderNoMatchRow') return;
                    const text = row.innerText.toLowerCase();
                    const match = !q || text.includes(q);
                    row.style.display = match ? '' : 'none';
                    if (match) matched++;
                });

                if (orderTableFilterCount) {
                    orderTableFilterCount.textContent = q 
                        ? `${matched} of ${orderRows.length} matches` 
                        : `${orderRows.length} orders`;
                }

                let noMatchRow = document.getElementById('orderNoMatchRow');
                if (matched === 0 && q) {
                    if (!noMatchRow) {
                        noMatchRow = document.createElement('tr');
                        noMatchRow.id = 'orderNoMatchRow';
                        noMatchRow.innerHTML = `<td colspan="9" class="text-center py-4 text-muted"><i class="bi bi-search me-1"></i>No orders found matching "${this.value}"</td>`;
                        document.getElementById('tbl').appendChild(noMatchRow);
                    }
                    noMatchRow.style.display = '';
                } else if (noMatchRow) {
                    noMatchRow.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush