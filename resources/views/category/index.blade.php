@extends('layout.backend')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 text-primary fw-bold">
            <i class="bi bi-folder2-open me-2"></i>Category List
        </h4>
        <a class="btn btn-primary" href="{{ url('/category/create') }}">
            <i class="bi bi-plus-lg me-1"></i>New Category
        </a>
    </div>

    <div class="card-body p-0">
        @if (count($categories) > 0)
            <div class="px-3 py-2 border-bottom bg-light bg-opacity-50 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="input-group input-group-sm" style="max-width: 360px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="categoryTableFilter" class="form-control border-start-0" placeholder="Filter categories by ID or name..." autocomplete="off">
                </div>
                <span id="categoryTableFilterCount" class="badge bg-secondary-subtle text-secondary border fw-medium px-2 py-1">
                    {{ count($categories) }} categories
                </span>
            </div>
            <div class="table-responsive">
                <table id="categoryTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="80">ID</th>
                            <th>NAME</th>
                            <th class="text-center" width="180">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                        <tr>
                            <td class="text-center fw-bold text-secondary">
                                #{!! $category->id !!}
                            </td>
                            <td>
                                <a href="{{ url('/category/' . $category->id) }}" class="text-decoration-none fw-semibold link-primary">
                                    <i class="bi bi-folder me-1 text-warning"></i>{!! $category->name !!}
                                </a>
                            </td>
                            <td class="text-center">
                                <!-- Action Button Group -->
                                <div class="" role="" aria-label="Category Actions">
                                    <a class="btn btn-sm btn-outline-primary" href="{!! url('/category/' . $category->id . '/edit') !!}" title="Edit Category">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-info" href="{!! url('/category/' . $category->id) !!}" title="View Category">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <!-- Delete Form -->
                                    <form action="{{ route('category.delete', $category->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Delete Category">
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
                <p class="text-muted mt-2 mb-0">No categories found.</p>
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
        // SweetAlert2 Confirmation on Delete Button Click
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function (e) {
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Delete category?',
                    text: 'This category will be deleted permanently.',
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
        // Automatic Instant Table Search as soon as characters are typed
        const categoryTableFilter = document.getElementById('categoryTableFilter');
        const categoryTableFilterCount = document.getElementById('categoryTableFilterCount');
        const categoryRows = document.querySelectorAll('#categoryTable tbody tr');

        if (categoryTableFilter) {
            categoryTableFilter.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                let matched = 0;

                categoryRows.forEach(row => {
                    if (row.id === 'categoryNoMatchRow') return;
                    const text = row.innerText.toLowerCase();
                    const match = !q || text.includes(q);
                    row.style.display = match ? '' : 'none';
                    if (match) matched++;
                });

                if (categoryTableFilterCount) {
                    categoryTableFilterCount.textContent = q 
                        ? `${matched} of ${categoryRows.length} matches` 
                        : `${categoryRows.length} categories`;
                }

                let noMatchRow = document.getElementById('categoryNoMatchRow');
                if (matched === 0 && q) {
                    if (!noMatchRow) {
                        noMatchRow = document.createElement('tr');
                        noMatchRow.id = 'categoryNoMatchRow';
                        noMatchRow.innerHTML = `<td colspan="3" class="text-center py-4 text-muted"><i class="bi bi-search me-1"></i>No categories found matching "${this.value}"</td>`;
                        document.querySelector('#categoryTable tbody').appendChild(noMatchRow);
                    }
                    noMatchRow.style.display = '';
                } else if (noMatchRow) {
                    noMatchRow.style.display = 'none';
                }
            });
        }

        // Trigger SweetAlert2 Toast on Success Flash Message
        @if(Session::has('category_delete'))
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                html: "{!! session('category_delete') !!}",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endpush