@extends('layout.frontend')

@section('title', 'Notifications - 24/7 NHAM')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <!-- Page Header -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">
                        <i class="bi bi-bell-fill text-success me-2"></i> In-App Notifications
                    </h2>
                    <p class="text-muted small mb-0">Stay updated on your live food deliveries, driver arrivals, and promotions.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm rounded-3 px-3 fw-semibold">
                            <i class="bi bi-check2-all me-1"></i> Mark All as Read
                        </button>
                    </form>
                </div>
            </div>

            <!-- Notifications Feed -->
            @if($notifications->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="card-body py-5">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-muted" style="width: 80px; height: 80px; font-size: 2.2rem;">
                                <i class="bi bi-bell-slash"></i>
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">No Notifications Yet</h5>
                        <p class="text-muted small mb-3">When you place orders or receive driver updates, alerts will appear here.</p>
                        <a href="{{ url('/list') }}" class="btn btn-success rounded-pill px-4 fw-semibold">
                            Explore Food Menu
                        </a>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notif)
                            <div class="list-group-item p-3 p-md-4 transition-all {{ !$notif->is_read ? 'bg-light bg-opacity-75' : '' }}" style="border-left: 4px solid {{ !$notif->is_read ? 'var(--green-base, #16a34a)' : 'transparent' }};">
                                <div class="d-flex align-items-start gap-3">
                                    
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                             style="width: 44px; height: 44px; background: {{ !$notif->is_read ? '#16a34a' : '#64748b' }};">
                                            <i class="bi {{ $notif->icon ?: 'bi-bell-fill' }} fs-5"></i>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                            <h6 class="fw-bold mb-0 text-dark {{ !$notif->is_read ? 'fw-bolder' : '' }}">
                                                {{ $notif->title }}
                                                @if(!$notif->is_read)
                                                    <span class="badge bg-success ms-1 px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">NEW</span>
                                                @endif
                                            </h6>
                                            <span class="text-muted small">
                                                <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-secondary small mb-2 text-break" style="line-height: 1.5;">
                                            {{ $notif->message }}
                                        </p>

                                        <div class="d-flex flex-wrap align-items-center gap-2 pt-1">
                                            @if($notif->order_id)
                                                <a href="{{ route('customer.orders.show', $notif->order_id) }}" class="btn btn-sm btn-success rounded-3 px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                                                    <i class="bi bi-truck me-1"></i> View Order #{{ $notif->order_id }}
                                                </a>
                                            @endif

                                            @if(!$notif->is_read)
                                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1" style="font-size: 0.78rem;">
                                                        Mark read
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

