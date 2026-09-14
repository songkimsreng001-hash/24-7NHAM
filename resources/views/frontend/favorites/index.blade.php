@extends('layout.frontend')

@section('title', 'My Favorites - 24/7 NHAM')

@section('content')
<div class="container py-4 py-md-5">
    
    <!-- Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">
                <i class="bi bi-heart-fill text-danger me-2"></i> My Favorite Items
            </h2>
            <p class="text-muted small mb-0">Your saved food and drinks for quick 1-click re-ordering.</p>
        </div>
        <a href="{{ url('/list') }}" class="btn btn-outline-success btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Browse More Items
        </a>
    </div>

    <!-- Favorites Grid -->
    @if($favorites->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body py-5">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-danger" style="width: 80px; height: 80px; font-size: 2.2rem;">
                        <i class="bi bi-heart"></i>
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Saved Favorites</h5>
                <p class="text-muted small mb-3">You haven't saved any food items yet. Click the heart icon on any dish to save it here!</p>
                <a href="{{ url('/list') }}" class="btn btn-success rounded-pill px-4 fw-semibold">
                    Explore Delicious Menu
                </a>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
            @foreach($favorites as $fav)
                @php $product = $fav->product; @endphp
                @if($product)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative product-card transition-all">
                            
                            <!-- Remove Favorite Button -->
                            <form action="{{ route('favorites.toggle', $product->id) }}" method="POST" class="position-absolute top-0 end-0 m-2" style="z-index: 5;">
                                @csrf
                                <button type="submit" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger p-0" 
                                        style="width: 34px; height: 34px;" title="Remove from favorites">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            </form>

                            <!-- Product Image -->
                            <div class="bg-light text-center p-3" style="height: 180px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                @if($product->image)
                                    <img src="{{ $product->image_url }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-fluid" 
                                         style="max-height: 150px; object-fit: contain;">
                                @else
                                    <i class="bi bi-cup-hot text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                @endif
                            </div>

                            <!-- Card Body -->
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge bg-light text-success fw-bold text-uppercase" style="font-size: 0.68rem;">
                                            {{ $product->category ? $product->category->name : 'Food' }}
                                        </span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </h6>
                                    <p class="text-muted small mb-2 text-truncate" style="font-size: 0.78rem;">
                                        {{ $product->description ?: 'Fresh & delicious from 24/7 NHAM.' }}
                                    </p>
                                </div>

                                <div>
                                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                        <span class="fw-bolder text-success fs-5">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                        <a href="{{ route('add.to.cart', $product->id) }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">
                                            <i class="bi bi-cart-plus me-1"></i> Add
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="d-flex justify-content-center">
            {{ $favorites->links() }}
        </div>
    @endif

</div>
@endsection

