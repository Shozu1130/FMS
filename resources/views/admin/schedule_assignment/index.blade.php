@extends('layouts.admin')

@section('title', 'Schedule Assignments')

@section('content')
<div class="container-fluid">
    <!-- Simple Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Schedule Assignments</h1>
        <div>
            <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New
            </a>
            <a href="{{ route('admin.schedule-assignment.calendar') }}" class="btn btn-info btn-sm ml-2">
                <i class="fas fa-calendar"></i> Calendar
            </a>
            <a href="{{ route('admin.schedule-assignment.export') }}" class="btn btn-success btn-sm ml-2">
                <i class="fas fa-download"></i> Export
            </a>
        </div>
    </div>

    <!-- Simple Stats -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col-3">
                            <small class="text-muted">Total</small>
                            <div class="font-weight-bold">{{ $stats['total_assignments'] }}</div>
                        </div>
                        <div class="col-3">
                            <small class="text-muted">Faculty</small>
                            <div class="font-weight-bold">{{ $stats['active_faculty'] }}</div>
                        </div>
                        <div class="col-3">
                            <small class="text-muted">Units</small>
                            <div class="font-weight-bold">{{ $stats['total_units'] }}</div>
                        </div>
                        <div class="col-3">
                            <small class="text-muted">Conflicts</small>
                            <div class="font-weight-bold text-warning">{{ $stats['conflicts'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter mr-2"></i>Advanced Filters & Search
            </h6>
        </div>
        <div class="card-body bg-light">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="academic_year" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-graduation-cap text-primary mr-1"></i>Academic Year
                    </label>
                    <select name="academic_year" id="academic_year" class="form-select form-select-sm border-primary">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="semester" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-calendar-week text-info mr-1"></i>Semester
                    </label>
                    <select name="semester" id="semester" class="form-select form-select-sm border-info">
                        <option value="">All Semesters</option>
                        @foreach(\App\Models\ScheduleAssignment::getSemesters() as $key => $sem)
                            <option value="{{ $key }}" {{ request('semester') == $key ? 'selected' : '' }}>
                                {{ $sem }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="professor_id" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-user text-success mr-1"></i>Faculty
                    </label>
                    <select name="professor_id" id="professor_id" class="form-select form-select-sm border-success">
                        <option value="">All Faculty</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('professor_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-flag text-warning mr-1"></i>Status
                    </label>
                    <select name="status" id="status" class="form-select form-select-sm border-warning">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-search text-secondary mr-1"></i>Search
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" id="search" class="form-control border-secondary" 
                               placeholder="Subject code, name, section, faculty..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Simple Filters -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row align-items-end">
                <div class="col-md-2">
                    <label class="form-label small">Semester</label>
                    <select name="semester" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\ScheduleAssignment::getSemesters() as $key => $sem)
                            <option value="{{ $key }}" {{ request('semester') == $key ? 'selected' : '' }}>{{ $sem }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Faculty</label>
                    <select name="professor_id" class="form-select form-select-sm">
                        <option value="">All Faculty</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('professor_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Year</label>
                    <select name="academic_year" class="form-select form-select-sm">
                        <option value="">All</option>
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Professional Data Table -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-table mr-2"></i>
                Schedule Assignments ({{ number_format($assignments->count()) }} total)
            </h6>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.schedule-assignment.export', request()->query()) }}" class="btn btn-sm btn-light rounded-pill px-3">
                    <i class="fas fa-download mr-1"></i> Export CSV
                </a>
                <a href="{{ route('admin.schedule-assignment.calendar', request()->query()) }}" class="btn btn-sm btn-light rounded-pill px-3">
                    <i class="fas fa-calendar mr-1"></i> Calendar View
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($assignments->count() > 0)
                <!-- Bulk Actions Bar -->
                <div class="bg-light border-bottom px-3 py-2 d-none" id="bulk-actions-bar">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">
                            <span id="selected-count">0</span> items selected
                        </span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-success" onclick="bulkUpdateStatus('active')">
                                <i class="fas fa-check mr-1"></i>Activate
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="bulkUpdateStatus('inactive')">
                                <i class="fas fa-pause mr-1"></i>Deactivate
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="bulkUpdateStatus('completed')">
                                <i class="fas fa-flag-checkered mr-1"></i>Complete
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="assignmentsTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-center" width="5%">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" id="select-all" class="custom-control-input">
                                        <label class="custom-control-label" for="select-all"></label>
                                    </div>
                                </th>
                                <th class="border-0 font-weight-bold text-dark">Faculty Details</th>
                                <th class="border-0 font-weight-bold text-dark">Subject Details</th>
                                <th class="border-0 font-weight-bold text-dark">Schedule & Room</th>
                                <th class="border-0 font-weight-bold text-dark">Load Info</th>
                                <th class="border-0 font-weight-bold text-dark">Academic Period</th>
                                <th class="border-0 font-weight-bold text-dark">Source</th>
                                <th class="border-0 font-weight-bold text-dark">Status</th>
                                <th class="border-0 font-weight-bold text-dark text-center" width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paginatedAssignments as $assignment)
                                <tr class="border-0">
                                    <td class="border-0 text-center py-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="assignment_ids[]" value="{{ $assignment->id }}" 
                                                   class="custom-control-input assignment-checkbox" id="check-{{ $assignment->id }}">
                                            <label class="custom-control-label" for="check-{{ $assignment->id }}"></label>
                                        </div>
                                    </td>
                                    <td class="border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $assignment->faculty->name }}</div>
                                                <small class="text-muted">{{ $assignment->faculty->professor_id }}</small>
                                                <div class="text-muted small">{{ $assignment->faculty->employment_type }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="border-0 py-3">
                                        <div class="font-weight-bold text-dark">{{ $assignment->subject_code }}</div>
                                        <div class="text-dark">{{ Str::limit($assignment->subject_name, 40) }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-users mr-1"></i>Section: {{ $assignment->section }} | {{ $assignment->year_level }}
                                        </small>
                                    </td>
                                    <td class="border-0 py-3">
                                        <div class="font-weight-bold text-dark">{{ $assignment->schedule_display }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $assignment->room ?? 'No room assigned' }}
                                        </small>
                                    </td>
                                    <td class="border-0 py-3">
                                        <div class="font-weight-bold text-dark">{{ $assignment->units }} units</div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock mr-1"></i>{{ $assignment->hours_per_week }} hrs/week
                                        </small>
                                    </td>
                                    <td class="border-0 py-3">
                                        <div class="font-weight-bold text-dark">{{ $assignment->academic_year }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar mr-1"></i>{{ $assignment->semester }}
                                        </small>
                                    </td>
                                    <td class="border-0 py-3">
                                        <span class="badge rounded-pill bg-{{ isset($assignment->source_table) && $assignment->source_table == 'Subject Load Tracker' ? 'success' : 'primary' }}">
                                            <i class="fas fa-{{ isset($assignment->source_table) && $assignment->source_table == 'Subject Load Tracker' ? 'sync' : 'plus' }} mr-1"></i>
                                            {{ $assignment->source_table ?? 'Direct Assignment' }}
                                        </span>
                                    </td>
                                    <td class="border-0 py-3">
                                        <span class="badge rounded-pill bg-{{ $assignment->status == 'active' ? 'success' : ($assignment->status == 'inactive' ? 'warning' : 'secondary') }}">
                                            <i class="fas fa-{{ $assignment->status == 'active' ? 'check' : ($assignment->status == 'inactive' ? 'pause' : 'stop') }} mr-1"></i>
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td class="border-0 py-3 text-center">
                                        <div class="btn-group" role="group">
                                            @if(isset($assignment->source_table) && $assignment->source_table == 'Subject Load Tracker')
                                                <a href="{{ route('admin.subject-loads.show', $assignment->id) }}" 
                                                   class="btn btn-sm btn-outline-info rounded-pill px-2" title="View in Subject Load Tracker">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('admin.schedule-assignment.show', $assignment->id) }}" 
                                                   class="btn btn-sm btn-outline-info rounded-pill px-2 mr-1" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.schedule-assignment.edit', $assignment->id) }}" 
                                                   class="btn btn-sm btn-outline-warning rounded-pill px-2 mr-1" title="Edit Assignment">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" 
                                                        onclick="confirmDelete({{ $assignment->id }})" title="Delete Assignment">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bulk Actions -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted">With selected:</span>
                            <form method="POST" action="{{ route('admin.schedule-assignment.bulk-update-status') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="assignment_ids" id="bulk-assignment-ids">
                                <select name="status" class="form-select form-select-sm" style="width: auto;" required>
                                    <option value="">Change Status</option>
                                    @foreach(\App\Models\ScheduleAssignment::getStatusOptions() as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary" id="bulk-update-btn" disabled>
                                    Update
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            Showing {{ $paginatedAssignments->count() }} of {{ $assignments->count() }} assignments
                        </small>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-gray-300 mb-4"></i>
                    <h4 class="text-gray-500">No Schedule Assignments Found</h4>
                    <p class="text-muted mb-4">
                        @if(array_filter($filters))
                            No assignments match your current filters. Try adjusting your search criteria.
                        @else
                            Get started by creating your first schedule assignment.
                        @endif
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        @if(array_filter($filters))
                            <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        @endif
                        <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Assignment
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
</div>

@push('styles')
<style>
.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
    font-size: 0.875rem;
}

.icon-circle {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.btn-group .btn {
    border-radius: 1rem !important;
    margin: 0 1px;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff;
    border-color: #007bff;
}

.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.spinner-border-lg {
    width: 3rem;
    height: 3rem;
    <!-- Simple Assignment List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Assignments ({{ $paginatedAssignments->total() }})</h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary" id="selectAllBtn">Select All</button>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('active')">Mark Active</a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('inactive')">Mark Inactive</a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('completed')">Mark Completed</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="bulkDelete()">Delete Selected</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($paginatedAssignments->count() > 0)
            <!-- Simple Table Layout -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" id="selectAllCheckbox"></th>
                            <th>Faculty</th>
                            <th>Subject</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                @foreach($paginatedAssignments as $assignment)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input assignment-checkbox" value="{{ $assignment->id }}">
                            </td>
                            <td>
                                <strong>{{ $assignment->faculty->name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">ID: {{ $assignment->faculty->professor_id ?? $assignment->professor_id }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $assignment->subject_code }}</span>
                                <br>{{ $assignment->subject_name }}
                                <br><small class="text-muted">{{ $assignment->section }} - {{ $assignment->year_level }}</small>
                            </td>
                            <td>
                                <strong>{{ ucfirst($assignment->schedule_day) }}</strong>
                                <br>{{ $assignment->time_range }}
                                <br><small class="text-muted">Room: {{ $assignment->room ?? 'TBA' }}</small>
                            </td>
                            <td>
                                @if($assignment->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($assignment->status == 'inactive')
                                    <span class="badge bg-danger">Inactive</span>
                                @elseif($assignment->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($assignment->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if(isset($assignment->source_table) && $assignment->source_table == 'Subject Load Tracker')
                                        <a href="{{ route('admin.subject-loads.show', $assignment->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.subject-loads.edit', $assignment->id) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteSubjectLoad({{ $assignment->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.schedule-assignment.show', $assignment->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.schedule-assignment.edit', $assignment->id) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteAssignment({{ $assignment->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
            @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Showing {{ $paginatedAssignments->count() }} of {{ $assignments->count() }} assignments
                </small>
                {{ $paginatedAssignments->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5>No Schedule Assignments Found</h5>
                <p class="text-muted">
                    @if(array_filter($filters))
                        No assignments match your filters.
                    @else
                        Get started by creating your first assignment.
                    @endif
                </p>
                <div class="mt-3">
                    @if(array_filter($filters))
                        <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-secondary btn-sm me-2">
                            Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-primary btn-sm">
                        Create Assignment
                    </a>
                </div>
            </div>
        @endif
        </div>
    </div>
</div>

@push('styles')
<style>
/* Simple table styling */
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

/* Simple hover effects */
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Checkbox styling */
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add fade-in animation to table rows
    $('.table tbody tr').addClass('fade-in');
    
    // Handle select all checkbox
    $('#select-all').change(function() {
        $('.assignment-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });
    
    // Handle individual checkboxes
    $('.assignment-checkbox').change(function() {
        updateBulkActions();
        updateSelectAllState();
    });
    
    // Update select all checkbox state
    function updateSelectAllState() {
        const totalCheckboxes = $('.assignment-checkbox').length;
        const checkedCheckboxes = $('.assignment-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#select-all').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#select-all').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all').prop('indeterminate', true);
        }
    }
    
    // Update bulk actions availability
    function updateBulkActions() {
        const checkedBoxes = $('.assignment-checkbox:checked');
        const bulkActionsBar = $('#bulk-actions-bar');
        const selectedCount = $('#selected-count');
        
        if (checkedBoxes.length > 0) {
            bulkActionsBar.removeClass('d-none').addClass('fade-in');
            selectedCount.text(checkedBoxes.length);
        } else {
            bulkActionsBar.addClass('d-none');
        }
    }
    
    // Auto-submit form on filter change
    $('#academic_year, #semester, #professor_id, #status').change(function() {
        showLoadingOverlay();
        $(this).closest('form').submit();
    });
});

// Show loading overlay
function showLoadingOverlay() {
    const overlay = $(`
        <div class="loading-overlay">
            <div class="text-center">
                <div class="spinner-border spinner-border-lg text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Loading assignments...</div>
            </div>
        </div>
    `);
    $('body').append(overlay);
}

// Bulk status update function with professional feedback
function bulkUpdateStatus(status) {
    const checkedBoxes = $('.assignment-checkbox:checked');
    if (checkedBoxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one assignment to update.',
            confirmButtonColor: '#007bff'
        });
        return;
    }
    
    const statusLabels = {
        'active': 'Active',
        'inactive': 'Inactive', 
        'completed': 'Completed'
    };
    
    Swal.fire({
        title: 'Confirm Bulk Update',
        text: `Update ${checkedBoxes.length} assignment(s) to ${statusLabels[status]}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Update',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoadingOverlay();
            
            const ids = [];
            checkedBoxes.each(function() {
                ids.push($(this).val());
            });
            
            // Submit form
            const form = $('<form>', {
                method: 'POST',
                action: '{{ route("admin.schedule-assignment.bulk-update-status") }}'
            });
            
            form.append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: '{{ csrf_token() }}'
            }));
            
            form.append($('<input>', {
                type: 'hidden',
                name: 'assignment_ids',
                value: ids.join(',')
            }));
            
            form.append($('<input>', {
                type: 'hidden',
                name: 'status',
                value: status
            }));
            
            $('body').append(form);
            form.submit();
        }
    });
}

// Professional delete confirmation
function confirmDelete(assignmentId) {
    Swal.fire({
        title: 'Delete Assignment?',
        text: 'This action cannot be undone. The assignment will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoadingOverlay();
            
            const form = $('<form>', {
                method: 'POST',
                action: `/admin/schedule-assignment/${assignmentId}`
            });
            
            form.append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: '{{ csrf_token() }}'
            }));
            
            form.append($('<input>', {
                type: 'hidden',
                name: '_method',
                value: 'DELETE'
            }));
            
            $('body').append(form);
            form.submit();
        }
    });
}
</script>
@endpush
// JavaScript for bulk actions with debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing checkbox functionality...');
    
    const selectAllBtn = document.getElementById('selectAllBtn');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    // Use a more dynamic selector since checkboxes are loaded via pagination
    function getCheckboxes() {
        return document.querySelectorAll('.assignment-checkbox');
    }
    
    // Handle select all button
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Select all button clicked');
            const checkboxes = getCheckboxes();
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = !allChecked;
            }
            
            console.log('Checkboxes updated:', !allChecked);
        });
    }
    
    // Handle select all checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            console.log('Select all checkbox changed:', this.checked);
            const checkboxes = getCheckboxes();
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Handle individual checkboxes - use event delegation
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('assignment-checkbox')) {
            console.log('Individual checkbox changed');
            const checkboxes = getCheckboxes();
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);
            }
        }
    });
});

function bulkUpdateStatus(status) {
    const checkedBoxes = document.querySelectorAll('.assignment-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select assignments to update.');
        return;
    }
    
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.schedule-assignment.bulk-update-status") }}';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Add assignment IDs
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'assignment_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    // Add status
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    form.appendChild(statusInput);
    
    document.body.appendChild(form);
    form.submit();
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.assignment-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select assignments to delete.');
        return;
    }
    
    if (confirm('Are you sure you want to delete the selected assignments? This action cannot be undone.')) {
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.schedule-assignment.bulk-delete") }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Add assignment IDs
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'assignment_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteAssignment(id) {
    if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
        // Use fetch API for better error handling
        fetch(`{{ url('admin/schedule-assignment') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error deleting assignment. Please try again.');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Error deleting assignment. Please try again.');
        });
    }
}

function deleteSubjectLoad(id) {
    if (confirm('Are you sure you want to delete this subject load? This action cannot be undone.')) {
        // Use fetch API for subject load deletion
        fetch(`{{ url('admin/subject-loads') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error deleting subject load. Please try again.');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Error deleting subject load. Please try again.');
        });
    }
}
</script>
@endpush
@endsection
