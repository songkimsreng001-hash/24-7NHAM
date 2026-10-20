@extends('layout.backend')

@section('content')
<main>
    <div class="container-fluid px-4">
        <!-- Page Title & Breadcrumbs -->
        <h1 class="mt-4 text-primary fw-bold">
            <i class="bi bi-box-seam me-2"></i>Create Product
        </h1>
        <ol class="breadcrumb mb-4 bg-light p-2 rounded-3">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active">
                <i class="bi bi-plus-square me-1"></i>Create Product
            </li>
        </ol>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold text-secondary">
                    <i class="bi bi-pencil-square me-2"></i>Product Details Form
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
                    
                    <!-- Category Select -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Category:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="bi bi-folder"></i>
                            </span>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select category</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Product Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Name:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter product name" required>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold">Price:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="bi bi-currency-dollar"></i>
                            </span>
                            <input type="text" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0.00" required>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Image:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="bi bi-image"></i>
                            </span>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Description:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="bi bi-card-text"></i>
                            </span>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter product description" required>{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Action Button Group -->
                    <div aria-label="Product Form Actions">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bi bi-check-lg me-1"></i>Create Product
                        </button>
                        <a class="btn btn-danger" href="{{ route('product.index') }}">
                            <i class="bi bi-arrow-left me-1"></i>Back
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
        // Trigger SweetAlert2 on Success Session Flash
        @if(Session::has('product_create'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: "{!! session('product_create') !!}",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endpush