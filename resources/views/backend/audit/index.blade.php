@extends('layout.backend')

@section('page-title', 'Security Audit Trail - 24/7 NHAM Admin')

@section('content')
<div class="page-header">
    <div>
        <h1>Security & Operations Audit Trail</h1>
        <p>Immutable activity log of administrative decisions, courier approvals, order cancellations, and broadcasts.</p>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">
            <i class="bi bi-journal-text text-primary me-1"></i> Recorded Events ({{ $logs->total() }})
        </h3>
        <span class="panel-badge">Compliance Log</span>
    </div>

    @if($logs->isEmpty())
        <div class="empty-state py-5 text-center">
            <i class="bi bi-clock text-muted fs-1 mb-2 d-block"></i>
            <h6 class="fw-bold text-dark">No Audit Events Logged</h6>
            <p class="text-muted small">Administrative actions will automatically record here.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light text-muted small">
                    <tr>
                        <th>Timestamp</th>
                        <th>Admin User</th>
                        <th>Action Type</th>
                        <th>Target Model & ID</th>
                        <th>Details / Rationale</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td class="text-muted small">
                                {{ $log->created_at->format('M d, Y h:i:s A') }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $log->admin ? $log->admin->name : 'System' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $log->admin ? $log->admin->email : '' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.78rem;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                @if($log->auditable_type)
                                    <span class="text-secondary small">
                                        {{ $log->auditable_type }} #{{ $log->auditable_id }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-dark small">{{ $log->details }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-center">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

