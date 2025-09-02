@extends('layouts.professor_admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">My Evaluation Results</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Evaluation Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ number_format($averageRating, 2) }}</h3>
                            <p class="text-muted">Overall Rating</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $evaluations->count() }}</h3>
                            <p class="text-muted">Total Evaluations</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <h3 class="text-info">{{ $latestEvaluation ? $latestEvaluation->evaluation_period : 'N/A' }}</h3>
                            <p class="text-muted">Latest Period</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <span class="badge bg-{{ $ratingCategoryColor }} fs-6">
                                {{ $ratingCategory }}
                            </span>
                            <p class="text-muted mt-2">Rating Category</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h5 class="card-title mb-0">Evaluation History</h5>
        </div>
        <div class="card-body">
            @if($evaluations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Academic Year</th>
                                <th>Evaluation Period</th>
                                <th>Teaching Effectiveness</th>
                                <th>Research & Scholarship</th>
                                <th>Service & Engagement</th>
                                <th>Professional Development</th>
                                <th>Student Advising</th>
                                <th>Overall Rating</th>
                                <th>Feedback</th>
                                <th>Evaluated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluations as $evaluation)
                            <tr>
                                <td>{{ $evaluation->academic_year }}</td>
                                <td>{{ $evaluation->evaluation_period }}</td>
                                <td>{{ number_format($evaluation->teaching_effectiveness, 1) }}</td>
                                <td>{{ number_format($evaluation->research_scholarship, 1) }}</td>
                                <td>{{ number_format($evaluation->service_engagement, 1) }}</td>
                                <td>{{ number_format($evaluation->professional_development, 1) }}</td>
                                <td>{{ number_format($evaluation->student_advising, 1) }}</td>
                                <td>
                                    <span class="badge bg-{{ $evaluation->overall_rating >= 4.5 ? 'success' : ($evaluation->overall_rating >= 4.0 ? 'info' : ($evaluation->overall_rating >= 3.5 ? 'warning' : ($evaluation->overall_rating >= 3.0 ? 'secondary' : 'danger'))) }}">
                                        {{ number_format($evaluation->overall_rating, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($evaluation->feedback)
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="popover" data-bs-title="Feedback" data-bs-content="{{ $evaluation->feedback }}">
                                            View
                                        </button>
                                    @else
                                        <span class="text-muted">No feedback</span>
                                    @endif
                                </td>
                                <td>{{ $evaluation->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Strengths</h5>
                        <ul id="strengthsList" class="list-group">
                            @foreach($evaluations->take(1) as $eval)
                                @if(!empty($eval->strengths))
                                    @foreach(explode("\n", trim($eval->strengths)) as $line)
                                        @if(strlen(trim($line))>0)
                                            <li class="list-group-item">{{ trim($line) }}</li>
                                        @endif
                                    @endforeach
                                @else
                                    <li class="list-group-item text-muted">No strengths provided.</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Areas for Improvement</h5>
                        <ul id="weaknessesList" class="list-group">
                            @foreach($evaluations->take(1) as $eval)
                                @if(!empty($eval->areas_for_improvement))
                                    @foreach(explode("\n", trim($eval->areas_for_improvement)) as $line)
                                        @if(strlen(trim($line))>0)
                                            <li class="list-group-item">{{ trim($line) }}</li>
                                        @endif
                                    @endforeach
                                @else
                                    <li class="list-group-item text-muted">No areas for improvement provided.</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="card bg-light mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">Recommended Trainings</h5>
                        <ul id="trainingRecommendations" class="mb-0"></ul>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No evaluation records found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

    // Auto-generate training recommendations from weaknesses and low-scoring dimensions
    (function(){
        const weaknesses = Array.from(document.querySelectorAll('#weaknessesList li'))
            .map(li => li.textContent.toLowerCase());

        const latest = @json($latestEvaluation);
        const recs = new Set();

        const add = (text) => { if (text) recs.add(text); };

        // Map keywords to trainings
        const keywordMap = [
            {kw:['punctual','late','attendance'], rec:'Time management and professional responsibility workshop'},
            {kw:['communication','clarity','speak','voice'], rec:'Effective communication and presentation skills course'},
            {kw:['classroom','management','discipline'], rec:'Classroom management strategies seminar'},
            {kw:['assessment','grading','rubric','fairness'], rec:'Fair and transparent assessment design training'},
            {kw:['motivation','engagement','participation'], rec:'Student engagement and active learning techniques training'},
            {kw:['subject','content','knowledge','update'], rec:'Subject matter upskilling or curriculum update workshop'},
            {kw:['patience','relations','empathy'], rec:'Human relations and student support training'},
        ];

        weaknesses.forEach(w => {
            keywordMap.forEach(map => {
                if (map.kw.some(k => w.includes(k))) add(map.rec);
            });
        });

        if (latest) {
            const dims = [
                {score: parseFloat(latest.teaching_effectiveness), rec:'Instructional design and pedagogy masterclass'},
                {score: parseFloat(latest.subject_matter_knowledge), rec:'Advanced subject matter refresher course'},
                {score: parseFloat(latest.classroom_management), rec:'Classroom management strategies seminar'},
                {score: parseFloat(latest.communication_skills), rec:'Effective communication and presentation skills course'},
                {score: parseFloat(latest.student_engagement), rec:'Student engagement and active learning techniques training'},
            ];
            dims.filter(d => !isNaN(d.score) && d.score < 3.5).forEach(d => add(d.rec));
        }

        const ul = document.getElementById('trainingRecommendations');
        if (ul && recs.size) {
            recs.forEach(r => {
                const li = document.createElement('li');
                li.textContent = r;
                ul.appendChild(li);
            });
        } else if (ul) {
            ul.innerHTML = '<li class="text-muted">No recommendations at this time.</li>'
        }
    })();
</script>
@endsection
