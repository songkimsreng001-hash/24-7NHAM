@extends('layout.backend')


@section('content')
<div class="page-header">
    <div>
        <h1>Promo Codes & Discounts</h1>
        <p>Create and manage discount vouchers for customer food delivery checkouts.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Create Promo Form -->
    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="bi bi-tag-fill text-success me-1"></i> Create New Code
                </h3>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.promos.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror" 
                               placeholder="e.g. NHAM20, FREESHIP" required>
                        @error('code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Discount Type <span class="text-danger">*</span></label>
                        <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" required>
                            <option value="percent">Percentage Discount (%)</option>
                            <option value="fixed">Fixed Amount Discount ($)</option>
                        </select>
                        @error('discount_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="discount_value" class="form-control @error('discount_value') is-invalid @enderror" 
                               placeholder="e.g. 10 for 10% or $10" required>
                        @error('discount_value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Minimum Order Value ($)</label>
                        <input type="number" step="0.01" name="min_order" class="form-control @error('min_order') is-invalid @enderror" 
                               value="0" placeholder="0.00">
                        @error('min_order') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">Expiration Date (Optional)</label>
                        <input type="date" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror">
                        @error('expires_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn-primary-green w-100 py-2.5">
                        <i class="bi bi-plus-circle me-1"></i> Save Promo Code
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Promo Codes Table -->
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Active & Expired Promo Codes</h3>
                <span class="panel-badge">{{ $promos->total() }} Total</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Min Order</th>
                            <th>Status</th>
                            <th>Expires</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promos as $promo)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace fs-6">
                                        {{ $promo->code }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        {{ $promo->discount_type === 'percent' ? $promo->discount_value . '%' : '$' . number_format($promo->discount_value, 2) }} OFF
                                    </span>
                                </td>
                                <td>
                                    ${{ number_format($promo->min_order, 2) }}
                                </td>
                                <td>
                                    @if($promo->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $promo->expires_at ? $promo->expires_at->format('M d, Y') : 'No Expiry' }}
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.promos.toggle', $promo->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @if($promo->is_active)
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">Deactivate</button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2">Activate</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No promo codes created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top d-flex justify-content-center">
                {{ $promos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

