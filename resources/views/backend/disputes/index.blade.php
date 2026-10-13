@extends('layout.backend')


@section('content')
<div class="page-header">
    <div>
        <h1>Dispute & Complaint Resolution</h1>
        <p>Review customer complaints regarding missing items, delivery delays, or driver disputes.</p>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">
            <i class="bi bi-shield-exclamation text-danger me-1"></i> Customer Disputes ({{ $disputes->total() }})
        </h3>
        <span class="panel-badge">Support Queue</span>
    </div>

    @if($disputes->isEmpty())
        <div class="empty-state py-5 text-center">
            <i class="bi bi-shield-check text-success fs-1 mb-2 d-block"></i>
            <h6 class="fw-bold text-dark">Zero Open Disputes</h6>
            <p class="text-muted small">No complaints have been reported by customers.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light text-muted small">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Subject & Issue</th>
                        <th>Assigned Courier</th>
                        <th>Status</th>
                        <th>Filed Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disputes as $dispute)
                        <tr>
                            <td>
                                <a href="{{ route('customer.orders.show', $dispute->order_id) }}" target="_blank" class="fw-bold text-success">
                                    #{{ $dispute->order_id }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $dispute->user ? $dispute->user->name : 'Customer' }}</div>
                                <div class="text-muted small">{{ $dispute->user ? $dispute->user->email : '' }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $dispute->subject }}</div>
                                <p class="text-muted small mb-0 text-truncate" style="max-width: 320px;" title="{{ $dispute->description }}">
                                    {{ $dispute->description }}
                                </p>
                                @if($dispute->resolution_notes)
                                    <div class="text-success small mt-1">
                                        <i class="bi bi-arrow-return-right me-1"></i><strong>Resolution:</strong> {{ $dispute->resolution_notes }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($dispute->order && $dispute->order->driver)
                                    <span class="fw-semibold text-dark">{{ $dispute->order->driver->name }}</span>
                                @else
                                    <span class="text-muted small">None</span>
                                @endif
                            </td>
                            <td>
                                @if($dispute->status === 'resolved')
                                    <span class="badge bg-success">Resolved</span>
                                @elseif($dispute->status === 'investigating')
                                    <span class="badge bg-warning text-dark">Investigating</span>
                                @else
                                    <span class="badge bg-danger">Open</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $dispute->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#resolveModal{{ $dispute->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> Update
                                </button>

                                <!-- Resolve Modal -->
                                <div class="modal fade text-start" id="resolveModal{{ $dispute->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.disputes.resolve', $dispute->id) }}" method="POST" class="modal-content">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-dark">Update Dispute #{{ $dispute->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Customer Complaint</label>
                                                    <div class="p-2.5 bg-light rounded-3 small text-secondary">
                                                        <strong>{{ $dispute->subject }}:</strong> {{ $dispute->description }}
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Resolution Status <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select form-select-sm" required>
                                                        <option value="investigating" {{ $dispute->status === 'investigating' ? 'selected' : '' }}>Under Investigation</option>
                                                        <option value="resolved" {{ $dispute->status === 'resolved' ? 'selected' : '' }}>Resolved & Closed</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Resolution Note to Customer <span class="text-danger">*</span></label>
                                                    <textarea name="resolution_notes" class="form-control form-control-sm" rows="3" required placeholder="Describe refund issued, replacement food dispatched, or resolution explanation...">{{ $dispute->resolution_notes }}</textarea>
                                                    <div class="form-text small">This message is dispatched to the customer's in-app notification center.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success btn-sm fw-bold">Save & Notify Customer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $disputes->links() }}
        </div>
    @endif
</div>
@endsection

