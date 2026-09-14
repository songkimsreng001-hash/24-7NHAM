@extends('layout.backend')

@section('page-title', 'Delivery Analytics & Performance - 24/7 NHAM Admin')

@section('content')
<div class="page-header">
    <div>
        <h1>Business Analytics & Courier Performance</h1>
        <p>Live revenue metrics, payment method distributions, top-selling foods, and driver fulfillment rates.</p>
    </div>
</div>

<!-- Primary Stats Grid -->
<div class="stat-grid mb-4">
    <div class="stat-card green">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
        <div class="stat-trend"><span class="trend-badge">Approved / Paid</span></div>
        <div class="stat-link"><i class="bi bi-currency-dollar"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Orders Placed</div>
        <div class="stat-value">{{ $totalOrders }}</div>
        <div class="stat-trend"><span class="trend-badge text-primary bg-primary-subtle">All-time</span></div>
        <div class="stat-corner-link"><i class="bi bi-bag-check"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Cash on Delivery (COD)</div>
        <div class="stat-value text-warning">{{ $codOrders }}</div>
        <div class="stat-trend"><span class="trend-badge text-warning bg-warning-subtle">{{ $totalOrders > 0 ? round(($codOrders / $totalOrders) * 100) : 0 }}% of total</span></div>
        <div class="stat-corner-link"><i class="bi bi-cash-stack"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Card / Stripe Online</div>
        <div class="stat-value text-success">{{ $cardOrders }}</div>
        <div class="stat-trend"><span class="trend-badge text-success bg-success-subtle">{{ $totalOrders > 0 ? round(($cardOrders / $totalOrders) * 100) : 0 }}% of total</span></div>
        <div class="stat-corner-link"><i class="bi bi-credit-card"></i></div>
    </div>
</div>

<div class="row g-4">
    <!-- Top Selling Dishes -->
    <div class="col-lg-6">
        <div class="panel h-100">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="bi bi-trophy-fill text-warning me-1"></i> Top Selling Menu Items
                </h3>
            </div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Product</th>
                                <th>Units Sold</th>
                                <th class="text-end">Total Gross</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $prod)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $prod->name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $prod->total_qty }} ordered</span>
                                    </td>
                                    <td class="text-end fw-bolder text-success">
                                        ${{ number_format($prod->total_sales, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No sales recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Courier Leaderboard -->
    <div class="col-lg-6">
        <div class="panel h-100">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="bi bi-bicycle text-success me-1"></i> Courier Performance & Fulfillment
                </h3>
            </div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Courier</th>
                                <th>Completed</th>
                                <th>Rating</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($driverStats as $drv)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $drv->name }}</div>
                                        <div class="text-muted small">{{ ucfirst($drv->vehicle_type ?? 'Motorbike') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                            {{ $drv->completed_deliveries }} jobs
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-warning fw-bold">
                                            <i class="bi bi-star-fill"></i> {{ number_format($drv->averageDriverRating(), 1) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($drv->is_online)
                                            <span class="badge bg-success">Online</span>
                                        @else
                                            <span class="badge bg-secondary">Offline</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No couriers registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

