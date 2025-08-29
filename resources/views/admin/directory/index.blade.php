@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Faculty Directory</h1>
            <p class="text-muted">View deleted faculty members</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Deleted Faculty Members</h6>
        </div>
        <div class="card-body">
            @if($deletedFaculty->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Professor ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deletedFaculty as $prof)
                        <tr>
                            <td>{{ $prof->professor_id }}</td>
                            <td>{{ $prof->name }}</td>
                            <td>{{ $prof->email }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    Deleted
                                </span>
                            </td>
                            <td>{{ $prof->deleted_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Actions">
                                    <form action="{{ route('admin.faculty.restore', $prof->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.faculty.force-delete', $prof->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this professor? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Permanently Delete">
                                            <i class="bi bi-trash"></i> Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <p>No deleted faculty members found.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
