@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Clearance Requests Management</h4>
                    <div>
                        <a href="{{ route('admin.clearance-requests.export', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Export
                        </a>
                        <a href="{{ route('admin.clearance-requests.dashboard') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="clearance_type" class="form-control">
                                    <option value="">All Types</option>
                                    @foreach($clearanceTypes as $key => $name)
                                        <option value="{{ $key }}" {{ request('clearance_type') == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="faculty_id" class="form-control">
                                    <option value="">All Faculty</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.clearance-requests.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($requests->count() > 0)
                        <!-- Bulk Actions -->
                        <div class="mb-3">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" onclick="bulkApprove()">
                                    <i class="fas fa-check"></i> Bulk Approve
                                </button>
                                <button type="button" class="btn btn-danger" onclick="bulkReject()">
                                    <i class="fas fa-times"></i> Bulk Reject
                                </button>
                            </div>
                            <small class="text-muted ml-2">Select requests using checkboxes to perform bulk actions</small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>Faculty</th>
                                        <th>Clearance Type</th>
                                        <th>Status</th>
                                        <th>Requested Date</th>
                                        <th>Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="request-checkbox" value="{{ $request->id }}">
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $request->faculty->name }}</strong><br>
                                                    <small class="text-muted">{{ $request->faculty->professor_id }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $request->clearance_type_name }}</td>
                                            <td>
                                                <span class="badge {{ $request->status_badge_class }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $request->requested_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                <div style="max-width: 200px;">
                                                    {{ Str::limit($request->reason, 100) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.clearance-requests.show', $request) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($request->isPending())
                                                        <button type="button" class="btn btn-sm btn-success" 
                                                                onclick="quickApprove({{ $request->id }})">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                onclick="quickReject({{ $request->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $requests->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No clearance requests found</h5>
                            <p class="text-muted">No requests match your current filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Approve Modal -->
<div class="modal fade" id="bulkApproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkApproveForm" method="POST" action="{{ route('admin.clearance-requests.bulk-approve') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Approve Requests</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve the selected requests?</p>
                    <div class="form-group">
                        <label>Admin Remarks (Optional)</label>
                        <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Optional remarks for all approved requests..."></textarea>
                    </div>
                    <div id="selectedRequestsList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkRejectForm" method="POST" action="{{ route('admin.clearance-requests.bulk-reject') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Reject Requests</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject the selected requests?</p>
                    <div class="form-group">
                        <label>Admin Remarks <span class="text-danger">*</span></label>
                        <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Please provide reason for rejection..." required></textarea>
                    </div>
                    <div id="selectedRequestsListReject"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.request-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all when individual checkboxes change
    document.querySelectorAll('.request-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allCheckboxes = document.querySelectorAll('.request-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.request-checkbox:checked');
            document.getElementById('selectAll').checked = allCheckboxes.length === checkedCheckboxes.length;
        });
    });
});

function getSelectedRequests() {
    const selected = [];
    document.querySelectorAll('.request-checkbox:checked').forEach(checkbox => {
        selected.push(checkbox.value);
    });
    return selected;
}

function bulkApprove() {
    const selected = getSelectedRequests();
    if (selected.length === 0) {
        alert('Please select at least one request to approve.');
        return;
    }
    
    // Add hidden inputs for selected requests
    const form = document.getElementById('bulkApproveForm');
    const existingInputs = form.querySelectorAll('input[name="request_ids[]"]');
    existingInputs.forEach(input => input.remove());
    
    selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'request_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.getElementById('selectedRequestsList').innerHTML = `<p><strong>${selected.length}</strong> requests selected for approval.</p>`;
    $('#bulkApproveModal').modal('show');
}

function bulkReject() {
    const selected = getSelectedRequests();
    if (selected.length === 0) {
        alert('Please select at least one request to reject.');
        return;
    }
    
    // Add hidden inputs for selected requests
    const form = document.getElementById('bulkRejectForm');
    const existingInputs = form.querySelectorAll('input[name="request_ids[]"]');
    existingInputs.forEach(input => input.remove());
    
    selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'request_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.getElementById('selectedRequestsListReject').innerHTML = `<p><strong>${selected.length}</strong> requests selected for rejection.</p>`;
    $('#bulkRejectModal').modal('show');
}

function quickApprove(requestId) {
    if (confirm('Are you sure you want to approve this request?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/clearance-requests/${requestId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function quickReject(requestId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason && reason.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/clearance-requests/${requestId}/reject`;
        
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
