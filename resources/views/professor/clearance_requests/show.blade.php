@extends('layouts.professor_admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
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

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('professor.clearance-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Requests
                        </a>
                        
                        @if($clearanceRequest->isPending())
                        <div>
                            <a href="{{ route('professor.clearance-requests.edit', $clearanceRequest) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Request
                            </a>
                            <form action="{{ route('professor.clearance-requests.destroy', $clearanceRequest) }}" 
                                  method="POST" class="d-inline ml-2"
                                  onsubmit="return confirm('Are you sure you want to delete this request?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete Request
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
