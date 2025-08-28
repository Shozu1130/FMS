@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Teaching History Details</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-purple text-white">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Faculty Member:</strong> {{ $teachingHistory->faculty->name }}</p>
                            <p><strong>Professor ID:</strong> {{ $teachingHistory->faculty->professor_id }}</p>
                            <p><strong>Course Code:</strong> {{ $teachingHistory->course_code }}</p>
                            <p><strong>Course Title:</strong> {{ $teachingHistory->course_title }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Semester:</strong> {{ $teachingHistory->semester }}</p>
                            <p><strong>Academic Year:</strong> {{ $teachingHistory->academic_year }}</p>
                            <p><strong>Units:</strong> {{ $teachingHistory->units }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $teachingHistory->is_active ? 'success' : 'secondary' }}">
                                    {{ $teachingHistory->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($teachingHistory->schedule)
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Schedule:</strong> {{ $teachingHistory->schedule }}</p>
                            @if($teachingHistory->start_time && $teachingHistory->end_time)
                            <p><strong>Time Slot:</strong> {{ $teachingHistory->time_slot }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($teachingHistory->room)
                            <p><strong>Room:</strong> {{ $teachingHistory->room }}</p>
                            @endif
                            <p><strong>Number of Students:</strong> {{ $teachingHistory->number_of_students }}</p>
                            <p><strong>Student Load:</strong> {{ $teachingHistory->student_load }}</p>
                        </div>
                    </div>
                    @endif

                    @if($teachingHistory->rating)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <p><strong>Rating:</strong> {{ number_format($teachingHistory->rating, 2) }}/5.00</p>
                        </div>
                    </div>
                    @endif

                    @if($teachingHistory->remarks)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <p><strong>Remarks:</strong></p>
                            <p class="border p-3 rounded">{{ $teachingHistory->remarks }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-purple text-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.teaching_history.edit', $teachingHistory->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Teaching History
                        </a>
                        
                        <form action="{{ route('admin.teaching_history.destroy', $teachingHistory->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this teaching history?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Delete Teaching History
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.teaching_history.faculty', $teachingHistory->faculty_id) }}" class="btn btn-info">
                            <i class="bi bi-person"></i> View Faculty's Teaching History
                        </a>
                        
                        <a href="{{ route('admin.teaching_history.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            @if($teachingHistory->evaluations->count() > 0)
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Evaluations ({{ $teachingHistory->evaluations->count() }})</h5>
                </div>
                <div class="card-body">
                    @foreach($teachingHistory->evaluations->take(3) as $evaluation)
                    <div class="border-bottom pb-2 mb-2">
                        <small class="text-muted">{{ $evaluation->evaluation_period_full }} - {{ $evaluation->academic_year }}</small>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Rating: {{ $evaluation->formatted_overall_rating }}</span>
                            <span class="badge bg-{{ $evaluation->overall_rating >= 4.0 ? 'success' : ($evaluation->overall_rating >= 3.0 ? 'warning' : 'danger') }}">
                                {{ $evaluation->rating_category }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                    @if($teachingHistory->evaluations->count() > 3)
                    <div class="text-center mt-2">
                        <small class="text-muted">+{{ $teachingHistory->evaluations->count() - 3 }} more evaluations</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
