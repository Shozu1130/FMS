@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Subject Load Management</h4>
                    <div>
                        <a href="{{ route('admin.subject-loads.dashboard') }}" class="btn btn-info me-2">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.subject-loads.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Assign Subject
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="faculty_id" class="form-select">
                                    <option value="">All Faculty</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }} ({{ $faculty->professor_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="academic_year" class="form-select">
                                    <option value="">All Years</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="semester" class="form-select">
                                    <option value="">All Semesters</option>
                                    <option value="1st Semester" {{ request('semester') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                    <option value="2nd Semester" {{ request('semester') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                    <option value="Summer" {{ request('semester') == 'Summer' ? 'selected' : '' }}>Summer</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit" title="Search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('admin.subject-loads.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="toggleSelectAll()">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> Bulk Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('active')">Mark as Active</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('inactive')">Mark as Inactive</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('completed')">Mark as Completed</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.subject-loads.export', request()->query()) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                            <a href="{{ route('admin.subject-loads.report', request()->query()) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </a>
                        </div>
                    </div>

                    <!-- Subject Loads Table -->
                    @if($subjectLoads->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Faculty</th>
                                        <th>Subject</th>
                                        <th>Section</th>
                                        <th>Year Level</th>
                                        <th>Units/Hours</th>
                                        <th>Schedule</th>
                                        <th>Room</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjectLoads as $load)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="load-checkbox" value="{{ $load->id }}">
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $load->faculty->name }}</div>
                                                <small class="text-muted">{{ $load->faculty->professor_id }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $load->subject_code }}</div>
                                                <small class="text-muted">{{ $load->subject_name }}</small>
                                            </td>
                                            <td>{{ $load->section }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $load->year_level ?: 'Not Set' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $load->units }} units</span>
                                                <br><small class="text-muted">{{ $load->hours_per_week }} hrs/week</small>
                                            </td>
                                            <td>
                                                <div>{{ $load->schedule_display }}</div>
                                            </td>
                                            <td>{{ $load->room ?: 'TBA' }}</td>
                                            <td>
                                                <div>{{ $load->academic_year }}</div>
                                                <small class="text-muted">{{ $load->semester }}</small>
                                            </td>
                                            <td>
                                                @if($load->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($load->status == 'inactive')
                                                    <span class="badge bg-warning">Inactive</span>
                                                @else
                                                    <span class="badge bg-secondary">Completed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.subject-loads.show', $load) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.subject-loads.edit', $load) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit Assignment">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.subject-loads.destroy', $load) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this subject load?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Assignment">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $subjectLoads->firstItem() }} to {{ $subjectLoads->lastItem() }} of {{ $subjectLoads->total() }} results
                            </div>
                            {{ $subjectLoads->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Subject Loads Found</h5>
                            <p class="text-muted">No subject loads match your current filters.</p>
                            <a href="{{ route('admin.subject-loads.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Assign First Subject
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Form -->
<form id="bulkForm" action="{{ route('admin.subject-loads.bulk-status') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="status" id="bulkStatus">
    <div id="bulkIds"></div>
</form>

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.load-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function bulkUpdateStatus(status) {
    const selectedIds = Array.from(document.querySelectorAll('.load-checkbox:checked'))
                            .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one subject load.');
        return;
    }
    
    if (confirm(`Are you sure you want to mark ${selectedIds.length} subject loads as ${status}?`)) {
        document.getElementById('bulkStatus').value = status;
        
        const bulkIds = document.getElementById('bulkIds');
        bulkIds.innerHTML = '';
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            bulkIds.appendChild(input);
        });
        
        document.getElementById('bulkForm').submit();
    }
}
</script>
@endsection
