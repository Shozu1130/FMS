@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Create Clearance</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.clearance.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="faculty_id" class="form-label">Faculty Member *</label>
                    <select class="form-select" id="faculty_id" name="faculty_id" required>
                        <option value="">Select Faculty</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
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
                            <option value="{{ $type }}" {{ old('clearance_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="issued_date" class="form-label">Issued Date *</label>
                    <input type="date" class="form-control" id="issued_date" name="issued_date" value="{{ old('issued_date') }}" required>
                </div>

                <div class="mb-3">
                    <label for="expiration_date" class="form-label">Expiration Date</label>
                    <input type="date" class="form-control" id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}">
                </div>

                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-purple">Create Clearance</button>
                    <a href="{{ route('admin.clearance.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
