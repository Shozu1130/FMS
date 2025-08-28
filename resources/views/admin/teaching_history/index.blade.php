@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Teaching Histories</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.teaching_history.create') }}" class="btn btn-purple mb-3">
        <i class="bi bi-plus"></i> New Teaching History
    </a>

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
                        <th class="text-end">Actions</th>
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
                        <td class="text-end">
                            <a href="{{ route('admin.teaching_history.edit', $history->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.teaching_history.destroy', $history->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this teaching history?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
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
