@extends('layouts.professor_admin')

@section('content')
<h1 class="mb-4">My Leave Requests</h1>

<a href="{{ route('professor.leave.create') }}" class="btn btn-primary mb-3">
    <i class="bi bi-plus-circle"></i> New Leave Request
</a>

<div class="card">
    <div class="card-body">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $r)
                <tr>
                    <td>{{ ucfirst($r->type) }}</td>
                    <td>{{ $r->start_date }} - {{ $r->end_date }}</td>
                    <td>
                        <span class="badge bg-{{ $r->status == 'approved' ? 'success' : ($r->status == 'rejected' ? 'danger' : 'secondary') }}">{{ ucfirst($r->status) }}</span>
                    </td>
                    <td>{{ $r->reason }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No leave requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection



