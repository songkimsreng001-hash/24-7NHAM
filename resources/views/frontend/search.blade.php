@extends('layout.frontend')


@section('content')
<div class="container">
    <div class="section-header">
        <h2>Search Products</h2>
        <p>Find exactly what you're looking for.</p>
    </div>

    {{-- Search form with instant auto-search --}}
    <form action="{{ url('/search') }}" method="GET" class="mb-5 position-relative" id="mainSearchForm" style="max-width:560px;" autocomplete="off">
        <div class="input-group" style="border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <input type="text" name="keyword" id="mainSearchInput"
                   value="{{ old('keyword', $keyword ?? '') }}"
                   class="form-control border-end-0"
                   style="border-radius:12px 0 0 12px; border-color:#e2e8f0; font-size:.9rem; padding:12px 16px;"
                   placeholder="Search products by name…" autocomplete="off">
            <button type="submit" class="btn"
                    style="background:#16a34a; color:#fff; border-radius:0 12px 12px 0; padding:12px 20px; font-weight:600; font-size:.88rem; border:none;">
                <i class="fas fa-search me-1"></i> Search
            </button>
        </div>

        {{-- Instant dropdown suggestions --}}
        <div id="mainSearchResults" class="navbar-search-results shadow-lg" style="margin-top: 4px; border-radius: 12px;"></div>
    </form>

    {{-- Results --}}
    @if(isset($keyword) && $keyword !== '')
        @php $total = method_exists($products, 'total') ? $products->total() : $products->count(); @endphp
        <p class="text-muted mb-4" style="font-size:.85rem;">
            Showing results for <strong>"{{ $keyword }}"</strong>
            — {{ $total }} {{ Str::plural('product', $total) }} found
        </p>
    @endif

    @if($products->isEmpty())
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h5>{{ isset($keyword) && $keyword ? 'No results for "' . $keyword . '"' : 'Start searching' }}</h5>
            <p>Try different keywords or <a href="{{ url('/list') }}" style="color:#16a34a">browse all products</a>.</p>
        </div>
    @else
        <div class="row g-4 row-cols-2 row-cols-md-3 row-cols-xl-4">
            @foreach($products as $product)
            <div class="col">
                <div class="product-card">
                    <div class="card-img-wrap position-relative">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        @auth
                        <form action="{{ route('favorites.toggle', $product->id) }}" method="POST" class="position-absolute top-0 end-0 m-2" style="z-index: 5;">
                            @csrf
                            <button type="submit" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center border-0"
                                    style="width: 32px; height: 32px; background: rgba(255,255,255,0.92);"
                                    title="{{ $product->isFavoritedBy(Auth::user()) ? 'Remove from favorites' : 'Save to favorites' }}">
                                <i class="bi {{ $product->isFavoritedBy(Auth::user()) ? 'bi-heart-fill text-danger' : 'bi-heart text-muted' }}" style="font-size: 0.9rem;"></i>
                            </button>
                        </form>
                        @endauth
                    </div>
                    <div class="card-body">
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="product-desc">{{ Str::limit($product->description, 70) }}</div>
                        <div class="product-price">${{ number_format($product->price, 2) }}</div>
                    </div>
                    <div class="card-footer-actions">
                        <a href="{{ url('/show/' . $product->id) }}" class="btn-view-product">
                            <i class="fas fa-eye me-1"></i> View
                        </a>
                        {{-- FIX: was href="#" (dead link), now routes to add.to.cart --}}
                        <a href="{{ route('add.to.cart', $product->id) }}" class="btn-add-cart">
                            <i class="fas fa-cart-plus me-1"></i> Add to Cart
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            @if(method_exists($products, 'links'))
                {{ $products->links('pagination::bootstrap-5') }}
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('mainSearchInput');
    const results = document.getElementById('mainSearchResults');
    if (!input || !results) return;

    let timer = null;

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const safe = escapeHtml(text);
        const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return safe.replace(new RegExp(`(${escapedQuery})`, 'gi'), '<strong class="text-success">$1</strong>');
    }

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(timer);

        if (!q) {
            results.innerHTML = '';
            results.classList.remove('show');
            return;
        }

        results.innerHTML = `
            <div class="navbar-search-loading py-3 text-center text-muted small">
                <i class="bi bi-arrow-repeat me-1 spin"></i> Searching...
            </div>`;
        results.classList.add('show');

        timer = setTimeout(function () {
            fetch(`{{ url('/navbar-search') }}?title=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(items => {
                results.innerHTML = '';
                if (!items.length) {
                    results.innerHTML = `
                        <div class="navbar-search-empty py-3 text-center text-muted small">
                            <i class="bi bi-search d-block mb-1"></i> No matching products found for "${escapeHtml(q)}"
                        </div>`;
                    results.classList.add('show');
                    return;
                }

                items.forEach(p => {
                    const row = document.createElement('a');
                    row.href = p.url;
                    row.className = 'navbar-search-result text-decoration-none';
                    const img = p.image
                        ? `<img src="${escapeHtml(p.image)}" alt="" class="rounded object-fit-cover" style="width:36px;height:36px;" onerror="this.onerror=null;this.src='{{ asset('img/logo.png') }}';">`
                        : `<div class="navbar-search-result-icon"><i class="bi bi-box-seam"></i></div>`;
                    row.innerHTML = `
                        ${img}
                        <div class="navbar-search-result-info">
                            <span class="navbar-search-result-title">${highlightMatch(p.title, q)}</span>
                            <span class="navbar-search-result-price">$${escapeHtml(p.price)}</span>
                        </div>
                        <i class="bi bi-chevron-right text-muted ms-auto small"></i>
                    `;
                    results.appendChild(row);
                });
                results.classList.add('show');
            })
            .catch(() => {
                results.classList.remove('show');
            });
        }, 120);
    });

    input.addEventListener('focus', function () {
        if (this.value.trim().length >= 1 && results.innerHTML.trim()) {
            results.classList.add('show');
        }
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            results.classList.remove('show');
        }
    });

    document.addEventListener('click', function (e) {
        if (!document.getElementById('mainSearchForm')?.contains(e.target)) {
            results.classList.remove('show');
        }
    });
});
</script>
@endpush