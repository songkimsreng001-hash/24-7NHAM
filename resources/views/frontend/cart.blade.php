@extends('layout.frontend')
@section('page-title', 'Cart — 24/7 NHAM')

@push('styles')
<style>
    .cart-wrap { padding: 40px 0 80px; }
    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table th {
        font-size: .72rem; font-weight: 600; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .05em;
        padding: 0 16px 14px; text-align: left;
        border-bottom: 2px solid #f1f5f9;
    }
    .cart-table td {
        padding: 18px 16px; border-bottom: 1px solid #f8fafc;
        vertical-align: middle; font-size: .88rem; color: #374151;
    }
    .cart-table tr:last-child td { border-bottom: none; }
    .cart-table tr:hover td { background: #fafafa; }

    .cart-product-img {
        width: 72px; height: 72px; border-radius: 12px;
        object-fit: cover; border: 1px solid #f1f5f9;
    }
    .cart-product-name { font-weight: 600; color: #0f172a; font-size: .9rem; }
    .qty-input {
        width: 68px; text-align: center;
        border: 1.5px solid #e2e8f0; border-radius: 9px;
        padding: 7px 8px; font-size: .88rem; font-weight: 600;
        color: #0f172a; outline: none;
        transition: border-color .18s;
    }
    .qty-input:focus { border-color: #16a34a; }
    .btn-remove {
        width: 34px; height: 34px; border-radius: 8px;
        border: 1.5px solid #fee2e2; background: #fff;
        color: #ef4444; font-size: .85rem;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .18s;
    }
    .btn-remove:hover { background: #fee2e2; border-color: #ef4444; }

    /* Order summary panel */
    .summary-panel {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        position: sticky; top: 80px;
    }
    .summary-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
    .summary-row {
        display: flex; justify-content: space-between;
        font-size: .88rem; padding: 8px 0;
        border-bottom: 1px solid #f8fafc; color: #475569;
    }
    .summary-row:last-of-type { border-bottom: none; }
    .summary-total {
        display: flex; justify-content: space-between;
        font-size: 1.05rem; font-weight: 700; color: #0f172a;
        margin-top: 14px; padding-top: 14px;
        border-top: 2px solid #f1f5f9;
    }
    .btn-checkout {
        display: block; width: 100%; text-align: center;
        background: #16a34a; color: #fff;
        border: none; border-radius: 12px;
        padding: 13px; font-size: .9rem; font-weight: 700;
        text-decoration: none; margin-top: 18px;
        transition: background .18s;
    }
    .btn-checkout:hover { background: #166534; color: #fff; }
    .btn-continue {
        display: block; width: 100%; text-align: center;
        background: #fff; color: #475569;
        border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: 11px; font-size: .88rem; font-weight: 600;
        text-decoration: none; margin-top: 10px;
        transition: all .18s;
    }
    .btn-continue:hover { border-color: #16a34a; color: #16a34a; }

    /* Empty cart */
    .empty-cart {
        text-align: center; padding: 80px 24px; color: #94a3b8;
        background: #fff; border-radius: 20px; border: 1px solid #f1f5f9;
    }
    .empty-cart i { font-size: 3.5rem; margin-bottom: 18px; opacity: .35; }
    .empty-cart h4 { font-weight: 700; color: #475569; margin-bottom: 8px; }
    .empty-cart p { font-size: .87rem; margin-bottom: 22px; }
</style>
@endpush

@section('content')
<div class="container cart-wrap">
    <div class="mb-4">
        <h2 style="font-size:1.65rem; font-weight:700; color:#0f172a;">Shopping Cart</h2>
        <p style="font-size:.85rem; color:#64748b;">
            @php $cartItems = session('cart', []); $itemCount = count($cartItems); @endphp
            {{ $itemCount }} {{ Str::plural('item', $itemCount) }} in your cart
        </p>
    </div>

    @if($itemCount === 0)
        <div class="empty-cart">
            <i class="bi bi-cart-x"></i>
            <h4>Your cart is empty</h4>
            <p>Add some products to get started.</p>
            <a href="{{ url('/list') }}" class="btn-add-cart d-inline-flex align-items-center gap-2"
               style="border-radius:12px; padding:11px 28px; font-size:.88rem;">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    @else
        <div class="row g-4 align-items-start">

            {{-- Cart table --}}
            <div class="col-lg-8">
                <div style="background:#fff; border-radius:20px; border:1px solid #f1f5f9; box-shadow:0 2px 8px rgba(0,0,0,.04); overflow:hidden;">
                    <div style="overflow-x:auto;">
                        @php $total = 0; @endphp
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $id => $details)
                                    @php $subtotal = $details['price'] * $details['quantity']; $total += $subtotal; @endphp
                                    <tr data-id="{{ $id }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                {{-- FIX: was img/{{ image }} (missing asset()), broke on some servers --}}
                                                @php
                                                    $cartImg = $details['image_url'] ?? (
                                                        !empty($details['image'])
                                                            ? (str_starts_with($details['image'], 'http') ? $details['image'] : asset('img/' . $details['image']))
                                                            : asset('img/logo.png')
                                                    );
                                                @endphp
                                                <img src="{{ $cartImg }}"
                                                     alt="{{ $details['name'] }}" class="cart-product-img">
                                                <div class="cart-product-name">{{ $details['name'] }}</div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($details['price'], 2) }}</td>
                                        <td>
                                            <input type="number" value="{{ $details['quantity'] }}"
                                                   min="1" max="99"
                                                   class="qty-input update-cart">
                                        </td>
                                        <td style="font-weight:700; color:#0f172a;">
                                            ${{ number_format($subtotal, 2) }}
                                        </td>
                                        <td>
                                            <button class="btn-remove remove-from-cart" title="Remove">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="col-lg-4">
                <div class="summary-panel">
                    <div class="summary-title">Order Summary</div>
                    @foreach($cartItems as $id => $details)
                        <div class="summary-row">
                            <span>{{ Str::limit($details['name'], 24) }} × {{ $details['quantity'] }}</span>
                            <span>${{ number_format($details['price'] * $details['quantity'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span style="color:#16a34a; font-weight:600;">Free</span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span id="cartTotal">${{ number_format($total, 2) }}</span>
                    </div>
                    <a href="{{ route('cart.checkout') }}" class="btn-checkout">
                        <i class="fas fa-lock me-2"></i> Checkout
                    </a>
                    <a href="{{ url('/list') }}" class="btn-continue">
                        <i class="fas fa-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection

@push('scripts')
{{-- FIX: Cart scripts moved here so @stack('scripts') in layout picks them up.
     Original had them inline in @section('content') with no @stack in the old layout. --}}
<script>
    $(".update-cart").on("change", function () {
        var ele = $(this);
        $.ajax({
            url: '{{ route("update.cart") }}',
            method: "PATCH",
            data: {
                _token: '{{ csrf_token() }}',
                id: ele.closest("tr").attr("data-id"),
                quantity: ele.val()
            },
            success: function () {
                Swal.fire({toast:true,position:'top-end',icon:'success',title:'Cart updated',showConfirmButton:false,timer:1200});
                setTimeout(function(){ window.location.reload(); }, 500);
            },
            error: function () {
                Swal.fire({icon:'error',title:'Unable to update cart',text:'Please try again.'});
            }
        });
    });

    $(".remove-from-cart").on("click", function () {
        var ele = $(this);
        Swal.fire({
            title: "Remove this item?",
            text: "The item will be removed from your cart.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, remove it"
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '{{ route("remove.from.cart") }}',
                method: "DELETE",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: ele.closest("tr").attr("data-id")
                },
                success: function () {
                    Swal.fire({toast:true,position:'top-end',icon:'success',title:'Item removed',showConfirmButton:false,timer:1200});
                    setTimeout(function(){ window.location.reload(); }, 500);
                },
                error: function () {
                    Swal.fire({icon:'error',title:'Unable to remove item',text:'Please try again.'});
                }
            });
        });
    });
</script>
@endpush