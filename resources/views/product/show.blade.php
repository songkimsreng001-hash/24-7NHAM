@extends('layout.backend')

@section('content')
<main>
    <div class="container-fluid px-4">
        <!-- Page Header & Breadcrumbs -->
        <h1 class="mt-4 text-primary fw-bold">
            <i class="bi bi-box-seam me-2"></i>Product Details
        </h1>
        <ol class="breadcrumb mb-4 bg-light p-2 rounded-3">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('product.index') }}" class="text-decoration-none">
                    <i class="bi bi-grid me-1"></i>Products
                </a>
            </li>
            <li class="breadcrumb-item active">
                <i class="bi bi-eye me-1"></i>Show Product
            </li>
        </ol>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-secondary">
                    <i class="bi bi-info-circle me-2"></i>Product #{{ $product->id }} Information
                </h5>
                <span class="badge bg-success fs-6">${{ number_format((float)$product->price, 2) }}</span>
            </div>

            <div class="card-body p-4">
                <div class="row align-items-center">
                    <!-- Product Image -->
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        <div class="p-2 border rounded bg-light">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-sm" style="max-height: 250px; width: 100%; object-fit: cover;" />
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Product Name</label>
                            <h4 class="fw-bold text-dark mb-0">
                                <i class="bi bi-tag me-2 text-primary"></i>{{ $product->name }}
                            </h4>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Category</label>
                            <p class="mb-0 text-dark fw-medium">
                                <span class="badge bg-light text-primary border">
                                    <i class="bi bi-folder me-1"></i>{{ $product->category->name ?? 'N/A' }}
                                </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Price</label>
                            <p class="fs-5 fw-bold text-success mb-0">
                                <i class="bi bi-currency-dollar me-1"></i>{{ number_format((float)$product->price, 2) }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small text-uppercase fw-bold">Description</label>
                            <p class="text-secondary mb-0 p-3 bg-light rounded border">
                                <i class="bi bi-card-text me-2 text-primary"></i>{{ $product->description ?? 'No description available.' }}
                            </p>
                        </div>

                        <!-- Action Button Group -->
                        <div class="btn-group" role="group" aria-label="Product Action Buttons">
                            <a class="btn btn-secondary" href="{{ route('product.index') }}">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>
                            <a class="btn btn-primary" href="{{ url('product/' . $product->id . '/edit') }}">
                                <i class="bi bi-pencil me-1"></i>Edit Product
                            </a>
                            <button type="button" class="btn btn-danger btn-delete" data-id="{{ $product->id }}">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>

                        <!-- Hidden Deletion Form -->
                        <form id="delete-form-{{ $product->id }}" action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
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
        // SweetAlert2 Delete Confirmation
        const deleteBtn = document.querySelector('.btn-delete');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const form = document.getElementById(`delete-form-${productId}`);

                Swal.fire({
                    title: 'Delete Product?',
                    text: 'Are you sure you want to delete this product? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i>Yes, Delete!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endpush