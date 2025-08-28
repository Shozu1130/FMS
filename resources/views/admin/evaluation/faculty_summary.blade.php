@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Faculty Evaluation Summary</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Faculty</th>
                        <th>Professor ID</th>
                        <th>Average Rating</th>
                        <th>Rating Category</th>
                        <th>Total Evaluations</th>
                        <th>Recent Evaluations</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faculties as $faculty)
                    <tr>
                        <td>{{ $faculty->name }}</td>
                        <td>{{ $faculty->professor_id }}</td>
                        <td>
                            @if($faculty->average_rating)
                                {{ number_format($faculty->average_rating, 2) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($faculty->average_rating)
                                <span class="badge bg-{{ $faculty->average_rating >= 4.5 ? 'success' : ($faculty->average_rating >= 4.0 ? 'info' : ($faculty->average_rating >= 3.5 ? 'warning' : ($faculty->average_rating >= 3.0 ? 'secondary' : 'danger'))) }}">
                                    {{ $faculty->average_rating >= 4.5 ? 'Outstanding' : ($faculty->average_rating >= 4.0 ? 'Very Satisfactory' : ($faculty->average_rating >= 3.5 ? 'Satisfactory' : ($faculty->average_rating >= 3.0 ? 'Fair' : 'Needs Improvement'))) }}
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $faculty->evaluations->count() }}</td>
                        <td>
                            @if($faculty->recent_evaluations->count() > 0)
                                <div class="small">
                                    @foreach($faculty->recent_evaluations->take(2) as $evaluation)
                                        <div>{{ $evaluation->evaluation_period_full }} {{ $evaluation->academic_year }}: {{ number_format($evaluation->overall_rating, 2) }}</div>
                                    @endforeach
                                    @if($faculty->recent_evaluations->count() > 2)
                                        <div class="text-muted">+{{ $faculty->recent_evaluations->count() - 2 }} more</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">No evaluations</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $faculty->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($faculty->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
