@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Request Teaching History Input</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Professors who haven't submitted teaching history for {{ $currentSemester }} {{ $currentYear }}</p>
        <a href="{{ route('admin.teaching_history.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Teaching Histories
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Professor Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($professorsWithoutData as $faculty)
                    <tr>
                        <td>{{ $faculty->name }}</td>
                        <td>
                            <span class="badge bg-{{ $faculty->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($faculty->status) }}</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#requestModal{{ $faculty->id }}">
                                <i class="bi bi-send"></i> Request Input
                            </button>
                            
                            <!-- Modal for request message -->
                            <div class="modal fade" id="requestModal{{ $faculty->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.teaching_history.request_professor', $faculty) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Request Teaching History from {{ $faculty->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="message{{ $faculty->id }}" class="form-label">Message (Optional)</label>
                                                    <textarea class="form-control" id="message{{ $faculty->id }}" name="message" rows="3" placeholder="Please provide your teaching history for {{ $currentSemester }} {{ $currentYear }}">Please provide your teaching history for {{ $currentSemester }} {{ $currentYear }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="bi bi-send"></i> Send Request
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center">All professors have submitted teaching history data for this semester.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
