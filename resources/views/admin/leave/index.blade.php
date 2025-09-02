@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Leave Requests</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Type</th>
                        <th>Dates</th>
                        <th>Attachment</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $r)
                    <tr>
                        <td>{{ $r->faculty->name }} ({{ $r->faculty->professor_id }})</td>
                        <td>{{ ucfirst($r->type) }}</td>
                        <td>{{ $r->start_date }} - {{ $r->end_date }}</td>
                        <td>
                            @if($r->file_path)
                                <a href="{{ asset('storage/' . $r->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View PDF</a>
                            @else
                                No attachment
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $r->status == 'approved' ? 'success' : ($r->status == 'rejected' ? 'danger' : 'secondary') }}">{{ ucfirst($r->status) }}</span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.leave.update', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to approve this leave request?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button class="btn btn-sm btn-success" {{ $r->status == 'approved' ? 'disabled' : '' }}>Approve</button>
                            </form>
                            <form action="{{ route('admin.leave.update', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this leave request?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-sm btn-danger" {{ $r->status == 'rejected' ? 'disabled' : '' }}>Reject</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No leave requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection



