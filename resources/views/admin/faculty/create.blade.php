@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Add New Professor</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.faculty.store') }}">
                @csrf
                
                <!-- Professor ID Preview -->
                <div class="mb-3">
                    <label class="form-label">Professor ID</label>
                    <div class="alert alert-info py-2">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Auto-generated: </strong>
                        <span id="professorIdPreview">PROF-{{ date('Y') }}-****</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.faculty.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-purple">
                        <i class="bi bi-check-circle"></i> Create Professor Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
