@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Clearance Request Details</h4>
                    <span class="badge {{ $clearanceRequest->status_badge_class }} badge-lg">
                        {{ ucfirst($clearanceRequest->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Request Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Request ID:</strong></td>
                                    <td>#{{ $clearanceRequest->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Clearance Type:</strong></td>
                                    <td>{{ $clearanceRequest->clearance_type_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $clearanceRequest->status_badge_class }}">
                                            {{ ucfirst($clearanceRequest->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Requested Date:</strong></td>
                                    <td>{{ $clearanceRequest->requested_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @if($clearanceRequest->processed_at)
                                <tr>
                                    <td><strong>Processed Date:</strong></td>
                                    <td>{{ $clearanceRequest->processed_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Processed By:</strong></td>
                                    <td>{{ $clearanceRequest->processedBy->name ?? 'N/A' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Faculty Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $clearanceRequest->faculty->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Professor ID:</strong></td>
                                    <td>{{ $clearanceRequest->faculty->professor_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Employment Type:</strong></td>
                                    <td>{{ $clearanceRequest->faculty->employment_type }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>{{ $clearanceRequest->faculty->status }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $clearanceRequest->faculty->email }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted">Reason for Request</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $clearanceRequest->reason }}
                            </div>
                        </div>
                    </div>

                    @if($clearanceRequest->admin_remarks)
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted">Admin Remarks</h6>
                            <div class="alert alert-{{ $clearanceRequest->isApproved() ? 'success' : 'danger' }}">
                                <i class="fas fa-{{ $clearanceRequest->isApproved() ? 'check-circle' : 'exclamation-triangle' }}"></i>
                                {{ $clearanceRequest->admin_remarks }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($clearanceRequest->isPending())
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted">Process Request</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-check"></i> Approve Request</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.clearance-requests.approve', $clearanceRequest) }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label>Admin Remarks (Optional)</label>
                                                    <textarea name="admin_remarks" class="form-control" rows="3" 
                                                              placeholder="Optional remarks for approval..."></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-success btn-block">
                                                    <i class="fas fa-check"></i> Approve Request
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0"><i class="fas fa-times"></i> Reject Request</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.clearance-requests.reject', $clearanceRequest) }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label>Admin Remarks <span class="text-danger">*</span></label>
                                                    <textarea name="admin_remarks" class="form-control" rows="3" 
                                                              placeholder="Please provide reason for rejection..." required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-danger btn-block">
                                                    <i class="fas fa-times"></i> Reject Request
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.clearance-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Requests
                        </a>
                        
                        <div>
                            @if($clearanceRequest->isPending())
                                <button type="button" class="btn btn-success" onclick="quickApprove()">
                                    <i class="fas fa-check"></i> Quick Approve
                                </button>
                                <button type="button" class="btn btn-danger" onclick="quickReject()">
                                    <i class="fas fa-times"></i> Quick Reject
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function quickApprove() {
    if (confirm('Are you sure you want to approve this request?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.clearance-requests.approve", $clearanceRequest) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function quickReject() {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason && reason.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.clearance-requests.reject", $clearanceRequest) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const remarksInput = document.createElement('input');
        remarksInput.type = 'hidden';
        remarksInput.name = 'admin_remarks';
        remarksInput.value = reason;
        form.appendChild(remarksInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
