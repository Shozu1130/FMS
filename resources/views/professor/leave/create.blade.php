@extends('layouts.professor')

@section('content')
<h1 class="text-purple mb-4">Apply for Leave</h1>

<div class="card shadow">
    <div class="card-body">
        <form method="POST" action="{{ route('professor.leave.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Type of Leave</label>
                <select name="type" class="form-select" required>
                    @foreach(\App\Models\LeaveRequest::types() as $key => $label)
                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Reason (optional)</label>
                <textarea name="reason" class="form-control" rows="4" placeholder="Brief reason...">{{ old('reason') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('professor.leave.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-purple">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection



