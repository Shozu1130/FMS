@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Clearances</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.clearance.create') }}" class="btn btn-purple mb-3">
        <i class="bi bi-plus"></i> New Clearance
    </a>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Faculty</th>
                        <th>Clearance Type</th>
                        <th>Issued Date</th>
                        <th>Expiration Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clearances as $clearance)
                    <tr>
                        <td>{{ $clearance->faculty->name }}</td>
                        <td>{{ $clearance->clearance_type }}</td>
                        <td>{{ $clearance->issued_date->format('Y-m-d') }}</td>
                        <td>{{ $clearance->expiration_date ? $clearance->expiration_date->format('Y-m-d') : 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $clearance->is_cleared ? 'success' : 'secondary' }}">
                                {{ $clearance->is_cleared ? 'Cleared' : 'Pending' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.clearance.edit', $clearance->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.clearance.destroy', $clearance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this clearance?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No clearances yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $clearances->links() }}
        </div>
    </div>
</div>
@endsection
