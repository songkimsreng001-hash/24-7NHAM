@extends('layout.backend')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header & Breadcrumbs -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="text-primary fw-bold mb-1">
                <i class="bi bi-box-seam me-2"></i>{{ __('Product Catalog') }}
            </h1>
            <p class="text-muted small mb-0">{{ __("Manage your store's inventory, prices, categories, and descriptions.") }}</p>
        </div>
        <div class="mt-2 mt-sm-0">
            <a class="btn btn-primary shadow-sm" href="{{ route('product.create') }}">
                <i class="bi bi-plus-lg me-1"></i>{{ __('New Product') }}
            </a>
        </div>
    </div>

    <ol class="breadcrumb mb-4 bg-light p-2 rounded-3">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                <i class="bi bi-speedometer2 me-1"></i>{{ __('Dashboard') }}
            </a>
        </li>
        <li class="breadcrumb-item active">
            <i class="bi bi-grid me-1"></i>{{ __('Products') }}
        </li>
    </ol>

    <!-- Main Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0 fw-semibold text-secondary">
                <i class="bi bi-list-ul me-2"></i>{{ __('Product List') }}
            </h5>
            <span id="productTableFilterCount" class="badge bg-primary-subtle text-primary border px-3 py-2">
                {{ count($products) }} {{ __('total products') }}
            </span>
        </div>

        <div class="card-body p-0">
            <!-- Filter Toolbar: Search Input + Category Filter Dropdown -->
            <div class="px-3 py-3 border-bottom bg-light bg-opacity-50">
                <div class="row g-2 align-items-center">
                    <!-- Text Search -->
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="productTableFilter" class="form-control border-start-0" placeholder="{{ __('Search by name, price, description...') }}" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary d-none" id="btnClearSearch" title="Clear search">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-secondary">
                                <i class="bi bi-funnel me-1"></i>{{ __('Category') }}:
                            </span>
                            <select id="productCategoryFilter" class="form-select">
                                <option value="">{{ __('All Categories') }} ({{ count($products) }})</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string)request('category_id') === (string)$category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->products_count ?? $category->products->count() }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Reset Filters Button -->
                    <div class="col-12 col-sm-6 col-md-3 text-sm-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 w-sm-auto d-none" id="btnResetFilters">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Clear filter') }}
                        </button>
                    </div>
                </div>
            </div>

            @if (count($products) > 0)
                <div class="table-responsive">
                    <table id="myTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="70" class="text-center">#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th class="text-center" width="200">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $index => $product)
                            <tr class="product-row" 
                                data-id="{{ $product->id }}"
                                data-category-id="{{ $product->category_id }}"
                                data-category-name="{{ strtolower($product->category->name ?? 'uncategorized') }}"
                                data-name="{{ strtolower($product->name) }}"
                                data-price="{{ $product->price }}"
                                data-search="{{ strtolower($product->name . ' ' . ($product->category->name ?? '') . ' ' . $product->price . ' ' . strip_tags($product->description)) }}">
                                <td class="text-center text-muted small fw-semibold">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('product.show', $product->id) }}" class="me-3 position-relative d-inline-block">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded img-thumbnail shadow-sm" style="width:52px; height:52px; object-fit:cover;" />
                                        </a>
                                        <div>
                                            <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none fw-semibold link-primary d-block">
                                                {{ $product->name }}
                                            </a>
                                            <span class="text-muted small">ID: #{{ $product->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($product->category)
                                        <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none filter-category-link" data-category-id="{{ $product->category_id }}" title="Click to filter by this category">
                                            <i class="bi bi-folder me-1"></i>{{ $product->category->name }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">
                                    ${{ number_format((float)$product->price, 2) }}
                                </td>
                                <td class="text-muted small" style="max-width: 280px;">
                                    <div class="text-truncate" title="{{ strip_tags($product->description) }}">
                                        {{ Str::limit(strip_tags($product->description), 75) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <!-- Action Buttons -->
                                    <div class="d-inline-flex gap-1" role="group" aria-label="Product Actions">
                                        <!-- View Full Details -->
                                        <a class="btn btn-sm btn-outline-info" href="{{ route('product.show', $product->id) }}" title="View Product Details">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- Quick View Modal Trigger -->
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-quick-view" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#quickViewModal"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-category="{{ $product->category->name ?? 'Uncategorized' }}"
                                            data-price="{{ number_format((float)$product->price, 2) }}"
                                            data-image="{{ $product->image_url }}"
                                            data-description="{{ e($product->description) }}"
                                            data-created="{{ $product->created_at ? $product->created_at->format('M d, Y h:i A') : 'N/A' }}"
                                            data-show-url="{{ route('product.show', $product->id) }}"
                                            data-edit-url="{{ route('product.edit', $product->id) }}"
                                            title="Quick Preview">
                                            <i class="bi bi-zoom-in"></i>
                                        </button>
                                        
                                        <!-- Edit Product -->
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('product.edit', $product->id) }}" title="Edit Product">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <!-- Delete Product -->
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
                    <p class="text-muted mt-2 mb-3">No products found in the catalog.</p>
                    <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Add First Product
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick View Product Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title fw-bold text-primary" id="quickViewModalLabel">
                    <i class="bi bi-eye me-2"></i>Quick Product View
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row align-items-center g-4">
                    <div class="col-md-5 text-center">
                        <div class="p-2 border rounded bg-light bg-opacity-50">
                            <img id="qvImage" src="" alt="Product Image" class="img-fluid rounded shadow-sm" style="max-height: 240px; width: 100%; object-fit: contain;">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-light text-primary border" id="qvCategory">Category</span>
                            <span class="fs-5 fw-bold text-success" id="qvPrice">$0.00</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2" id="qvName">Product Name</h4>
                        <div class="text-muted small mb-3">
                            <i class="bi bi-clock me-1"></i>Created: <span id="qvCreated">N/A</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Description</label>
                            <p class="text-secondary small p-3 bg-light rounded border mb-0" id="qvDescription" style="max-height: 140px; overflow-y: auto; white-space: pre-wrap;"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    Close
                </button>
                <div class="d-flex gap-2">
                    <a id="qvFullPageBtn" href="#" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Full Page
                    </a>
                    <a id="qvEditBtn" href="#" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit Product
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('productTableFilter');
        const categoryFilter = document.getElementById('productCategoryFilter');
        const filterCount = document.getElementById('productTableFilterCount');
        const btnResetFilters = document.getElementById('btnResetFilters');
        const btnClearSearch = document.getElementById('btnClearSearch');
        const rows = document.querySelectorAll('.product-row');
        const totalRows = rows.length;

        // Quick View Modal elements
        const qvModal = document.getElementById('quickViewModal');
        const qvImage = document.getElementById('qvImage');
        const qvName = document.getElementById('qvName');
        const qvCategory = document.getElementById('qvCategory');
        const qvPrice = document.getElementById('qvPrice');
        const qvDescription = document.getElementById('qvDescription');
        const qvCreated = document.getElementById('qvCreated');
        const qvFullPageBtn = document.getElementById('qvFullPageBtn');
        const qvEditBtn = document.getElementById('qvEditBtn');

        // Quick View Modal Data Population
        document.querySelectorAll('.btn-quick-view').forEach(btn => {
            btn.addEventListener('click', function () {
                const name = this.getAttribute('data-name');
                const category = this.getAttribute('data-category');
                const price = this.getAttribute('data-price');
                const image = this.getAttribute('data-image');
                const description = this.getAttribute('data-description');
                const created = this.getAttribute('data-created');
                const showUrl = this.getAttribute('data-show-url');
                const editUrl = this.getAttribute('data-edit-url');

                if (qvName) qvName.textContent = name;
                if (qvCategory) qvCategory.textContent = category;
                if (qvPrice) qvPrice.textContent = '$' + price;
                if (qvImage) qvImage.src = image;
                if (qvDescription) qvDescription.textContent = description || 'No description available.';
                if (qvCreated) qvCreated.textContent = created;
                if (qvFullPageBtn) qvFullPageBtn.href = showUrl;
                if (qvEditBtn) qvEditBtn.href = editUrl;
            });
        });

        // Combined Instant Filtering (Search query + Category dropdown)
        function applyFilters() {
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const selectedCategory = categoryFilter ? categoryFilter.value.trim() : '';

            let visibleCount = 0;

            rows.forEach(row => {
                const rowCategoryId = row.getAttribute('data-category-id') || '';
                const rowSearchData = row.getAttribute('data-search') || '';

                const matchesQuery = !query || rowSearchData.includes(query);
                const matchesCategory = !selectedCategory || (rowCategoryId === selectedCategory);

                if (matchesQuery && matchesCategory) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update badge count
            if (filterCount) {
                if (!query && !selectedCategory) {
                    filterCount.textContent = `${totalRows} total products`;
                    filterCount.className = 'badge bg-primary-subtle text-primary border px-3 py-2';
                } else {
                    filterCount.textContent = `Showing ${visibleCount} of ${totalRows} products`;
                    filterCount.className = 'badge bg-info-subtle text-info border px-3 py-2';
                }
            }

            // Toggle Reset Button
            const isFiltered = (query !== '' || selectedCategory !== '');
            if (btnResetFilters) {
                btnResetFilters.classList.toggle('d-none', !isFiltered);
            }
            if (btnClearSearch) {
                btnClearSearch.classList.toggle('d-none', query === '');
            }

            // Show / hide "No matching products" row
            let noMatchRow = document.getElementById('productNoMatchRow');
            if (visibleCount === 0 && totalRows > 0) {
                if (!noMatchRow) {
                    noMatchRow = document.createElement('tr');
                    noMatchRow.id = 'productNoMatchRow';
                    noMatchRow.innerHTML = `
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-3 d-block opacity-50 mb-2"></i>
                            <div>No products match your filter criteria.</div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btnInnerReset">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset All Filters
                            </button>
                        </td>
                    `;
                    document.querySelector('#myTable tbody').appendChild(noMatchRow);
                    const innerReset = document.getElementById('btnInnerReset');
                    if (innerReset) innerReset.addEventListener('click', resetFilters);
                }
                noMatchRow.style.display = '';
            } else if (noMatchRow) {
                noMatchRow.style.display = 'none';
            }
        }

        function resetFilters() {
            if (searchInput) searchInput.value = '';
            if (categoryFilter) categoryFilter.value = '';
            applyFilters();
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        if (categoryFilter) {
            categoryFilter.addEventListener('change', applyFilters);
        }

        if (btnClearSearch) {
            btnClearSearch.addEventListener('click', function () {
                if (searchInput) searchInput.value = '';
                applyFilters();
            });
        }

        if (btnResetFilters) {
            btnResetFilters.addEventListener('click', resetFilters);
        }

        // Quick click on table category badge filters by that category
        document.querySelectorAll('.filter-category-link').forEach(link => {
            link.addEventListener('click', function () {
                const catId = this.getAttribute('data-category-id');
                if (categoryFilter) {
                    categoryFilter.value = catId;
                    applyFilters();
                }
            });
        });

        // Initial filter application if category_id URL param was preset
        if (categoryFilter && categoryFilter.value !== '') {
            applyFilters();
        }

        // SweetAlert2 Confirmation for Product Deletion
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');
                const row = this.closest('tr');
                const productName = row ? row.querySelector('td:nth-child(2) a').textContent.trim() : 'this product';
                
                Swal.fire({
                    title: 'Delete Product?',
                    text: `Are you sure you want to permanently delete "${productName}"?`,
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

        // Flash Notification Toasts
        const flashMessages = <?php echo json_encode([
            'product_delete' => session('product_delete'),
            'product_update' => session('product_update'),
            'product_create' => session('product_create'),
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        Object.entries(flashMessages).forEach(([key, message]) => {
            if (!message) return;

            const titles = {
                product_delete: 'Deleted!',
                product_update: 'Updated!',
                product_create: 'Created!'
            };

            Swal.fire({
                icon: 'success',
                title: titles[key] || 'Success!',
                html: message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        });
    });
</script>
@endpush
