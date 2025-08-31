@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-purple">Teaching History for {{ $faculty->name }}</h1>
        <a href="{{ route('admin.teaching_history.create') }}" class="btn btn-purple">
            <i class="bi bi-plus"></i> New Teaching Assignment
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Professor ID:</strong> {{ $faculty->professor_id }}</p>
                    <p><strong>Email:</strong> {{ $faculty->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $faculty->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($faculty->status) }}
                        </span>
                    </p>
                    <p><strong>Total Assignments:</strong> {{ $teachingHistories->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Semester</th>
                        <th>Academic Year</th>
                        <th>Units</th>
                        <th>Students</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
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
                        <td>{{ $history->number_of_students }}</td>
                        <td>
                            @if($history->rating)
                                {{ number_format($history->rating, 2) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $history->is_active ? 'success' : 'secondary' }}">{{ $history->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.teaching_history.show', $history->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.teaching_history.edit', $history->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('admin.evaluation.create_for_faculty', $faculty->id) }}" class="btn btn-sm btn-purple" title="Evaluate">
                                <i class="bi bi-star-fill"></i>
                            </a>
                            <form action="{{ route('admin.teaching_history.destroy', $history->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this teaching history?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">No teaching histories found for this faculty member.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $teachingHistories->links() }}
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.faculty.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Faculty List
        </a>
        <a href="{{ route('admin.teaching_history.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list"></i> View All Teaching Histories
        </a>
    </div>
</div>
@endsection
