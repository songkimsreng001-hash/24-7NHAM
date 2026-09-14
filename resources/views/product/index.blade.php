@extends('layout.backend')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 text-primary fw-bold">
            <i class="bi bi-box-seam me-2"></i>Product List
        </h4>
        <a class="btn btn-primary" href="{{ url('/product/create') }}">
            <i class="bi bi-plus-lg me-1"></i>New Product
        </a>
    </div>

    <div class="card-body p-0">
        @if (count($products) > 0)
            <div class="px-3 py-2 border-bottom bg-light bg-opacity-50 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="input-group input-group-sm" style="max-width: 360px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="productTableFilter" class="form-control border-start-0" placeholder="Filter products by name, description, price..." autocomplete="off">
                </div>
                <span id="productTableFilterCount" class="badge bg-secondary-subtle text-secondary border fw-medium px-2 py-1">
                    {{ count($products) }} products
                </span>
            </div>
            <div class="table-responsive">
                <table id="myTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NAME</th>
                            <th>DESCRIPTION</th>
                            <th class="text-center" width="120">IMAGE</th>
                            <th>PRICE</th>
                            <th class="text-center" width="180">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                        <tr>
                            <td>
                                <a href="{{ url('/product/'.$product->id) }}" class="text-decoration-none fw-semibold link-primary">
                                    <i class="bi bi-tag me-1 text-secondary"></i>{{ $product->name }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                {!! $product->description !!}
                            </td>
                            <td class="text-center">
                                <img src="{{ asset('img/'.$product->image) }}" alt="{{ $product->name }}" class="rounded img-thumbnail shadow-sm" style="width:60px; height:60px; object-fit:cover;" />
                            </td>
                            <td class="fw-bold text-success">
                                ${{ number_format((float)$product->price, 2) }}
                            </td>
                            <td class="text-center">
                                <!-- Action Button Group -->
                                <div class="" aria-label="Product Actions">
                                    <a class="btn btn-sm btn-outline-primary" href="{!! url('product/' . $product->id . '/edit') !!}" title="Edit Product">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')

                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Delete Product">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted display-4"></i>
                <p class="text-muted mt-2 mb-0">No products found.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // SweetAlert2 Confirmation for Product Deletion
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function (e) {
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Delete product?',
                    text: 'This product will be permanently deleted.',
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
        });

        // Automatic Instant Table Search as soon as characters are typed
        const productTableFilter = document.getElementById('productTableFilter');
        const productTableFilterCount = document.getElementById('productTableFilterCount');
        const productRows = document.querySelectorAll('#myTable tbody tr');

        if (productTableFilter) {
            productTableFilter.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                let matched = 0;

                productRows.forEach(row => {
                    if (row.id === 'productNoMatchRow') return;
                    const text = row.innerText.toLowerCase();
                    const match = !q || text.includes(q);
                    row.style.display = match ? '' : 'none';
                    if (match) matched++;
                });

                if (productTableFilterCount) {
                    productTableFilterCount.textContent = q 
                        ? `${matched} of ${productRows.length} matches` 
                        : `${productRows.length} products`;
                }

                let noMatchRow = document.getElementById('productNoMatchRow');
                if (matched === 0 && q) {
                    if (!noMatchRow) {
                        noMatchRow = document.createElement('tr');
                        noMatchRow.id = 'productNoMatchRow';
                        noMatchRow.innerHTML = `<td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-search me-1"></i>No products found matching "${this.value}"</td>`;
                        document.querySelector('#myTable tbody').appendChild(noMatchRow);
                    }
                    noMatchRow.style.display = '';
                } else if (noMatchRow) {
                    noMatchRow.style.display = 'none';
                }
            });
        }

        // Trigger SweetAlert2 Toast on Success Session Flash
        @if(Session::has('product_delete'))
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                html: "{!! session('product_delete') !!}",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endpush