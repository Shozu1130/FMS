@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Evaluations</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('admin.evaluation.create') }}" class="btn btn-purple">
            <i class="bi bi-plus"></i> New Evaluation
        </a>
        <a href="{{ route('admin.evaluation.faculty_summary') }}" class="btn btn-outline-primary">
            <i class="bi bi-people"></i> Faculty Evaluation Summary
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickEvaluateModal">
            <i class="bi bi-star-fill"></i> Quick Evaluate
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Faculty</th>
                        <th>Teaching History</th>
                        <th>Evaluation Period</th>
                        <th>Academic Year</th>
                        <th>Semester</th>
                        <th>Overall Rating</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $evaluation)
                    <tr>
                        <td>{{ $evaluation->faculty->name }}</td>
                        <td>{{ $evaluation->teachingHistory ? $evaluation->teachingHistory->course_code . ' - ' . $evaluation->teachingHistory->course_title : 'N/A' }}</td>
                        <td>{{ $evaluation->evaluation_period_full }}</td>
                        <td>{{ $evaluation->academic_year }}</td>
                        <td>{{ $evaluation->semester }}</td>
                        <td>{{ number_format($evaluation->overall_rating, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $evaluation->is_published ? 'success' : 'secondary' }}">
                                {{ $evaluation->is_published ? 'Published' : 'Unpublished' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.evaluation.edit', $evaluation->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.evaluation.destroy', $evaluation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this evaluation?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No evaluations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $evaluations->links() }}
        </div>
    </div>
</div>
@endsection

<!-- Quick Evaluation Modal -->
<div class="modal fade" id="quickEvaluateModal" tabindex="-1" aria-labelledby="quickEvaluateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickEvaluateModalLabel">Quick Evaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.evaluation.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Faculty</label>
                            <select name="faculty_id" class="form-select" required>
                                <option value="">Select Faculty</option>
                                @foreach($faculties ?? [] as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year" class="form-select" required>
                                @for($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}-{{ $year + 1 }}">{{ $year }}-{{ $year + 1 }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Evaluation Period</label>
                            <select name="evaluation_period" class="form-select" required>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                                <option value="Annual">Annual</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teaching Effectiveness (1-5)</label>
                        <input type="number" name="teaching_effectiveness" class="form-control" min="1" max="5" step="0.1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Research & Scholarship (1-5)</label>
                        <input type="number" name="research_scholarship" class="form-control" min="1" max="5" step="0.1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Service & Community Engagement (1-5)</label>
                        <input type="number" name="service_engagement" class="form-control" min="1" max="5" step="0.1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Professional Development (1-5)</label>
                        <input type="number" name="professional_development" class="form-control" min="1" max="5" step="0.1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Student Advising (1-5)</label>
                        <input type="number" name="student_advising" class="form-control" min="1" max="5" step="0.1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Feedback/Comments</label>
                        <textarea name="feedback" class="form-control" rows="4" placeholder="Provide detailed feedback..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Evaluation</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Auto-calculate overall rating
    document.querySelectorAll('form').forEach(form => {
        const ratingInputs = form.querySelectorAll('input[type="number"][min="1"][max="5"]');
        ratingInputs.forEach(input => {
            input.addEventListener('input', function() {
                const inputs = form.querySelectorAll('input[type="number"][min="1"][max="5"]');
                let total = 0;
                inputs.forEach(inp => {
                    if (inp.value) total += parseFloat(inp.value);
                });
                const average = total / inputs.length;
                
                // You can display this somewhere or add a hidden field
                if (!form.querySelector('input[name="overall_rating"]')) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'overall_rating';
                    hiddenInput.value = average.toFixed(2);
                    form.appendChild(hiddenInput);
                } else {
                    form.querySelector('input[name="overall_rating"]').value = average.toFixed(2);
                }
            });
        });
    });
</script>
@endsection
