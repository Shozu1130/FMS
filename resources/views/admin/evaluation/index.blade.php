@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Evaluations</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.evaluation.create') }}" class="btn btn-purple mb-3">
        <i class="bi bi-plus"></i> New Evaluation
    </a>

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
