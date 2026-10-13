@extends('layout.frontend')


@push('styles')
<style>
    .product-title { color: #0f172a; }
    .product-desc { color: #475569; }
    .stepper-wrap {
        display: flex; align-items: center; border: 1.5px solid #e2e8f0;
        border-radius: 10px; overflow: hidden;
    }
    .stepper-btn {
        width: 38px; height: 42px; border: none; background: #f8fafc;
        font-size: 1.1rem; cursor: pointer; color: #475569; transition: background .18s;
    }
    .stepper-btn:hover { background: #f0fdf4; color: #16a34a; }
    .stepper-input {
        width: 50px; text-align: center; border: none; outline: none;
        font-size: .9rem; font-weight: 600; color: #0f172a; background: #fff;
    }
    .back-products-link { color: #64748b; }
    .back-products-link:hover { color: #16a34a; }

    html[data-bs-theme="dark"] .product-title,
    html[data-theme="dark"] .product-title {
        color: #f8fafc !important;
    }
    html[data-bs-theme="dark"] .product-desc,
    html[data-theme="dark"] .product-desc {
        color: #cbd5e1 !important;
    }
    html[data-bs-theme="dark"] .stepper-wrap,
    html[data-theme="dark"] .stepper-wrap {
        border-color: #24344d;
    }
    html[data-bs-theme="dark"] .stepper-btn,
    html[data-theme="dark"] .stepper-btn {
        background: #131c2e; color: #cbd5e1;
    }
    html[data-bs-theme="dark"] .stepper-btn:hover,
    html[data-theme="dark"] .stepper-btn:hover {
        background: #1e293b; color: #4ade80;
    }
    html[data-bs-theme="dark"] .stepper-input,
    html[data-theme="dark"] .stepper-input {
        background: #0b1320; color: #f8fafc;
    }
    html[data-bs-theme="dark"] .back-products-link,
    html[data-theme="dark"] .back-products-link {
        color: #94a3b8 !important;
    }
</style>
@endpush

@section('content')
<div class="container" style="padding-top:40px; padding-bottom:64px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size:.82rem;">
            <li class="breadcrumb-item"><a href="{{ url('/list') }}" style="color:#16a34a">Products</a></li>
            @if($product->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('frontend.category', $product->category->id) }}" style="color:#16a34a">
                        {{ $product->category->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5 align-items-start">

        {{-- Product image --}}
        <div class="col-md-6">
            <div class="position-relative" style="border-radius:20px; overflow:hidden; background:#f8fafc; border:1px solid #f1f5f9;">
                <img src="{{ $product->image_url }}"
                     alt="{{ $product->name }}"
                     class="img-fluid w-100"
                     style="aspect-ratio:4/3; object-fit:cover;">
                @auth
                <form action="{{ route('favorites.toggle', $product->id) }}" method="POST" class="position-absolute top-0 end-0 m-3" style="z-index: 5;">
                    @csrf
                    <button type="submit" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center border-0"
                            style="width: 42px; height: 42px; background: rgba(255,255,255,0.92);"
                            title="{{ $product->isFavoritedBy(Auth::user()) ? 'Remove from favorites' : 'Save to favorites' }}">
                        <i class="bi {{ $product->isFavoritedBy(Auth::user()) ? 'bi-heart-fill text-danger' : 'bi-heart text-muted' }}" style="font-size: 1.25rem;"></i>
                    </button>
                </form>
                @endauth
            </div>
        </div>

        {{-- Product info --}}
        <div class="col-md-6">
            @if($product->category)
                <span style="background:#f0fdf4; color:#16a34a; border:1.5px solid #bbf7d0;
                             border-radius:20px; font-size:.75rem; font-weight:600;
                             padding:3px 12px; display:inline-block; margin-bottom:14px;">
                    {{ $product->category->name }}
                </span>
            @endif

            <div style="font-size:.8rem; color:#94a3b8; margin-bottom:6px;">SKU: #{{ $product->id }}</div>
            <h1 class="product-title" style="font-size:2rem; font-weight:800; margin-bottom:12px;">
                {{ $product->name }}
            </h1>

            <div style="font-size:2rem; font-weight:700; color:#16a34a; margin-bottom:20px;">
                ${{ number_format($product->price, 2) }}
            </div>

            <p class="product-desc" style="font-size:.92rem; line-height:1.7; margin-bottom:28px;">
                {{ $product->description ?: 'No description available.' }}
            </p>

            {{-- FIX: Add to cart now uses a proper link with quantity passed via URL / session
                 The original was a dead <button> with no form or route --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="stepper-wrap">
                    <button onclick="stepQty(-1)" type="button" class="stepper-btn">−</button>
                    <input type="number" id="qtyInput" value="1" min="1" max="99" class="stepper-input">
                    <button onclick="stepQty(1)" type="button" class="stepper-btn">+</button>
                </div>
                <a id="addToCartBtn"
                   href="{{ route('add.to.cart', $product->id) }}"
                   class="btn-add-cart d-inline-flex align-items-center gap-2"
                   style="flex:1; justify-content:center; padding:11px 24px; font-size:.9rem; border-radius:12px;">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </a>
            </div>

            <a href="{{ url('/list') }}" class="back-products-link"
               style="font-size:.83rem; text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function stepQty(delta) {
        const input = document.getElementById('qtyInput');
        let val = parseInt(input.value || '1') + delta;
        if (val < 1) val = 1;
        if (val > 99) val = 99;
        input.value = val;
    }

    document.getElementById('qtyInput').addEventListener('input', function () {
        let val = Math.max(1, Math.min(99, parseInt(this.value || '1')));
        this.value = val;
    });

    document.getElementById('addToCartBtn').addEventListener('click', function () {
        const url = new URL(this.href, window.location.origin);
        url.searchParams.set('quantity', document.getElementById('qtyInput').value || '1');
        this.href = url.toString();
    });
</script>
@endpush