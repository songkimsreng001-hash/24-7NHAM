@extends('layout.backend')

@section('page-title', 'Active Deliveries Dashboard - 24/7 NHAM Admin')

@section('content')
<div class="page-header">
    <div>
        <h1>Active Delivery Operations</h1>
        <p>Live tracking and management of all orders currently undergoing dispatch, pick-up, and courier delivery.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.order') }}" class="btn-outline-green">
            <i class="bi bi-list-ul me-1"></i> All Orders History
        </a>
    </div>
</div>

<!-- Active Orders Grid / Table -->
<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">
            <i class="bi bi-broadcast text-success me-1"></i> In-Progress Deliveries ({{ $activeOrders->count() }})
        </h3>
        <span class="panel-badge">Live Orders</span>
    </div>

    @if($activeOrders->isEmpty())
        <div class="empty-state py-5 text-center">
            <i class="bi bi-check-all text-success fs-1 mb-2 d-block"></i>
            <h6 class="fw-bold text-dark">No Active Deliveries</h6>
            <p class="text-muted small">All placed orders have either been completed or cancelled.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light text-muted small">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Delivery Destination</th>
                        <th>Status</th>
                        <th>Assigned Courier</th>
                        <th>Payment / Total</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeOrders as $order)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">#{{ $order->id }}</span>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $order->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $order->user ? $order->user->name : 'Guest' }}</div>
                                <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $order->shipping_phone ?: ($order->user ? $order->user->phone : 'N/A') }}</div>
                            </td>
                            <td>
                                <div class="text-truncate text-secondary" style="max-width: 240px;" title="{{ $order->shipping_address }}">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $order->shipping_address }}
                                </div>
                                <div class="text-muted small">{{ $order->distance_km ?? 2.5 }} km (~{{ $order->eta_minutes ?? 15 }} mins)</div>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($order->delivery_status === 'out_for_delivery') bg-warning text-dark 
                                    @elseif($order->delivery_status === 'picked_up') bg-primary 
                                    @else bg-info text-dark @endif">
                                    {{ $order->deliveryStatusLabel() }}
                                </span>
                            </td>
                            <td>
                                <!-- Courier Reassignment Form -->
                                <form action="{{ route('admin.orders.reassign', $order->id) }}" method="POST" class="d-flex align-items-center gap-1 m-0">
                                    @csrf
                                    <select name="driver_id" class="form-select form-select-sm" style="max-width: 150px; font-size: 0.8rem;" onchange="this.form.submit()">
                                        <option value="">-- Assign Driver --</option>
                                        @foreach($drivers as $drv)
                                            <option value="{{ $drv->id }}" {{ $order->driver_id === $drv->id ? 'selected' : '' }}>
                                                {{ $drv->name }} ({{ $drv->is_online ? 'Online' : 'Offline' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                                @if($order->driver)
                                    <div class="text-success small mt-1">
                                        <i class="bi bi-person-check-fill me-1"></i>{{ $order->driver->name }}
                                    </div>
                                @else
                                    <div class="text-danger small mt-1">Unassigned</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">${{ number_format($order->amount, 2) }}</div>
                                <span class="badge {{ $order->isCod() ? 'bg-warning text-dark' : 'bg-success' }} small">
                                    {{ strtoupper($order->payment_method ?? 'COD') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('customer.orders.show', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" title="View Customer Tracking Screen">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <!-- Cancel Order Trigger Modal -->
                                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal{{ $order->id }}" title="Cancel Order">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>

                                <!-- Modal Cancel -->
                                <div class="modal fade text-start" id="cancelOrderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="modal-content">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-danger">Cancel Order #{{ $order->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-secondary">Please specify why this active order is being cancelled. Both customer and driver will be notified immediately.</p>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Cancellation Reason <span class="text-danger">*</span></label>
                                                    <textarea name="reason" class="form-control" rows="3" required placeholder="e.g. Restaurant out of stock, customer requested cancellation, address unreachable..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-danger btn-sm fw-bold">Confirm Cancel Order</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

