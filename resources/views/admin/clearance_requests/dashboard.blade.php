@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h4 class="mb-4">Clearance Requests Dashboard</h4>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total Requests</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clipboard-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>{{ $stats['pending'] }}</h3>
                                    <p class="mb-0">Pending</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>{{ $stats['approved'] }}</h3>
                                    <p class="mb-0">Approved</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>{{ $stats['rejected'] }}</h3>
                                    <p class="mb-0">Rejected</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Pending Requests (Priority) -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pending Requests (Requires Action)</h5>
                            <span class="badge badge-warning">{{ $pendingRequests->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($pendingRequests->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($pendingRequests as $request)
                                        <div class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">{{ $request->faculty->name }}</div>
                                                <small class="text-muted">{{ $request->clearance_type_name }}</small><br>
                                                <small class="text-muted">{{ $request->requested_at->diffForHumans() }}</small>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.clearance-requests.show', $request) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.clearance-requests.index', ['status' => 'pending']) }}" 
                                       class="btn btn-warning">
                                        <i class="fas fa-list"></i> View All Pending
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <p class="text-muted">No pending requests!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Requests</h5>
                        </div>
                        <div class="card-body">
                            @if($recentRequests->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($recentRequests as $request)
                                        <div class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">{{ $request->faculty->name }}</div>
                                                <small class="text-muted">{{ $request->clearance_type_name }}</small><br>
                                                <small class="text-muted">{{ $request->requested_at->diffForHumans() }}</small>
                                            </div>
                                            <div>
                                                <span class="badge {{ $request->status_badge_class }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.clearance-requests.index') }}" class="btn btn-primary">
                                        <i class="fas fa-list"></i> View All Requests
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No requests yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('admin.clearance-requests.index', ['status' => 'pending']) }}" 
                                       class="btn btn-warning btn-block">
                                        <i class="fas fa-clock"></i><br>
                                        Review Pending<br>
                                        <small>({{ $stats['pending'] }} requests)</small>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('admin.clearance-requests.index') }}" 
                                       class="btn btn-primary btn-block">
                                        <i class="fas fa-list"></i><br>
                                        All Requests<br>
                                        <small>({{ $stats['total'] }} total)</small>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('admin.clearance-requests.export') }}" 
                                       class="btn btn-success btn-block">
                                        <i class="fas fa-download"></i><br>
                                        Export Data<br>
                                        <small>(CSV format)</small>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-info btn-block" onclick="refreshStats()">
                                        <i class="fas fa-sync-alt"></i><br>
                                        Refresh Stats<br>
                                        <small>(Update dashboard)</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshStats() {
    location.reload();
}

// Auto-refresh every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endsection
