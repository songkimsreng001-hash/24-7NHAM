@extends('layout.frontend')
@section('page-title', ($selectedCategory ? $selectedCategory->name . ' — ' : '') . 'Categories — 24/7 NHAM')

@section('content')

{{-- Category pills --}}
<div class="cat-pill-row">
    <div class="container">
        <div class="cat-pill-scroll">
            <a href="{{ route('frontend.categories') }}"
               class="cat-pill {{ is_null($selectedCategory) ? 'active' : '' }}">
                All Categories
            </a>
            @foreach($categories as $category)
                <a href="{{ route('frontend.category', $category->id) }}"
                   class="cat-pill {{ optional($selectedCategory)->id === $category->id ? 'active' : '' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<div class="container">
    <div class="section-header">
        <h2>{{ $selectedCategory ? $selectedCategory->name : 'All Categories' }}</h2>
        <p>
            @if($selectedCategory)
                Showing products in <strong>{{ $selectedCategory->name }}</strong>.
                <a href="{{ route('frontend.categories') }}" style="color:#16a34a">View all</a>
            @else
                Browse products by category.
            @endif
        </p>
    </div>

    @if($products->isEmpty())
        <div class="empty-state">
            <i class="fas fa-tags"></i>
            <h5>No products in this category</h5>
            <p><a href="{{ route('frontend.categories') }}" style="color:#16a34a">Browse all categories</a></p>
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

        @if(method_exists($products, 'links'))
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection