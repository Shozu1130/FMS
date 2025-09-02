@extends('layouts.professor_admin')

@section('content')
<h1 class="mb-4">My Teaching History</h1>

<a href="{{ route('professor.teaching_history.create') }}" class="btn btn-primary mb-3">
    <i class="bi bi-plus-circle"></i> Add Teaching Assignment
</a>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif


<div class="card">
    <div class="card-body">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Semester</th>
                    <th>Academic Year</th>
                    <th>Units</th>
                    <th>Schedule</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachingHistories as $history)
                <tr>
                    <td>{{ $history->course_code }}</td>
                    <td>{{ $history->course_title }}</td>
                    <td>{{ $history->semester }}</td>
                    <td>{{ $history->academic_year }}</td>
                    <td>{{ $history->units }}</td>
                    <td>{{ $history->formatted_schedule }}</td>
                    <td>
                        <a href="{{ route('professor.teaching_history.show', $history) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="{{ route('professor.teaching_history.edit', $history) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('professor.teaching_history.destroy', $history) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No teaching history records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $teachingHistories->links() }}
@endsection
