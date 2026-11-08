@extends('layout.backend')

@section('content')
<main>
    <div class="container-fluid px-4">
        <!-- Page Title & Breadcrumbs -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 mb-3">
            <div>
                <h1 class="text-primary fw-bold mb-1">
                    <i class="bi bi-pencil-square me-2"></i>{{ __('Edit Product') }}
                </h1>
                <p class="text-muted small mb-0">Modify product details, pricing, category, and images.</p>
            </div>
            <div class="d-flex gap-2 mt-2 mt-sm-0">
                <a class="btn btn-outline-info" href="{{ route('product.show', $product->id) }}">
                    <i class="bi bi-eye me-1"></i>{{ __('View') }}
                </a>
                <a class="btn btn-outline-secondary" href="{{ route('product.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Products') }}
                </a>
            </div>
        </div>

        <ol class="breadcrumb mb-4 bg-light p-2 rounded-3">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-speedometer2 me-1"></i>{{ __('Dashboard') }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('product.index') }}" class="text-decoration-none">
                    <i class="bi bi-grid me-1"></i>{{ __('Products') }}
                </a>
            </li>
            <li class="breadcrumb-item active">
                <i class="bi bi-pencil me-1"></i>Edit: {{ $product->name }}
            </li>
        </ol>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-secondary">
                    <i class="bi bi-box-seam me-2"></i>Product Information Form
                </h5>
                <span class="badge bg-primary-subtle text-primary border px-3 py-2">
                    Editing Product #{{ $product->id }}
                </span>
            </div>

            <div class="card-body p-4">
                {{-- Form Error List --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                            <strong>Please correct the following errors:</strong>
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{!! $error !!}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="editProductForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Category Select -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold">
                                {{ __('Category') }} <span class="text-danger">*</span>:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="bi bi-folder"></i>
                                </span>
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- {{ __('Category') }} --</option>
                                    @foreach($categories as $id => $name)
                                        @php
                                            $catId = is_object($name) ? $name->id : $id;
                                            $catName = is_object($name) ? $name->name : $name;
                                        @endphp
                                        <option value="{{ $catId }}" {{ (string)old('category_id', $product->category_id) === (string)$catId ? 'selected' : '' }}>
                                            {{ $catName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Product Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">
                                {{ __('Name') }} <span class="text-danger">*</span>:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="bi bi-tag"></i>
                                </span>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" placeholder="Enter product name" required>
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Price -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-semibold">
                                {{ __('Price') }} ($) <span class="text-danger">*</span>:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="bi bi-currency-dollar"></i>
                                </span>
                                <input type="number" step="0.01" min="0" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" placeholder="0.00" required>
                            </div>
                            @error('price')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload Field -->
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label fw-semibold">
                                Change Product Image <span class="text-muted fw-normal">(Optional)</span>:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="bi bi-upload"></i>
                                </span>
                                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            </div>
                            <small class="text-muted d-block mt-1">
                                Leave blank to keep existing image. Supported: JPG, PNG, GIF, WEBP (Max 5MB).
                            </small>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Current & New Image Visual Previews -->
                    <div class="row mb-3 g-3">
                        <!-- Current Image Display -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light bg-opacity-50 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-semibold text-secondary small">
                                        <i class="bi bi-image me-1"></i>Current Image
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary border">Loaded</span>
                                </div>
                                <div class="text-center p-2 bg-white rounded border">
                                    <img id="currentImageDisplay" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded shadow-sm" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                </div>
                                <div class="text-muted small mt-2 text-center text-truncate">
                                    File: <code>{{ $product->image ?: 'default' }}</code>
                                </div>
                            </div>
                        </div>

                        <!-- New Selected Image Preview (Dynamic) -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light bg-opacity-50 h-100" id="newImagePreviewBox">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-semibold text-secondary small">
                                        <i class="bi bi-sparkles me-1"></i>New Image Preview
                                    </span>
                                    <span class="badge bg-info-subtle text-info border" id="newImageBadge">No file chosen</span>
                                </div>
                                <div class="text-center p-2 bg-white rounded border d-flex align-items-center justify-content-center" style="min-height: 140px;">
                                    <div id="noNewImagePlaceholder" class="text-muted small">
                                        <i class="bi bi-cloud-arrow-up fs-2 d-block text-secondary opacity-50 mb-1"></i>
                                        Select a new file above to preview changes
                                    </div>
                                    <img id="imagePreview" src="#" alt="New Image Preview" class="rounded shadow-sm d-none" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                </div>
                                <div class="text-center mt-2 d-none" id="cancelNewImageContainer">
                                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" id="btnCancelNewImage">
                                        <i class="bi bi-x-circle me-1"></i>Clear selection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">
                            {{ __('Description') }} <span class="text-danger">*</span>:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="bi bi-card-text"></i>
                            </span>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter product description" required>{{ old('description', $product->description) }}</textarea>
                        </div>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                        <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnSubmit">
                            <i class="bi bi-check-lg me-1"></i>{{ __('Update') }}
                        </button>
                        <a class="btn btn-outline-info" href="{{ route('product.show', $product->id) }}">
                            <i class="bi bi-eye me-1"></i>{{ __('View') }}
                        </a>
                        <a class="btn btn-outline-danger" href="{{ route('product.index') }}">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editProductForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const noNewImagePlaceholder = document.getElementById('noNewImagePlaceholder');
        const newImageBadge = document.getElementById('newImageBadge');
        const cancelNewImageContainer = document.getElementById('cancelNewImageContainer');
        const btnCancelNewImage = document.getElementById('btnCancelNewImage');

        // Dynamic Image Preview Handler
        if (imageInput) {
            imageInput.addEventListener('change', function () {
                const [file] = this.files;
                if (file) {
                    imagePreview.src = URL.createObjectURL(file);
                    imagePreview.classList.remove('d-none');
                    noNewImagePlaceholder.classList.add('d-none');
                    newImageBadge.textContent = 'Selected: ' + file.name;
                    newImageBadge.className = 'badge bg-success-subtle text-success border';
                    cancelNewImageContainer.classList.remove('d-none');
                } else {
                    resetNewImagePreview();
                }
            });
        }

        function resetNewImagePreview() {
            if (imageInput) imageInput.value = '';
            if (imagePreview) {
                imagePreview.src = '';
                imagePreview.classList.add('d-none');
            }
            if (noNewImagePlaceholder) noNewImagePlaceholder.classList.remove('d-none');
            if (newImageBadge) {
                newImageBadge.textContent = 'No file chosen';
                newImageBadge.className = 'badge bg-info-subtle text-info border';
            }
            if (cancelNewImageContainer) cancelNewImageContainer.classList.add('d-none');
        }

        if (btnCancelNewImage) {
            btnCancelNewImage.addEventListener('click', resetNewImagePreview);
        }

        // SweetAlert2 Confirmation Dialog on Save
        if (btnSubmit) {
            btnSubmit.addEventListener('click', function (e) {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Update Product?',
                        text: 'Are you sure you want to save modifications for ' + @json($product->name) + '?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#dc3545',
                        confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Yes, Save Changes',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('Are you sure you want to save modifications?')) {
                        form.submit();
                    }
                }
            });
        }

        // Flash Messages via SweetAlert2
        @if(session('product_update'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    html: @json(session('product_update')),
                    timer: 3500,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            }
        @endif

        @if(session('product_create'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Created!',
                    html: @json(session('product_create')),
                    timer: 3500,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            }
        @endif
    });
</script>
@endpush
