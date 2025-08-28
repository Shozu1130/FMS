@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Professor Details</h1>

    <div class="card shadow">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Professor ID</dt>
                <dd class="col-sm-9">{{ $professor->professor_id }}</dd>

                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $professor->name }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $professor->email }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $professor->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($professor->status) }}
                    </span>
                </dd>
            </dl>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('admin.faculty.index') }}" class="btn btn-outline-secondary">Back</a>
                <a href="{{ route('admin.faculty.edit', $professor->id) }}" class="btn btn-primary">Edit</a>
                <form action="{{ route('admin.faculty.destroy', $professor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this professor? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


