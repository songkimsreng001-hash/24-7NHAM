@extends('layout.backend')


@push('styles')
<style>
    .dashboard-subtitle { max-width:650px; }
    .sales-total { font-size:10px;color:#217a42; }
    .sales-total strong { color:#243a2f;font-size:14px;margin-left:4px; }
    .chart-tooltip {
        position:absolute;left:50%;top:-32px;transform:translateX(-50%);
        padding:4px 7px;border-radius:6px;background:#173d2b;color:#fff;
        font-size:8px;font-weight:700;white-space:nowrap;opacity:0;pointer-events:none;
        transition:.15s;
    }
    .bar:hover .chart-tooltip { opacity:1; }
    .status-summary { display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:12px; }
    .status-summary-item { background:#f8faf9;border-radius:9px;padding:9px; }
    .status-summary-item strong { display:block;font-size:13px;color:#24372e; }
    .status-summary-item span { font-size:8px;color:#9aa59f; }
    .order-amount { font-weight:800;color:#263a31; }
    .product-thumb {
        width:34px;height:34px;border-radius:9px;object-fit:cover;background:#edf5f0;
        display:block;
    }
    .product-thumb-placeholder {
        width:34px;height:34px;border-radius:9px;background:#e7f5ed;color:#268052;
        display:grid;place-items:center;font-size:10px;font-weight:800;
    }
    .role-card {
        cursor: pointer;
        transition: border-color .15s ease, background-color .15s ease;
    }
    .role-card:hover {
        border-color: var(--nham-500) !important;
        background-color: var(--nham-50);
    }
    html[data-bs-theme="dark"] .role-card:hover,
    html[data-theme="dark"] .role-card:hover {
        background-color: rgba(24, 168, 94, 0.08);
        border-color: #18a85e !important;
    }
    @media (max-width:680px) {
        .status-summary { grid-template-columns:1fr; }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="dashboard-subtitle">
        <h1>Dashboard</h1>
        <p>Overview of your products, orders, customers, and shop performance.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('product.create') }}" class="btn-primary-green">
            <span>+</span> Add Product
        </a>
        <a href="{{ route('admin.order') }}" class="btn-outline-green">
            View Orders
        </a>
    </div>
</div>

{{-- KPI cards --}}
<div class="stat-grid">
    <div class="stat-card green">
        <a href="{{ route('product.index') }}" class="stat-link" aria-label="View products">
            <svg viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"/></svg>
        </a>
        <div class="stat-label">Total Products</div>
        <div class="stat-value">{{ number_format($totalProducts) }}</div>
        <div class="stat-trend">Your current product catalog</div>
    </div>

    <div class="stat-card">
        <a href="{{ route('admin.order') }}" class="stat-corner-link" aria-label="View orders">
            <svg viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"/></svg>
        </a>
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ number_format($totalOrders) }}</div>
        <div class="stat-trend"><span class="trend-badge">All orders</span></div>
    </div>

    <div class="stat-card">
        <a href="{{ route('profile.edit', auth()->id()) }}" class="stat-corner-link" aria-label="View profile">
            <svg viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"/></svg>
        </a>
        <div class="stat-label">Customers</div>
        <div class="stat-value">{{ number_format($totalCustomers) }}</div>
        <div class="stat-trend"><span class="trend-badge">Registered users</span></div>
    </div>

    <div class="stat-card">
        <span class="stat-corner-link" style="cursor:default" aria-hidden="true">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M14.5 9.5c-.6-.7-1.4-1-2.6-1-1.5 0-2.5.7-2.5 1.8 0 1.2 1 1.6 2.6 1.9 1.7.3 2.7.8 2.7 2.1 0 1.1-1 2.2-2.8 2.2-1.3 0-2.3-.4-3-1.2M12 7v10"/></svg>
        </span>
        <div class="stat-label">Paid Revenue</div>
        <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
        <div class="stat-trend"><span class="trend-badge">Paid orders</span></div>
    </div>
</div>

{{-- Sales analytics / reminder / top products --}}
<div class="section-row three-col">

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Sales Analytics</h2>
                <div class="sales-total">This week <strong>${{ number_format($weeklySales->sum('revenue'), 2) }}</strong></div>
            </div>
            <span class="panel-badge">Current week</span>
        </div>
        <div class="panel-body">
            <div class="bar-chart-wrap">
                @foreach($weeklySales as $i => $sale)
                    @php
                        $height = $weeklyMax > 0 ? max(8, ($sale['revenue'] / $weeklyMax) * 100) : 8;
                        $isToday = $sale['day'] === now()->format('D');
                    @endphp
                    <div class="bar-col">
                        <div class="bar-track">
                            <div class="bar {{ $isToday ? 'active' : ($sale['revenue'] > 0 ? 'filled' : 'striped') }}"
                                 style="height:{{ $height }}%">
                                <span class="chart-tooltip">${{ number_format($sale['revenue'], 2) }}</span>
                            </div>
                        </div>
                        <div class="bar-day">{{ $sale['short'] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mini-stats">
                <div class="mini-stat">
                    <strong>{{ $approvedOrders }}</strong>
                    <span>Approved orders</span>
                </div>
                <div class="mini-stat">
                    <strong>{{ $pendingOrders }}</strong>
                    <span>Paid / processing</span>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Reminders</h2>
            <a href="{{ route('admin.order') }}" class="panel-action-btn">Review</a>
        </div>
        <div class="panel-body">
            <div class="reminder-card">
                <div class="reminder-label">Order attention</div>
                <div class="reminder-title">
                    {{ $pendingOrders }} paid {{ \Illuminate\Support\Str::plural('order', $pendingOrders) }} ready for processing
                </div>
                <div class="reminder-time">
                    Keep paid orders moving so customers receive their products on time.
                </div>
                <a href="{{ route('admin.order') }}" class="btn-start">Open Orders</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Top Products</h2>
            <a href="{{ route('product.create') }}" class="panel-action-btn">+ New</a>
        </div>
        <div class="panel-body" style="padding-top:7px">
            @forelse($topProducts as $p)
                <div class="project-item">
                    <div class="project-dot"></div>
                    <div class="project-name">{{ $p->name }}</div>
                    <div class="project-due">{{ $p->total_quantity }} sold</div>
                </div>
            @empty
                <div class="empty-state" style="padding:25px 0">No sales data yet.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent orders + status / timer --}}
<div class="section-row left-heavy">

    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Recent Orders</h2>
            <a href="{{ route('admin.order') }}" class="panel-action-btn">View all</a>
        </div>
        <div class="panel-body" style="padding-top:6px">
            @forelse($recentOrders as $order)
                @php
                    $statusMap = [
                        0 => ['label'=>'Rejected', 'class'=>'status-pend'],
                        1 => ['label'=>'Approved', 'class'=>'status-done'],
                        2 => ['label'=>'Paid', 'class'=>'status-prog'],
                    ];
                    $status = $statusMap[$order->status] ?? ['label'=>'Unknown', 'class'=>'status-review'];
                @endphp
                <div class="team-item">
                    <div class="team-avatar">
                        {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="team-info">
                        <div class="team-name">{{ $order->user->name ?? 'Unknown customer' }}</div>
                        <div class="team-task">Order #{{ $order->id }} · {{ optional($order->created_at)->format('d M Y, h:i A') }}</div>
                    </div>
                    <div style="text-align:right">
                        <div class="order-amount">${{ number_format($order->amount, 2) }}</div>
                        <span class="team-status {{ $status['class'] }}">{{ $status['label'] }}</span>
                    </div>
                </div>
            @empty
                <div class="empty-state">No orders yet.</div>
            @endforelse
        </div>
    </div>

    <div>
        @php
            $statusTotal = max($approvedOrders + $pendingOrders + $rejectedOrders, 1);
            $approvedPct = round(($approvedOrders / $statusTotal) * 100, 1);
            $paidEnd = $approvedPct + round(($pendingOrders / $statusTotal) * 100, 1);
            $rejectedEnd = $paidEnd + round(($rejectedOrders / $statusTotal) * 100, 1);
        @endphp

        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Order Status</h2>
                <span class="panel-badge">{{ $approvedOrders + $pendingOrders + $rejectedOrders }} tracked</span>
            </div>
            <div class="panel-body">
                <div class="donut-wrap">
                    <div class="donut"
                         style="--approved:{{ $approvedPct }}%;--paid:{{ $paidEnd }}%;--rejected:{{ $rejectedEnd }}%;">
                        <div class="donut-center">
                            <strong>{{ $totalOrders ? round(($pendingOrders / $totalOrders) * 100) : 0 }}%</strong>
                            <span>Paid</span>
                        </div>
                    </div>
                    <div class="donut-legend">
                        <div class="legend-row"><span class="legend-dot" style="background:#0a8950"></span>Approved <strong>{{ $approvedOrders }}</strong></div>
                        <div class="legend-row"><span class="legend-dot" style="background:#f1b94b"></span>Paid <strong>{{ $pendingOrders }}</strong></div>
                        <div class="legend-row"><span class="legend-dot" style="background:#e76c5d"></span>Rejected <strong>{{ $rejectedOrders }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="time-tracker">
            <div class="tracker-label">Session Timer</div>
            <div class="tracker-time" id="sessionTimer">00:00:00</div>
            <div class="tracker-controls">
                <button class="tracker-btn pause" id="trackerToggle" type="button" onclick="toggleTimer()" aria-label="Pause timer">
                    <svg id="trackerIcon" viewBox="0 0 24 24"><path d="M8 6v12M16 6v12" fill="none" stroke-width="2"/></svg>
                </button>
                <button class="tracker-btn stop" type="button" onclick="resetTimer()" aria-label="Reset timer">
                    <svg viewBox="0 0 24 24"><path d="M7 7h10v10H7z"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- User Roles & Access Management on Dashboard --}}
<div class="panel mb-4" id="dashboardUsersSection">
    <div class="panel-header flex-wrap gap-2">
        <div>
            <h2 class="panel-title d-flex align-items-center gap-2">
                <i class="bi bi-person-gear text-success"></i> User Roles & Access Management
            </h2>
            <div class="text-muted small mt-1">Assign and configure user roles (Customer, Courier / Driver, Administrator)</div>
        </div>
        <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
            <span class="badge bg-light text-secondary border px-2 py-1"><i class="bi bi-person me-1"></i> Customers: <strong>{{ $totalCustomersCount }}</strong></span>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-bicycle me-1"></i> Couriers: <strong>{{ $totalDrivers }}</strong></span>
            <span class="badge bg-dark-subtle text-dark border px-2 py-1"><i class="bi bi-shield-lock-fill me-1"></i> Admins: <strong>{{ $totalAdmins }}</strong></span>
            <div class="input-group input-group-sm" style="max-width: 220px;">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="dashboardUserFilter" class="form-control border-start-0" placeholder="Filter users..." autocomplete="off">
            </div>
            <a href="{{ route('admin.users') }}" class="panel-action-btn">Manage all</a>
        </div>
    </div>

    <div class="panel-body" style="padding:0;overflow-x:auto">
        <table class="table table-hover align-middle mb-0" id="dashboardUsersTable" style="font-size: 0.88rem;">
            <thead class="table-light text-muted small">
                <tr>
                    <th style="padding-left:19px">User</th>
                    <th>Current Role</th>
                    <th>Quick Role Switch</th>
                    <th>Contact Phone</th>
                    <th>Joined Date</th>
                    <th style="text-align:right;padding-right:19px">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dashboardUsers as $dashUser)
                    <tr>
                        <td style="padding-left:19px">
                            <div class="d-flex align-items-center gap-2">
                                @if($dashUser->avatar)
                                    <img src="{{ $dashUser->avatar_url }}" alt="{{ $dashUser->name }}" class="avatar rounded-circle object-fit-cover" style="width: 32px; height: 32px;">
                                @else
                                    <div class="avatar" style="width: 32px; height: 32px; font-size: 11px;">
                                        {{ strtoupper(substr($dashUser->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $dashUser->name }}</div>
                                    <div class="text-muted small">{{ $dashUser->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($dashUser->role === 'admin')
                                <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>
                            @elseif($dashUser->role === 'driver')
                                <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-bicycle me-1"></i>Courier</span>
                            @else
                                <span class="badge bg-light text-secondary border px-2 py-1"><i class="bi bi-person me-1"></i>Customer</span>
                            @endif
                        </td>
                        <td>
                            @if($dashUser->id === auth()->id())
                                <span class="badge bg-secondary-subtle text-secondary py-1 px-2" title="Logged-in account">
                                    <i class="bi bi-lock-fill me-1"></i> Current Admin
                                </span>
                            @else
                                <form action="{{ route('admin.users.assignRole', $dashUser->id) }}" method="POST" class="d-inline-block m-0">
                                    @csrf
                                    <select name="role" class="form-select form-select-sm py-1 px-2 rounded-pill fw-semibold border shadow-none" style="width: auto; font-size: 0.8rem;" onchange="this.form.submit()" title="Change user role">
                                        <option value="customer" {{ in_array($dashUser->role, ['customer', 'user', null]) ? 'selected' : '' }}>Customer</option>
                                        <option value="driver" {{ $dashUser->role === 'driver' ? 'selected' : '' }}>Courier / Driver</option>
                                        <option value="admin" {{ $dashUser->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                    </select>
                                </form>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ $dashUser->phone ?: '—' }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ $dashUser->created_at ? $dashUser->created_at->format('M d, Y') : '—' }}
                        </td>
                        <td style="text-align:right;padding-right:19px">
                            <button type="button" class="btn btn-sm btn-outline-success py-1 px-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#assignRoleModalDash{{ $dashUser->id }}" title="Configure role & driver settings">
                                <i class="bi bi-person-gear me-1"></i> Assign Role
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No user accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($dashboardUsers as $dashUser)
<div class="modal fade" id="assignRoleModalDash{{ $dashUser->id }}" tabindex="-1" aria-labelledby="assignRoleModalLabelDash{{ $dashUser->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.users.assignRole', $dashUser->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="assignRoleModalLabelDash{{ $dashUser->id }}">
                        <i class="bi bi-person-gear text-success me-2"></i>Assign Role for {{ $dashUser->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center gap-3">
                        @if($dashUser->avatar)
                            <img src="{{ $dashUser->avatar_url }}" alt="{{ $dashUser->name }}" class="avatar rounded-circle object-fit-cover" style="width: 40px; height: 40px;">
                        @else
                            <div class="avatar" style="width: 40px; height: 40px; font-size: 14px;">
                                {{ strtoupper(substr($dashUser->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="fw-bold text-dark text-truncate">{{ $dashUser->name }}</div>
                            <div class="text-muted small text-truncate">{{ $dashUser->email }}</div>
                        </div>
                        <div class="ms-auto">
                            @if($dashUser->role === 'admin')
                                <span class="badge bg-dark">Admin</span>
                            @elseif($dashUser->role === 'driver')
                                <span class="badge bg-success">Courier</span>
                            @else
                                <span class="badge bg-secondary">Customer</span>
                            @endif
                        </div>
                    </div>

                    @if($dashUser->id === auth()->id())
                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="bi bi-shield-exclamation me-1"></i> You are currently logged in as this administrator. Modifying your own admin role is disabled to prevent lockout.
                        </div>
                    @else
                        <label class="form-label fw-bold small text-uppercase text-muted">Select Account Role</label>
                        <div class="d-grid gap-2 mb-3">
                            <label class="border rounded-3 p-3 d-flex align-items-start gap-3 cursor-pointer role-card" for="roleCustDash{{ $dashUser->id }}">
                                <input type="radio" name="role" id="roleCustDash{{ $dashUser->id }}" value="customer" class="form-check-input mt-1" {{ in_array($dashUser->role, ['customer', 'user', null]) ? 'checked' : '' }} onchange="toggleDriverFields('Dash', {{ $dashUser->id }})">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-person-circle text-primary"></i> Customer
                                        <span class="badge bg-light text-secondary border">Default</span>
                                    </div>
                                    <div class="text-muted small mt-1">Standard customer account to browse menus, place orders, and track deliveries.</div>
                                </div>
                            </label>

                            <label class="border rounded-3 p-3 d-flex align-items-start gap-3 cursor-pointer role-card" for="roleDriverDash{{ $dashUser->id }}">
                                <input type="radio" name="role" id="roleDriverDash{{ $dashUser->id }}" value="driver" class="form-check-input mt-1" {{ $dashUser->role === 'driver' ? 'checked' : '' }} onchange="toggleDriverFields('Dash', {{ $dashUser->id }})">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-bicycle text-success"></i> Courier / Driver
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Delivery</span>
                                    </div>
                                    <div class="text-muted small mt-1">Can access the Courier Dashboard, accept delivery orders, update live delivery status, and earn delivery fees.</div>
                                </div>
                            </label>

                            <label class="border rounded-3 p-3 d-flex align-items-start gap-3 cursor-pointer role-card" for="roleAdminDash{{ $dashUser->id }}">
                                <input type="radio" name="role" id="roleAdminDash{{ $dashUser->id }}" value="admin" class="form-check-input mt-1" {{ $dashUser->role === 'admin' ? 'checked' : '' }} onchange="toggleDriverFields('Dash', {{ $dashUser->id }})">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-shield-lock-fill text-dark"></i> Administrator
                                        <span class="badge bg-dark text-white">Full Access</span>
                                    </div>
                                    <div class="text-muted small mt-1">Full access to manage products, categories, orders, user roles, promotions, and system analytics.</div>
                                </div>
                            </label>
                        </div>

                        {{-- Driver-specific options --}}
                        <div id="driverFieldsDash{{ $dashUser->id }}" class="border rounded-3 p-3 bg-light-subtle mb-3" style="{{ $dashUser->role === 'driver' ? '' : 'display:none;' }}">
                            <div class="fw-bold small text-success mb-2"><i class="bi bi-gear-fill me-1"></i> Courier Setup Options</div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small">Vehicle Type</label>
                                    <select name="vehicle_type" class="form-select form-select-sm">
                                        <option value="motorbike" {{ ($dashUser->vehicle_type ?? '') === 'motorbike' ? 'selected' : '' }}>Motorbike</option>
                                        <option value="bicycle" {{ ($dashUser->vehicle_type ?? '') === 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                                        <option value="car" {{ ($dashUser->vehicle_type ?? '') === 'car' ? 'selected' : '' }}>Car / Van</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">License Plate</label>
                                    <input type="text" name="license_plate" class="form-control form-control-sm" placeholder="e.g. 1AB-2345" value="{{ $dashUser->license_plate ?? '' }}">
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="verify_driver" id="verifyDriverDash{{ $dashUser->id }}" value="1" {{ $dashUser->driver_verified_at ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold" for="verifyDriverDash{{ $dashUser->id }}">Verify driver account immediately</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    @if($dashUser->id !== auth()->id())
                        <button type="submit" class="btn btn-sm btn-primary-green px-4">
                            <i class="bi bi-check-lg me-1"></i> Update Role
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Latest products --}}
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Latest Products</h2>
        <a href="{{ route('product.index') }}" class="panel-action-btn">View all</a>
    </div>

    <div class="panel-body" style="padding:0;overflow-x:auto">
        @if($products->isEmpty())
            <div class="empty-state">
                No products yet.
                <a href="{{ route('product.create') }}" style="color:#17824e;font-weight:800">Add your first product</a>
            </div>
        @else
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="padding-left:19px">Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th style="text-align:right;padding-right:19px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td style="padding-left:19px">
                                <div style="display:flex;align-items:center;gap:9px">
                                    @if($product->image)
                                        <img class="product-thumb" src="{{ asset('img/'.$product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="product-thumb-placeholder">{{ strtoupper(substr($product->name,0,1)) }}</div>
                                    @endif
                                    <div>
                                        <div class="product-name">{{ $product->name }}</div>
                                        <div class="product-id">ID #{{ $product->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($product->category)
                                    <span class="cat-badge">{{ $product->category->name }}</span>
                                @else
                                    <span style="color:#a0aaa5">—</span>
                                @endif
                            </td>
                            <td class="price-cell">${{ number_format($product->price, 2) }}</td>
                            <td style="text-align:right;padding-right:19px">
                                <a href="{{ route('product.show', $product->id) }}" class="btn-view">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    let seconds = 0;
    let running = true;
    let interval = setInterval(tick, 1000);

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        seconds++;
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        const timer = document.getElementById('sessionTimer');
        if (timer) timer.textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;
    }

    function setTimerIcon(paused) {
        const icon = document.getElementById('trackerIcon');
        if (!icon) return;
        icon.innerHTML = paused
            ? '<path d="m9 6 8 6-8 6z"/>'
            : '<path d="M8 6v12M16 6v12" fill="none" stroke-width="2"/>';
    }

    function toggleTimer() {
        if (running) {
            clearInterval(interval);
            setTimerIcon(true);
        } else {
            interval = setInterval(tick, 1000);
            setTimerIcon(false);
        }
        running = !running;
    }

    function resetTimer() {
        clearInterval(interval);
        seconds = 0;
        running = false;
        const timer = document.getElementById('sessionTimer');
        if (timer) timer.textContent = '00:00:00';
        setTimerIcon(true);
    }

    function toggleDriverFields(prefix, userId) {
        const driverFields = document.getElementById('driverFields' + prefix + userId);
        const driverRadio = document.getElementById('roleDriver' + prefix + userId);
        if (driverFields && driverRadio) {
            driverFields.style.display = driverRadio.checked ? 'block' : 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const userFilter = document.getElementById('dashboardUserFilter');
        const userRows = document.querySelectorAll('#dashboardUsersTable tbody tr');

        if (userFilter) {
            userFilter.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                userRows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = !q || text.includes(q) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
