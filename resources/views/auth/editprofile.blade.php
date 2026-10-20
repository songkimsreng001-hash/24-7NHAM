@extends('auth.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-person-gear me-2"></i>{{ __('Edit Profile') }}
                    </h5>
                    <span class="badge bg-light text-muted border">Account</span>
                </div>

                <div class="card-body p-4">
                    {{-- Form Validation Error Alert --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-octagon-fill fs-5 me-2"></i>
                                <strong>Please fix the following issues:</strong>
                            </div>
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update', $user) }}" id="editProfileForm" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
                        <style>
                            .avatar-circle-wrapper {
                                width: 120px;
                                height: 120px;
                                cursor: pointer;
                                position: relative;
                                overflow: hidden;
                                border: 3.5px solid #16a34a;
                                box-shadow: 0 4px 14px rgba(22, 163, 74, 0.2);
                                transition: all 0.22s ease-in-out;
                                user-select: none;
                            }
                            .avatar-circle-wrapper:hover {
                                transform: scale(1.05);
                                border-color: #15803d;
                                box-shadow: 0 8px 24px rgba(22, 163, 74, 0.35);
                            }
                            .avatar-circle-wrapper img {
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                                display: block;
                                transition: filter 0.2s ease;
                            }
                            .avatar-circle-wrapper:hover img {
                                filter: brightness(0.75);
                            }
                            .avatar-hover-overlay {
                                position: absolute;
                                inset: 0;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                color: #ffffff;
                                background: rgba(0, 0, 0, 0.45);
                                opacity: 0;
                                transition: opacity 0.22s ease-in-out;
                                border-radius: 50%;
                                pointer-events: none;
                            }
                            .avatar-circle-wrapper:hover .avatar-hover-overlay {
                                opacity: 1;
                            }
                            .avatar-camera-badge {
                                position: absolute;
                                bottom: 2px;
                                right: 2px;
                                width: 36px;
                                height: 36px;
                                background: #16a34a;
                                color: #ffffff;
                                border: 2.5px solid #ffffff;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
                                cursor: pointer;
                                transition: transform 0.2s ease, background 0.2s ease;
                            }
                            .avatar-circle-wrapper:hover ~ .avatar-camera-badge,
                            .avatar-camera-badge:hover {
                                transform: scale(1.1);
                                background: #15803d;
                            }

                            /* Cropper Circular Mask & Styling */
                            .cropper-view-box,
                            .cropper-face {
                                border-radius: 50%;
                            }
                            .cropper-view-box {
                                outline: 2px solid #16a34a;
                                outline-color: rgba(22, 163, 74, 0.9);
                            }
                            .crop-live-preview {
                                background-color: #f8fafc;
                                background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
                                background-size: 8px 8px;
                            }
                        </style>

                        <!-- Profile Photo Upload (Click on profile rounded-circle to upload & crop) -->
                        <div class="mb-4 text-center">
                            <div class="position-relative d-inline-block mb-2">
                                <div id="profileAvatarCircle" 
                                     class="avatar-circle-wrapper rounded-circle" 
                                     title="Click on profile rounded-circle to upload and crop photo">
                                    <img id="avatarPreview" 
                                         src="{{ $user->avatar_url }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle">
                                    <div class="avatar-hover-overlay rounded-circle">
                                        <i class="bi bi-camera-fill fs-3 mb-1"></i>
                                        <span class="fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Change Photo</span>
                                    </div>
                                </div>
                                <div class="avatar-camera-badge rounded-circle" id="profileCameraBadge" title="Click to upload and crop photo">
                                    <i class="bi bi-camera-fill" style="font-size: 0.95rem;"></i>
                                </div>
                            </div>
                            <div>
                                <label for="avatarInput" class="form-label fw-bold text-dark small d-block mb-1" style="cursor: pointer;">
                                    Profile Photo
                                </label>
                                <div class="d-flex justify-content-center">
                                    <!-- File input triggered by clicking the profile rounded-circle -->
                                    <input type="file" 
                                           class="form-control form-control-sm w-auto d-none @error('avatar') is-invalid @enderror" 
                                           id="avatarInput" 
                                           name="avatar" 
                                           accept=".png,.jpg,.jpeg,.webp,.PNG,.JPG,.JPEG,.WEBP,image/png,image/jpeg,image/webp">
                                    <!-- Hidden field to hold cropped base64 payload as fallback -->
                                    <input type="hidden" name="cropped_avatar_data" id="croppedAvatarData">
                                </div>
                                @if($user->avatar)
                                    <div class="form-check d-inline-block mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_avatar" id="remove_avatar" value="1">
                                        <label class="form-check-label small text-danger" for="remove_avatar">
                                            <i class="bi bi-trash3 me-1"></i>Remove current photo
                                        </label>
                                    </div>
                                @endif
                                @error('avatar')
                                    <div class="text-danger small mt-1 fw-semibold">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Name Input -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">{{ __('Name') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? auth()->user()->name }}" required autocomplete="name" autofocus placeholder="Enter your full name">
                                
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">{{ __('E-Mail Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? auth()->user()->email }}" required autocomplete="email" placeholder="Enter your email address">
                                
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone Number Input (Required for Delivery & Checkout) -->
                        <div class="mb-4">
                            <label for="phone" class="form-label fw-semibold d-flex justify-content-between">
                                <span>{{ __('Phone Number') }} <span class="text-danger">*</span></span>
                                <span class="text-muted small">Required for order deliveries</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="bi bi-telephone"></i>
                                </span>
                                <input id="phone" type="tel" class="form-control font-monospace @error('phone') is-invalid @enderror" name="phone" value="{{ preg_replace('/[^0-9]/', '', old('phone') ?? (auth()->user()->phone ?? '')) }}" required placeholder="0123456789" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-text small text-muted"><i class="bi bi-shield-check text-success me-1"></i>Numbers only (exactly 10 digits). Couriers will contact you at this number for order deliveries.</div>
                        </div>

                        @if(auth()->user()->isDriver())
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <div class="fw-bold small text-dark mb-2"><i class="bi bi-bicycle me-1 text-success"></i>Delivery Courier Details</div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Vehicle Type</label>
                                    <select name="vehicle_type" class="form-select form-select-sm">
                                        <option value="motorbike" {{ auth()->user()->vehicle_type === 'motorbike' ? 'selected' : '' }}>Motorbike</option>
                                        <option value="bicycle" {{ auth()->user()->vehicle_type === 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                                        <option value="scooter" {{ auth()->user()->vehicle_type === 'scooter' ? 'selected' : '' }}>Scooter</option>
                                        <option value="car" {{ auth()->user()->vehicle_type === 'car' ? 'selected' : '' }}>Car</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">License Plate</label>
                                    <input type="text" name="license_plate" class="form-control form-control-sm" value="{{ auth()->user()->license_plate }}" placeholder="e.g. 1AB-2345">
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Action Button Group -->
                        <div class="d-flex justify-content-end">
                            <div class="btn-group" role="group" aria-label="Profile Actions">
                                <a href="{{ url()->previous() }}" class="btn btn-danger">
                                    <i class="bi bi-arrow-left me-1"></i>Cancel
                                </a>
                            </div>
                            <div class="btn-group ms-2" role="group" aria-label="Profile Actions">
                                <button type="button" class="btn btn-success" id="btnUpdateProfile">
                                    <i class="bi bi-check-circle me-1"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Crop Profile Photo Modal -->
                    <div class="modal fade" id="cropImageModal" tabindex="-1" aria-labelledby="cropImageModalLabel" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                <div class="modal-header bg-light border-bottom py-3">
                                    <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="cropImageModalLabel">
                                        <i class="bi bi-crop text-success fs-5"></i>
                                        <span>Crop & Adjust Profile Photo</span>
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p class="text-muted small mb-3">
                                        Drag the frame or image to reposition. Use the controls or scroll wheel to zoom and rotate your photo before uploading.
                                    </p>

                                    <div class="row g-4 align-items-center">
                                        <!-- Main Cropper Viewport -->
                                        <div class="col-md-8">
                                            <div class="cropper-container-wrapper bg-dark rounded-3 overflow-hidden d-flex align-items-center justify-content-center shadow-inner" style="height: 380px; max-height: 55vh;">
                                                <img id="cropImageTarget" src="" alt="Crop Target" style="max-width: 100%; display: block;">
                                            </div>

                                            <!-- Cropper Action Buttons -->
                                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mt-3">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" id="btnZoomIn" title="Zoom In">
                                                        <i class="bi bi-zoom-in me-1"></i>Zoom In
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" id="btnZoomOut" title="Zoom Out">
                                                        <i class="bi bi-zoom-out me-1"></i>Zoom Out
                                                    </button>
                                                </div>

                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" id="btnRotateLeft" title="Rotate Left 90°">
                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>-90°
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" id="btnRotateRight" title="Rotate Right 90°">
                                                        <i class="bi bi-arrow-clockwise me-1"></i>+90°
                                                    </button>
                                                </div>

                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetCrop" title="Reset View">
                                                    <i class="bi bi-arrow-repeat me-1"></i>Reset
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Circular Live Previews -->
                                        <div class="col-md-4 text-center border-start ps-md-4">
                                            <div class="text-muted small fw-bold text-uppercase mb-3" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                Circular Preview
                                            </div>
                                            <div class="d-flex flex-column align-items-center gap-3">
                                                <div class="crop-live-preview rounded-circle border border-3 border-success shadow-sm overflow-hidden" 
                                                     style="width: 120px; height: 120px;"></div>
                                                <div class="crop-live-preview rounded-circle border border-2 border-secondary-subtle overflow-hidden" 
                                                     style="width: 60px; height: 60px;"></div>
                                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                    This is how your cropped photo will appear in your profile circle.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top py-2 px-4 d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="button" class="btn btn-success rounded-3 fw-semibold px-4" id="btnApplyCrop">
                                        <i class="bi bi-check2 me-1"></i>Crop & Apply Photo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Cropper.js JavaScript Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editProfileForm');
        const btnSubmit = document.getElementById('btnUpdateProfile');
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');
        const profileAvatarCircle = document.getElementById('profileAvatarCircle');
        const profileCameraBadge = document.getElementById('profileCameraBadge');
        const removeAvatarCheck = document.getElementById('remove_avatar');
        const croppedAvatarData = document.getElementById('croppedAvatarData');
        const defaultAvatarUrl = @json($user->avatar_url);

        // Cropper elements
        const cropModalEl = document.getElementById('cropImageModal');
        const cropModal = cropModalEl && window.bootstrap ? new bootstrap.Modal(cropModalEl) : null;
        const cropImageTarget = document.getElementById('cropImageTarget');
        const btnApplyCrop = document.getElementById('btnApplyCrop');
        const btnZoomIn = document.getElementById('btnZoomIn');
        const btnZoomOut = document.getElementById('btnZoomOut');
        const btnRotateLeft = document.getElementById('btnRotateLeft');
        const btnRotateRight = document.getElementById('btnRotateRight');
        const btnResetCrop = document.getElementById('btnResetCrop');

        let cropper = null;
        let cropApplied = false;

        // Click on profile rounded-circle or camera badge triggers file upload dialog
        if (profileAvatarCircle && avatarInput) {
            profileAvatarCircle.addEventListener('click', function () {
                avatarInput.click();
            });
        }
        if (profileCameraBadge && avatarInput) {
            profileCameraBadge.addEventListener('click', function () {
                avatarInput.click();
            });
        }

        // Handle file selection -> validate & open crop modal
        if (avatarInput && cropImageTarget) {
            avatarInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;

                // Extract file extension and convert to lowercase
                const fileName = file.name;
                const ext = fileName.split('.').pop().toLowerCase();
                const allowedExts = ['png', 'jpg', 'jpeg', 'webp'];

                if (!allowedExts.includes(ext)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unsupported File Format',
                        text: 'Only PNG, JPG, JPEG, and WEBP files are allowed (including .PNG, .JPG, .JPEG, .WEBP).'
                    });
                    this.value = '';
                    return;
                }

                // Check 5MB size limit
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Profile photo size must not exceed 5MB.'
                    });
                    this.value = '';
                    return;
                }

                cropApplied = false;

                // Read file to open in cropper modal
                const reader = new FileReader();
                reader.onload = function (e) {
                    cropImageTarget.src = e.target.result;
                    if (cropModal) {
                        cropModal.show();
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        // Initialize Cropper when modal opens
        if (cropModalEl && cropImageTarget) {
            cropModalEl.addEventListener('shown.bs.modal', function () {
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropImageTarget, {
                    aspectRatio: 1, // 1:1 circle/square avatar ratio
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.9,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    preview: '.crop-live-preview',
                    responsive: true
                });
            });

            cropModalEl.addEventListener('hidden.bs.modal', function () {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                // If modal closed without applying crop and nothing staged, clear input
                if (!cropApplied && croppedAvatarData && !croppedAvatarData.value) {
                    if (avatarInput) avatarInput.value = '';
                }
            });
        }

        // Cropper toolbar controls
        if (btnZoomIn) {
            btnZoomIn.addEventListener('click', function () {
                if (cropper) cropper.zoom(0.1);
            });
        }
        if (btnZoomOut) {
            btnZoomOut.addEventListener('click', function () {
                if (cropper) cropper.zoom(-0.1);
            });
        }
        if (btnRotateLeft) {
            btnRotateLeft.addEventListener('click', function () {
                if (cropper) cropper.rotate(-90);
            });
        }
        if (btnRotateRight) {
            btnRotateRight.addEventListener('click', function () {
                if (cropper) cropper.rotate(90);
            });
        }
        if (btnResetCrop) {
            btnResetCrop.addEventListener('click', function () {
                if (cropper) cropper.reset();
            });
        }

        // Apply Cropped Image
        if (btnApplyCrop) {
            btnApplyCrop.addEventListener('click', function () {
                if (!cropper) return;

                const canvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 500,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                if (!canvas) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Crop Failed',
                        text: 'Could not generate cropped image. Please try again.'
                    });
                    return;
                }

                // 1. DataURL for instant preview and fallback input
                const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.92);
                if (avatarPreview) {
                    avatarPreview.src = croppedDataUrl;
                }
                if (croppedAvatarData) {
                    croppedAvatarData.value = croppedDataUrl;
                }
                if (removeAvatarCheck) {
                    removeAvatarCheck.checked = false;
                }

                // 2. Also populate standard File on avatarInput using DataTransfer
                canvas.toBlob(function (blob) {
                    if (blob && window.DataTransfer && avatarInput) {
                        try {
                            const dt = new DataTransfer();
                            const croppedFile = new File([blob], 'cropped_avatar.jpg', { type: 'image/jpeg' });
                            dt.items.add(croppedFile);
                            avatarInput.files = dt.files;
                        } catch (err) {
                            console.warn('DataTransfer could not update file input, using croppedAvatarData', err);
                        }
                    }
                }, 'image/jpeg', 0.92);

                cropApplied = true;
                if (cropModal) {
                    cropModal.hide();
                }

                // Toast notification
                Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                }).fire({
                    icon: 'success',
                    title: 'Profile photo cropped! Click "Update Profile" to save.'
                });
            });
        }

        // Handle "Remove current photo" toggle
        if (removeAvatarCheck && avatarPreview) {
            removeAvatarCheck.addEventListener('change', function () {
                if (this.checked) {
                    if (avatarInput) avatarInput.value = '';
                    if (croppedAvatarData) croppedAvatarData.value = '';
                    avatarPreview.src = "https://ui-avatars.com/api/?name={{ urlencode($user->name ?: 'User') }}&background=16a34a&color=ffffff&size=256&bold=true";
                } else {
                    avatarPreview.src = defaultAvatarUrl;
                }
            });
        }

        // SweetAlert2 Interactive Confirmation Popup
        if (btnSubmit) {
            btnSubmit.addEventListener('click', function (e) {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                Swal.fire({
                    title: 'Update Profile?',
                    text: 'Are you sure you want to save these profile changes?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-check-circle me-1"></i>Yes, Update!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }

        // SweetAlert2 Toast for Flash Session Success
        @if(Session::has('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: "{!! session('success') !!}",
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endpush