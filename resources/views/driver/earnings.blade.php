@extends('driver.layout')

@section('title', 'Earnings & History - 24/7 NHAM Courier')

@section('content')
<div class="row g-3">
    
    <!-- Earnings Overview Cards -->
    <div class="col-12">
        <h4 class="fw-bolder text-dark mb-3">
            <i class="bi bi-wallet2 text-success me-2"></i> Courier Earnings & Payouts
        </h4>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="text-muted small fw-semibold mb-1">Today's Earnings</div>
                    <div class="fs-2 fw-bolder text-success mb-0">${{ number_format($todayEarnings, 2) }}</div>
                    <div class="text-muted small mt-1">From deliveries completed today</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="text-muted small fw-semibold mb-1">This Week's Earnings</div>
                    <div class="fs-2 fw-bolder text-primary mb-0">${{ number_format($weekEarnings, 2) }}</div>
                    <div class="text-muted small mt-1">Starting from Monday this week</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="text-muted small fw-semibold mb-1">Lifetime Total Earnings</div>
                    <div class="fs-2 fw-bolder text-dark mb-0">${{ number_format($totalEarnings, 2) }}</div>
                    <div class="text-muted small mt-1">Standard rate: $2.50 base + 10% delivery fee</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivered Orders Log Table -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-1 text-secondary"></i> Completed Delivery Log
                </h6>
                <span class="badge bg-light text-dark border">{{ $deliveredOrders->total() }} Total Completed</span>
            </div>

            @if($deliveredOrders->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-receipt-cutoff fs-2 mb-2"></i>
                    <p class="small mb-0">No completed deliveries yet. Accept jobs from dashboard to begin earning!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Order #</th>
                                <th>Completed At</th>
                                <th>Customer & Drop-off</th>
                                <th>Order Value</th>
                                <th>Courier Payout</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliveredOrders as $ord)
                                @php $payout = 2.50 + round($ord->amount * 0.10, 2); @endphp
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark">#{{ $ord->id }}</span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $ord->delivered_at ? $ord->delivered_at->format('M d, Y h:i A') : $ord->updated_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $ord->user ? $ord->user->name : 'Customer' }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 250px;">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $ord->shipping_address }}
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        ${{ number_format($ord->amount, 2) }}
                                        <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.7rem;">
                                            {{ strtoupper($ord->payment_method ?? 'COD') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bolder text-success fs-6">+${{ number_format($payout, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($ord->review)
                                            <span class="text-warning fw-bold">
                                                <i class="bi bi-star-fill"></i> {{ $ord->review->driver_rating }}★
                                            </span>
                                            @if($ord->review->comment)
                                                <div class="text-muted small fst-italic text-truncate" style="max-width: 150px;" title="{{ $ord->review->comment }}">
                                                    "{{ $ord->review->comment }}"
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $deliveredOrders->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

