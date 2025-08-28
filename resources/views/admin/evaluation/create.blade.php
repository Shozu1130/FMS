@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Create Evaluation</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.evaluation.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
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
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="teaching_history_id" class="form-label">Teaching History</label>
                            <select class="form-select" id="teaching_history_id" name="teaching_history_id">
                                <option value="">Select Teaching History (Optional)</option>
                                @foreach($teachingHistories as $history)
                                    <option value="{{ $history->id }}" {{ old('teaching_history_id') == $history->id ? 'selected' : '' }}>
                                        {{ $history->course_code }} - {{ $history->course_title }} ({{ $history->semester }} {{ $history->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="evaluation_period" class="form-label">Evaluation Period *</label>
                            <select class="form-select" id="evaluation_period" name="evaluation_period" required>
                                <option value="">Select Period</option>
                                <option value="midterm" {{ old('evaluation_period') == 'midterm' ? 'selected' : '' }}>Midterm</option>
                                <option value="final" {{ old('evaluation_period') == 'final' ? 'selected' : '' }}>Final</option>
                                <option value="annual" {{ old('evaluation_period') == 'annual' ? 'selected' : '' }}>Annual</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year *</label>
                            <input type="number" class="form-control" id="academic_year" name="academic_year" value="{{ old('academic_year', date('Y')) }}" min="2000" max="2100" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester *</label>
                            <select class="form-select"极速加速器 id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1st Semester" {{ old('semester') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                <option value="2nd Semester" {{ old('semester') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                <option value="Summer" {{ old('semester') == 'Summer' ? 'selected' : '' }}>Summer</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Evaluation Components (Rate 1-5)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="teaching_effectiveness" class="form-label">Teaching Effectiveness *</label>
                                    <input type="number" class="form-control" id="teaching_effectiveness" name="teaching_effectiveness" value="{{ old('teaching_effectiveness') }}" min="1" max="5" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject_matter_knowledge" class="form-label">Subject Matter Knowledge *</label>
                                    <input type="number" class="form-control" id="subject_matter_knowledge" name="subject_matter_knowledge" value="{{ old('subject_matter_knowledge') }}" min="1" max="5" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="classroom_management" class="form-label">Classroom Management *</label>
                                    <input type="number" class="form-control" id="classroom_management" name="classroom_management" value="{{ old('classroom_management') }}" min="1" max="5" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="communication_skills" class="form-label">Communication Skills *</label>
                                    <input type="number" class="form-control" id="communication_skills" name="communication_skills" value="{{ old('communication_skills') }}" min="1" max="5" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_engagement" class="form-label">Student Engagement *</label>
                                    <input type="number" class="form-control" id="student_engagement" name="student_engagement" value="{{ old('student_engagement') }}" min="极速加速器1" max="5" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="strengths" class="form-label">Strengths</label>
                    <textarea class="form-control" id="strengths" name="strengths" rows="3">{{ old('strengths') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="areas_for_improvement" class="form-label">Areas for Improvement</label>
                    <textarea class="form-control" id="areas_for_improvement" name="areas_for_improvement" rows="3">{{ old('areas_for_improvement') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="recommendations" class="form-label">Recommendations</label>
                    <textarea class="form-control" id="recommendations" name="recommendations" rows="3">{{ old('recommendations') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-purple">Create Evaluation</button>
                    <a href="{{ route('admin.evaluation.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
