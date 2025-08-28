@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Salary Grades</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.salary_grades.create') }}" class="btn btn-purple mb-3">
        <i class="bi bi-plus"></i> New Salary Grade
    </a>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Step</th>
                        <th class="text-end">Base Salary</th>
                        <th class="text-end">Allowance</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $g)
                    <tr>
                        <td>{{ $g->grade }}</td>
                        <td>{{ $g->step }}</td>
                        <td class="text-end">{{ number_format($g->base_salary, 2) }}</td>
                        <td class="text-end">{{ number_format($g->allowance, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $g->is_active ? 'success' : 'secondary' }}">{{ $g->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.salary_grades.edit', $g->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.salary_grades.destroy', $g->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this grade?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No salary grades yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection



