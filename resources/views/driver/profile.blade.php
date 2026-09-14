@extends('driver.layout')

@section('title', 'Courier Profile - 24/7 NHAM')

@section('content')
<div class="row g-4 justify-content-center">
    
    <!-- Profile Edit Form -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    @if($driver->avatar)
                        <img src="{{ $driver->avatar_url }}" alt="{{ $driver->name }}" class="rounded-circle object-fit-cover shadow-sm border" style="width: 56px; height: 56px;">
                    @else
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bolder fs-3" style="width: 56px; height: 56px;">
                            {{ strtoupper(substr($driver->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $driver->name }}</h5>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            @if($driver->driver_verified_at)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                    <i class="bi bi-patch-check-fill me-1"></i> Verified Courier
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                    <i class="bi bi-clock me-1"></i> Verification Pending
                                </span>
                            @endif
                            <span class="text-muted small">{{ $driver->email }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.edit', $driver->id) }}" class="btn btn-outline-success btn-sm rounded-3">
                    <i class="bi bi-camera me-1"></i> Change Photo
                </a>
            </div>

            <form action="{{ route('driver.profile.update') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Contact Phone Number <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-1"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $driver->phone) }}" placeholder="e.g. +855 12 345 678" required>
                    </div>
                    <div class="form-text text-muted" style="font-size: 0.75rem;">Required for customer contact. Shown with privacy masking to customers.</div>
                    @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Vehicle Type <span class="text-danger">*</span></label>
                    <select name="vehicle_type" class="form-select @error('vehicle_type') is-invalid @enderror" required>
                        <option value="motorbike" {{ old('vehicle_type', $driver->vehicle_type) === 'motorbike' ? 'selected' : '' }}>Motorbike / Scooter</option>
                        <option value="bicycle" {{ old('vehicle_type', $driver->vehicle_type) === 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                        <option value="car" {{ old('vehicle_type', $driver->vehicle_type) === 'car' ? 'selected' : '' }}>Car / Van</option>
                    </select>
                    @error('vehicle_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">License Plate / Vehicle ID <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-1"><i class="bi bi-card-text"></i></span>
                        <input type="text" name="license_plate" class="form-control @error('license_plate') is-invalid @enderror" 
                               value="{{ old('license_plate', $driver->license_plate) }}" placeholder="e.g. 1AB-2345 (Phnom Penh)" required>
                    </div>
                    @error('license_plate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-success w-100 rounded-pill py-2.5 fw-bold shadow-sm">
                    Save Profile & Vehicle Details
                </button>
            </form>

        </div>
    </div>

    <!-- Ratings & Customer Feedback Column -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-star-fill text-warning me-1"></i> Customer Ratings & Reviews
            </h6>

            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 mb-3">
                <div class="display-5 fw-bolder text-warning lh-1">
                    {{ number_format($driver->averageDriverRating(), 1) }}
                </div>
                <div>
                    <div class="text-warning fs-5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($driver->averageDriverRating()) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <div class="text-muted small mt-1">Based on {{ $driver->driverReviews->count() }} customer ratings</div>
                </div>
            </div>

            @if($reviews->isEmpty())
                <div class="text-center py-4 text-muted small">
                    No customer ratings yet. Complete deliveries to build your reputation!
                </div>
            @else
                <div class="list-group list-group-flush small">
                    @foreach($reviews as $rev)
                        <div class="list-group-item px-0 py-2.5">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark">{{ $rev->user ? $rev->user->name : 'Customer' }}</span>
                                <span class="text-warning fw-bold">
                                    {{ $rev->driver_rating }} <i class="bi bi-star-fill"></i>
                                </span>
                            </div>
                            @if($rev->comment)
                                <p class="text-secondary mb-1 fst-italic">"{{ $rev->comment }}"</p>
                            @endif
                            <span class="text-muted" style="font-size: 0.72rem;">{{ $rev->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

</div>
@endsection

