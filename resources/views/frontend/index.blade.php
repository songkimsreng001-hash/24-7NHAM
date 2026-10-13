@extends('layout.frontend')


@section('content')

{{-- Hero --}}
<div class="hero-strip">
    <div class="container">
        <h1>Fresh picks, delivered fast</h1>
        <p>Discover the latest food, drinks, and more from 24/7 NHAM.</p>
        <!-- <form class="hero-search" action="{{ url('/search') }}" method="GET">
            <input type="text" name="keyword" placeholder="Search products…" value="{{ old('keyword', request('keyword')) }}">
            <button type="submit"><i class="fas fa-search me-1"></i> Search</button>
        </form> -->
    </div>
</div>

{{-- Category pills --}}
@if(isset($categories) && $categories->count())
<div class="cat-pill-row">
    <div class="container">
        <div class="cat-pill-scroll">
            <a href="{{ url('/list') }}" class="cat-pill active">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('frontend.category', $cat->id) }}" class="cat-pill">{{ $cat->name }}</a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Products grid --}}
<div class="container">
    <div class="section-header">
        <h2>Featured Products</h2>
        <p>Discover the latest items available in our store.</p>
    </div>

    @if($products->isEmpty())
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h5>No products yet</h5>
            <p>Check back soon — new items are on their way.</p>
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