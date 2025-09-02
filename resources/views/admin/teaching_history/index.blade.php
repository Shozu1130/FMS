@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Teaching Histories</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.teaching_history.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Teaching Assignment
        </a>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Faculty</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Semester</th>
                        <th>Academic Year</th>
                        <th>Units</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachingHistories as $history)
                    <tr>
                        <td>{{ $history->faculty->name }}</td>
                        <td>{{ $history->course_code }}</td>
                        <td>{{ $history->course_title }}</td>
                        <td>{{ $history->semester }}</td>
                        <td>{{ $history->academic_year }}</td>
                        <td>{{ $history->units }}</td>
                        <td>
                            <span class="badge bg-{{ $history->is_active ? 'success' : 'secondary' }}">{{ $history->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.teaching_history.show', $history) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('admin.teaching_history.edit', $history) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.teaching_history.destroy', $history) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No teaching histories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $teachingHistories->links() }}
        </div>
    </div>
</div>
@endsection
