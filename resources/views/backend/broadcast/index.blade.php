@extends('layout.backend')


@section('content')
<div class="page-header">
    <div>
        <h1>Broadcast Announcement Tool</h1>
        <p>Send persistent in-app notifications and news to all customers, couriers, or all platform members simultaneously.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Broadcast Compose Form -->
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="bi bi-megaphone-fill text-success me-1"></i> Compose Announcement
                </h3>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.broadcast.send') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Target Audience <span class="text-danger">*</span></label>
                        <select name="target_role" class="form-select @error('target_role') is-invalid @enderror" required>
                            <option value="all">Everyone (All Customers & Couriers)</option>
                            <option value="customer">Customers Only</option>
                            <option value="driver">Couriers / Drivers Only</option>
                            <option value="admin">Admins Only</option>
                        </select>
                        @error('target_role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Announcement Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                               placeholder="e.g. Rainy Day Delivery Surge or Weekend Promo 20% OFF!" required>
                        @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">Notification Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" 
                                  placeholder="Write your announcement details here..." required></textarea>
                        @error('message') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn-primary-green w-100 py-2.5">
                        <i class="bi bi-send-fill me-1"></i> Send Instant Broadcast
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Instructions / Best practices -->
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Broadcast Guidelines & Channels</h3>
            </div>
            <div class="panel-body text-secondary small" style="line-height: 1.6;">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div>
                        <strong class="text-dark d-block">Persistent In-App Storage</strong>
                        Broadcast notifications are permanently logged into the customer or courier in-app alert history and trigger badge count alerts.
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <strong class="text-dark d-block">Audit Tracking</strong>
                        Every broadcast is automatically timestamped and credited to your admin account in the audit security logs.
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <div>
                        <strong class="text-dark d-block">Instant Visibility</strong>
                        Users currently browsing the store will see real-time updates through background polling.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

