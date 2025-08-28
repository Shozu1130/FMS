@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Edit Clearance</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.clearance.update', $clearance->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="faculty_id" class="form-label">Faculty Member *</label>
                    <select class="form-select" id="faculty_id" name="faculty_id" required>
                        <option value="">Select Faculty</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ $clearance->faculty_id == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }} ({{ $faculty->professor_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="clearance_type" class="form-label">Clearance Type *</label>
                    <select class="form-select" id="clearance_type" name="clearance_type" required>
                        <option value="">Select Clearance Type</option>
                        @foreach(\App\Models\Clearance::getClearanceTypes() as $type)
                            <option value="{{ $type }}" {{ $clearance->clearance_type == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="issued_date" class="form-label">Issued Date *</label>
                    <input type="date" class="form-control" id="issued_date" name="issued_date" value="{{ old('issued_date', $clearance->issued_date->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="expiration_date" class="form-label">Expiration Date</label>
                    <input type="date" class="form-control" id="expiration_date" name="expiration_date" value="{{ old('expiration_date', $clearance->expiration_date ? $clearance->expiration_date->format('Y-m-d') : '') }}">
                </div>

                <div class="mb-3">
                    <label for="is_cleared" class="form-label">Status</label>
                    <select class="form-select" id="is_cleared" name="is_cleared">
                        <option value="1" {{ $clearance->is_cleared ? 'selected' : '' }}>Cleared</option>
                        <option value="0" {{ !$clearance->is_cleared ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('remarks', $clearance->remarks) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-purple">Update Clearance</button>
                    <a href="{{ route('admin.clearance.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
