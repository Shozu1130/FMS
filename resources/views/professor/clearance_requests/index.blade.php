@extends('layouts.professor_admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Clearance Requests</h4>
                    <a href="{{ route('professor.clearance-requests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Request
                    </a>
                </div>
                <div class="card-body">
                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Clearance Type</th>
                                        <th>Status</th>
                                        <th>Requested Date</th>
                                        <th>Processed Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>{{ $request->clearance_type_name }}</td>
                                            <td>
                                                <span class="badge {{ $request->status_badge_class }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $request->requested_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                {{ $request->processed_at ? $request->processed_at->format('M d, Y h:i A') : 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('professor.clearance-requests.show', $request) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if($request->isPending())
                                                        <a href="{{ route('professor.clearance-requests.edit', $request) }}" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <form action="{{ route('professor.clearance-requests.destroy', $request) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this request?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No clearance requests found</h5>
                            <p class="text-muted">You haven't submitted any clearance requests yet.</p>
                            <a href="{{ route('professor.clearance-requests.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Submit Your First Request
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $requests->where('status', 'pending')->count() }}</h4>
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
                                    <h4>{{ $requests->where('status', 'approved')->count() }}</h4>
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
                                    <h4>{{ $requests->where('status', 'rejected')->count() }}</h4>
                                    <p class="mb-0">Rejected</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $requests->count() }}</h4>
                                    <p class="mb-0">Total</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
