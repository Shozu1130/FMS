@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Evaluation Ratings Report</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Rating Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Outstanding (4.5-5.0)</span>
                        <span class="badge bg-success">{{ $ratingStats['outstanding'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Very Satisfactory (4.0-4.49)</span>
                        <span class="badge bg-info">{{ $ratingStats['very_satisfactory'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Satisfactory (3.5-3.99)</span>
                        <span class="badge bg-warning">{{ $ratingStats['satisfactory'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Fair (3.0-3.49)</span>
                        <span class="badge bg-secondary">{{ $ratingStats['fair'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Needs Improvement (<3.0)</span>
                        <span class="badge bg-danger">{{ $ratingStats['needs_improvement'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Faculty</th>
                                <th>Evaluation Period</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Overall Rating</th>
                                <th>Rating Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluations as $evaluation)
                            <tr>
                                <td>{{ $evaluation->faculty->name }}</td>
                                <td>{{ $evaluation->evaluation_period_full }}</td>
                                <td>{{ $evaluation->academic_year }}</td>
                                <td>{{ $evaluation->semester }}</td>
                                <td>{{ number_format($evaluation->overall_rating, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $evaluation->overall_rating >= 4.5 ? 'success' : ($evaluation->overall_rating >= 4.0 ? 'info' : ($evaluation->overall_rating >= 3.5 ? 'warning' : ($evaluation->overall_rating >= 3.0 ? 'secondary' : 'danger'))) }}">
                                        {{ $evaluation->rating_category }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $evaluation->is_published ? 'success' : 'secondary' }}">
                                        {{ $evaluation->is_published ? 'Published' : 'Unpublished' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
