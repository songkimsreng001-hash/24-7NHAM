@extends('layout.backend')


@push('styles')
<style>
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
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>User & Courier Management</h1>
        <p>Manage customer accounts, verify driver onboarding, and monitor account access.</p>
    </div>
    <div class="header-actions">
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-white text-dark border px-3 py-2 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-primary"></i> Customers: <strong>{{ $totalCustomers }}</strong>
            </span>
            <span class="badge bg-white text-dark border px-3 py-2 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-bicycle text-success"></i> Couriers: <strong>{{ $totalDrivers }}</strong>
            </span>
            <span class="badge bg-white text-dark border px-3 py-2 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-dark"></i> Admins: <strong>{{ $totalAdmins }}</strong>
            </span>
        </div>
    </div>
</div>

<!-- Role Filters -->
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="{{ route('admin.users', ['role' => 'all']) }}" class="btn {{ $role === 'all' ? 'btn-primary-green' : 'btn-outline-green' }} btn-sm rounded-pill px-3">
        All Accounts
    </a>
    <a href="{{ route('admin.users', ['role' => 'customer']) }}" class="btn {{ $role === 'customer' ? 'btn-primary-green' : 'btn-outline-green' }} btn-sm rounded-pill px-3">
        Customers Only
    </a>
    <a href="{{ route('admin.users', ['role' => 'driver']) }}" class="btn {{ $role === 'driver' ? 'btn-primary-green' : 'btn-outline-green' }} btn-sm rounded-pill px-3">
        Couriers / Drivers Only
    </a>
    <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="btn {{ $role === 'admin' ? 'btn-primary-green' : 'btn-outline-green' }} btn-sm rounded-pill px-3">
        Admins Only
    </a>
</div>

<!-- Users Table -->
<div class="panel">
    <div class="panel-header flex-wrap gap-2">
        <h3 class="panel-title">Registered Accounts</h3>
        <div class="input-group input-group-sm" style="max-width: 340px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="userTableFilter" class="form-control border-start-0" placeholder="Filter users by name, email, phone..." autocomplete="off">
        </div>
        <span class="panel-badge ms-auto" id="userTableFilterCount">{{ $users->total() }} Total Accounts</span>
    </div>
    <div class="table-responsive">
        <table id="usersTable" class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light text-muted small">
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Contact Phone</th>
                    <th>Driver Status / Vehicle</th>
                    <th>Account Status</th>
                    <th>Joined Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar" style="width: 32px; height: 32px; font-size: 11px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->id === auth()->id())
                                <span class="badge bg-dark text-white px-2 py-1" title="Current Administrator (You)"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>
                            @else
                                <form action="{{ route('admin.users.assignRole', $user->id) }}" method="POST" class="d-inline-block m-0">
                                    @csrf
                                    <select name="role" class="form-select form-select-sm py-1 px-2 rounded-pill fw-semibold border shadow-none" style="width: auto; font-size: 0.8rem;" onchange="this.form.submit()" title="Change user role directly">
                                        <option value="customer" {{ in_array($user->role, ['customer', 'user', null]) ? 'selected' : '' }}>Customer</option>
                                        <option value="driver" {{ $user->role === 'driver' ? 'selected' : '' }}>Courier / Driver</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                    </select>
                                </form>
                            @endif
                        </td>
                        <td>
                            @if($user->phone)
                                <span class="fw-semibold text-dark">{{ $user->phone }}</span>
                            @else
                                <span class="text-danger small fst-italic">Missing phone</span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'driver')
                                <div>
                                    @if($user->driver_verified_at)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check2-circle me-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            <i class="bi bi-hourglass-split me-1"></i> Unverified
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted small mt-1">
                                    {{ ucfirst($user->vehicle_type ?? 'Motorbike') }} • {{ $user->license_plate ?? 'No Plate' }}
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status === 'suspended')
                                <span class="badge bg-danger">Suspended</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#assignRoleModal{{ $user->id }}" title="Configure role & driver details">
                                    <i class="bi bi-person-gear me-1"></i> Role
                                </button>

                                @if($user->role === 'driver' && !$user->driver_verified_at)
                                    <form action="{{ route('admin.users.approveDriver', $user->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2 rounded-pill" title="Verify driver">
                                            <i class="bi bi-check-circle me-1"></i> Verify
                                        </button>
                                    </form>
                                @endif

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @if($user->status === 'suspended')
                                            <button type="submit" class="btn btn-sm btn-outline-primary py-1 px-2 rounded-pill">
                                                Activate
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-pill" onclick="return confirm('Suspend user {{ $user->name }}?')">
                                                Suspend
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No users found for this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>

@foreach($users as $user)
<div class="modal fade" id="assignRoleModal{{ $user->id }}" tabindex="-1" aria-labelledby="assignRoleModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.users.assignRole', $user->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="assignRoleModalLabel{{ $user->id }}">
                        <i class="bi bi-person-gear text-success me-2"></i>Assign Role for {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center gap-3">
                        @if($user->avatar)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar rounded-circle object-fit-cover" style="width: 40px; height: 40px;">
                        @else
                            <div class="avatar" style="width: 40px; height: 40px; font-size: 14px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="fw-bold text-dark text-truncate">{{ $user->name }}</div>
                            <div class="text-muted small text-truncate">{{ $user->email }}</div>
                        </div>
                        <div class="ms-auto">
                            @if($user->role === 'admin')
                                <span class="badge bg-dark">Admin</span>
                            @elseif($user->role === 'driver')
                                <span class="badge bg-success">Courier</span>
                            @else
                                <span class="badge bg-secondary">Customer</span>
                            @endif
                        </div>
                    </div>

                    @if($user->id === auth()->id())
                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="bi bi-shield-exclamation me-1"></i> You are currently logged in as this administrator. Modifying your own admin role is disabled to prevent lockout.
                        </div>
                    @else
                        <label class="form-label fw-bold small text-uppercase text-muted">Select Account Role</label>
                        <div class="d-grid gap-2 mb-3">
                            <label class="border rounded-3 p-3 d-flex align-items-start gap-3 cursor-pointer role-card" for="roleCust{{ $user->id }}">
                                <input type="radio" name="role" id="roleCust{{ $user->id }}" value="customer" class="form-check-input mt-1" {{ in_array($user->role, ['customer', 'user', null]) ? 'checked' : '' }} onchange="toggleDriverFields('', {{ $user->id }})">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-person-circle text-primary"></i> Customer
                                        <span class="badge bg-light text-secondary border">Default</span>
                                    </div>
                                    <div class="text-muted small mt-1">Standard customer account to browse menus, place orders, and track deliveries.</div>
                                </div>
                            </label>

                            <label class="border rounded-3 p-3 d-flex align-items-start gap-3 cursor-pointer role-card" for="roleDriver{{ $user->id }}">
                                <input type="radio" name="role" id="roleDriver{{ $user->id }}" value="driver" class="form-check-input mt-1" {{ $user->role === 'driver' ? 'checked' : '' }} onchange="toggleDriverFields('', {{ $user->id }})">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-bicycle text-success"></i> Courier / Driver
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Delivery</span>
                                    </div>
                                    <div class="text-muted small mt-1">Can access the Courier Dashboard, accept delivery orders, update live delivery status, and earn delivery fees.</div>
                                </div>
                            </label>

                            <label class="border rounded-3 p-3 d-flex align-items-start gap-3 cursor-pointer role-card" for="roleAdmin{{ $user->id }}">
                                <input type="radio" name="role" id="roleAdmin{{ $user->id }}" value="admin" class="form-check-input mt-1" {{ $user->role === 'admin' ? 'checked' : '' }} onchange="toggleDriverFields('', {{ $user->id }})">
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
                        <div id="driverFields{{ $user->id }}" class="border rounded-3 p-3 bg-light-subtle mb-3" style="{{ $user->role === 'driver' ? '' : 'display:none;' }}">
                            <div class="fw-bold small text-success mb-2"><i class="bi bi-gear-fill me-1"></i> Courier Setup Options</div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small">Vehicle Type</label>
                                    <select name="vehicle_type" class="form-select form-select-sm">
                                        <option value="motorbike" {{ ($user->vehicle_type ?? '') === 'motorbike' ? 'selected' : '' }}>Motorbike</option>
                                        <option value="bicycle" {{ ($user->vehicle_type ?? '') === 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                                        <option value="car" {{ ($user->vehicle_type ?? '') === 'car' ? 'selected' : '' }}>Car / Van</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">License Plate</label>
                                    <input type="text" name="license_plate" class="form-control form-control-sm" placeholder="e.g. 1AB-2345" value="{{ $user->license_plate ?? '' }}">
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="verify_driver" id="verifyDriver{{ $user->id }}" value="1" {{ $user->driver_verified_at ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold" for="verifyDriver{{ $user->id }}">Verify driver account immediately</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    @if($user->id !== auth()->id())
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
@endsection

@push('scripts')
<script>
function toggleDriverFields(prefix, userId) {
    const driverFields = document.getElementById('driverFields' + prefix + userId);
    const driverRadio = document.getElementById('roleDriver' + prefix + userId);
    if (driverFields && driverRadio) {
        driverFields.style.display = driverRadio.checked ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const userTableFilter = document.getElementById('userTableFilter');
    const userTableFilterCount = document.getElementById('userTableFilterCount');
    const userRows = document.querySelectorAll('#usersTable tbody tr');

    if (userTableFilter) {
        userTableFilter.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            let matched = 0;

            userRows.forEach(row => {
                if (row.id === 'userNoMatchRow') return;
                const text = row.innerText.toLowerCase();
                const match = !q || text.includes(q);
                row.style.display = match ? '' : 'none';
                if (match) matched++;
            });

            if (userTableFilterCount) {
                userTableFilterCount.textContent = q 
                    ? `${matched} of ${userRows.length} matches` 
                    : `{{ $users->total() }} Total Accounts`;
            }

            let noMatchRow = document.getElementById('userNoMatchRow');
            if (matched === 0 && q) {
                if (!noMatchRow) {
                    noMatchRow = document.createElement('tr');
                    noMatchRow.id = 'userNoMatchRow';
                    noMatchRow.innerHTML = `<td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-search me-1"></i>No accounts found matching "${this.value}"</td>`;
                    document.querySelector('#usersTable tbody').appendChild(noMatchRow);
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

