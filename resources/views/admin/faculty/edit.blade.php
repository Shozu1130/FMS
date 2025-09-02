@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Edit Professor</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.faculty.update', $professor->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="professor_id" class="form-label">Professor ID</label>
                    <input type="text" id="professor_id" value="{{ old('professor_id', $professor->professor_id) }}" class="form-control" readonly>
                    <input type="hidden" name="professor_id" value="{{ old('professor_id', $professor->professor_id) }}">
                    @error('professor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $professor->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $professor->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', $professor->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $professor->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="employment_type" class="form-label">Employment Type</label>
                    <select id="employment_type" name="employment_type" class="form-select @error('employment_type') is-invalid @enderror">
                        <option value="Full-Time" {{ old('employment_type', $professor->employment_type ?? 'Full-Time') == 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                        <option value="Part-Time" {{ old('employment_type', $professor->employment_type ?? 'Full-Time') == 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                    </select>
                    @error('employment_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.faculty.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


