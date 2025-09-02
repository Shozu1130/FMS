@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Faculty Management</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.faculty.create') }}" class="btn btn-purple mb-3">
        <i class="bi bi-person-plus"></i> Add New Professor
    </a>

    <div class="card shadow">
        <div class="card-body">
            @if($faculty->count() > 0)
            <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Professor ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Employment Type</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faculty as $prof)
                    <tr>
                        <td>{{ $prof->professor_id }}</td>
                        <td>{{ $prof->name }}</td>
                        <td>{{ $prof->email }}</td>
                        <td>{{ $prof->employment_type ?? 'Full-Time' }}</td>
                        <td>
                            <span class="badge bg-{{ $prof->status == 'active' ? 'success' : 'secondary' }}">
                                {{ $prof->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group" aria-label="Actions">
                                <a href="{{ route('admin.faculty.show', $prof->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.faculty.edit', $prof->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.faculty.destroy', $prof->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this professor? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @if(method_exists($faculty, 'links'))
                <div class="mt-3">
                    {{ $faculty->links() }}
                </div>
            @endif
            @else
            <div class="text-center py-4">
                <p>No faculty members found.</p>
                <a href="{{ route('admin.faculty.create') }}" class="btn btn-purple">
                    Add First Professor
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection