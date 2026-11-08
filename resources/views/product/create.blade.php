@extends('layout.backend')

@section('content')
<main>
    <div class="container-fluid px-4">
        <!-- Page Title & Breadcrumbs -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 mb-3">
            <div>
                <h1 class="text-primary fw-bold mb-1">
                    <i class="bi bi-plus-square me-2"></i>{{ __('Create Product') }}
                </h1>
                <p class="text-muted small mb-0">{{ __('Add a new item to your store catalog.') }}</p>
            </div>
            <div class="mt-2 mt-sm-0">
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
                <i class="bi bi-plus-square me-1"></i>{{ __('Create Product') }}
            </li>
        </ol>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold text-secondary">
                    <i class="bi bi-pencil-square me-2"></i>{{ __('Product Details') }}
                </h5>
            </div>
            <div class="card-body p-4">
                {{-- Form Error List --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                            <strong>Please fix the following errors:</strong>
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{!! $error !!}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" id="createProductForm">
                    @csrf
                    
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
                                        <option value="{{ $catId }}" {{ (string)old('category_id') === (string)$catId ? 'selected' : '' }}>
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
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter product name" required>
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
                                <input type="number" step="0.01" min="0" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0.00" required>
                            </div>
                            @error('price')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label fw-semibold">
                                {{ __('Image') }} <span class="text-danger">*</span>:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="bi bi-image"></i>
                                </span>
                                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                            </div>
                            <small class="text-muted d-block mt-1">Supported formats: JPG, PNG, GIF, WEBP (Max 5MB).</small>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Image Preview Placeholder -->
                    <div class="mb-3 d-none" id="imagePreviewContainer">
                        <label class="form-label fw-semibold text-secondary small">Image Preview:</label>
                        <div class="p-2 border rounded bg-light bg-opacity-50 d-inline-block">
                            <img id="imagePreview" src="#" alt="Preview" class="rounded shadow-sm" style="max-height: 140px; max-width: 100%; object-fit: contain;">
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
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter product description" required>{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Button Group -->
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSubmit">
                            <i class="bi bi-check-lg me-1"></i>{{ __('Create Product') }}
                        </button>
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
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');

        if (imageInput) {
            imageInput.addEventListener('change', function () {
                const [file] = this.files || [];
                if (file) {
                    imagePreview.src = URL.createObjectURL(file);
                    imagePreviewContainer.classList.remove('d-none');
                } else {
                    imagePreview.src = '';
                    imagePreviewContainer.classList.add('d-none');
                }
            });
        }

        // Trigger SweetAlert2 on Success Session Flash
        @if(session('product_create'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: @json(session('product_create')),
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            }
        @endif
    });
</script>
@endpush
