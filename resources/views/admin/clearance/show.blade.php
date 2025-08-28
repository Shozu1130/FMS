@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Clearance Details</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-purple text-white">
                    <h5 class="mb-0">Clearance Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Faculty Member:</strong> {{ $clearance->faculty->name }}</p>
                    <p><strong>Professor ID:</strong> {{ $clearance->faculty->professor_id }}</p>
                    <p><strong>Clearance Type:</strong> {{ $clearance->clearance_type }}</p>
                    <p><strong>Issued Date:</strong> {{ $clearance->issued_date->format('Y-m-d') }}</p>
                    <p><strong>Expiration Date:</strong> {{ $clearance->expiration_date ? $clearance->expiration_date->format('Y-m-d') : 'N/A' }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $clearance->is_cleared ? 'success' : 'secondary' }}">
                            {{ $clearance->is_cleared ? 'Cleared' : 'Pending' }}
                        </span>
                    </p>
                    <p><strong>Remarks:</strong></p>
                    <p class="border p-3 rounded">{{ $clearance->remarks }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.clearance.edit', $clearance->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Clearance
                        </a>
                        
                        <form action="{{ route('admin.clearance.destroy', $clearance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this clearance?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete Clearance
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.clearance.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
