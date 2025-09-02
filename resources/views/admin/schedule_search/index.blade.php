@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Schedule Search</h1>
                    <p class="text-muted mb-0">Search and view faculty schedules from Subject Load Tracker</p>
                </div>
                @if($hasSearchCriteria && $schedules->count() > 0)
                    <a href="{{ route('admin.schedule-search.export', request()->query()) }}" class="btn btn-outline-success">
                        <i class="fas fa-download"></i> Export Results
                    </a>
                @endif
            </div>

            @if($hasSearchCriteria && count($stats['schedule_conflicts']) > 0)
                <div class="alert alert-warning mb-4">
                    <h5><i class="fas fa-exclamation-triangle"></i> Schedule Conflicts Detected</h5>
                    <ul class="mb-0">
                        @foreach($stats['schedule_conflicts'] as $conflict)
                            <li>
                                <strong>{{ $conflict['faculty'] }}</strong> - {{ $conflict['day'] }}: 
                                {{ $conflict['subject1'] }} ({{ $conflict['time1'] }}) overlaps with 
                                {{ $conflict['subject2'] }} ({{ $conflict['time2'] }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-search"></i> Search Faculty Schedules
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" id="searchForm" class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search faculty, subject, section, room..." 
                                       value="{{ request('search') }}"
                                       id="searchInput">
                                @if(request('search'))
                                    <a href="{{ route('admin.schedule-search.index', request()->except('search')) }}" 
                                       class="btn btn-outline-secondary" title="Clear Search">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                            <small class="text-muted">Search across faculty name, subject code/name, section, or room</small>
                        </div>
                        
                        <!-- Faculty Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Faculty</label>
                            <select name="faculty_id" class="form-select" id="facultyFilter">
                                <option value="">All Faculty</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Academic Year Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year" class="form-select" id="yearFilter">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Semester Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" id="semesterFilter">
                                <option value="">All Semesters</option>
                                <option value="1st Semester" {{ request('semester') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                <option value="2nd Semester" {{ request('semester') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                <option value="Summer" {{ request('semester') == 'Summer' ? 'selected' : '' }}>Summer</option>
                            </select>
                        </div>
                        
                        <!-- Day Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Day</label>
                            <select name="schedule_day" class="form-select" id="dayFilter">
                                <option value="">All Days</option>
                                <option value="monday" {{ request('schedule_day') == 'monday' ? 'selected' : '' }}>Monday</option>
                                <option value="tuesday" {{ request('schedule_day') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="wednesday" {{ request('schedule_day') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="thursday" {{ request('schedule_day') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="friday" {{ request('schedule_day') == 'friday' ? 'selected' : '' }}>Friday</option>
                                <option value="saturday" {{ request('schedule_day') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                            </select>
                        </div>
                        
                        <!-- Year Level Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Year Level</label>
                            <select name="year_level" class="form-select" id="yearLevelFilter">
                                <option value="">All Levels</option>
                                <option value="1st Year" {{ request('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                <option value="2nd Year" {{ request('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3rd Year" {{ request('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4th Year" {{ request('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                            </select>
                        </div>
                        
                        <!-- Per Page -->
                        <div class="col-md-2">
                            <label class="form-label">Per Page</label>
                            <select name="per_page" class="form-select" id="perPageFilter">
                                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        
                        <!-- Search Button -->
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        
                        @if($hasSearchCriteria)
                            <div class="col-md-12">
                                <span class="text-muted small">
                                    Showing {{ $schedules->firstItem() ?? 0 }} to {{ $schedules->lastItem() ?? 0 }} of {{ $schedules->total() }} results
                                </span>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            @if($hasSearchCriteria)
                <!-- Statistics Cards (only show when searching) -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['total_assignments'] }}</h4>
                                        <p class="mb-0">Total Assignments</p>
                                    </div>
                                    <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['active_faculty'] }}</h4>
                                        <p class="mb-0">Active Faculty</p>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['total_units'] }}</h4>
                                        <p class="mb-0">Total Units</p>
                                    </div>
                                    <i class="fas fa-book fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ count($stats['schedule_conflicts']) }}</h4>
                                        <p class="mb-0">Schedule Conflicts</p>
                                    </div>
                                    <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Search Results</h5>
                    </div>
                    <div class="card-body">
                        @if($schedules->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Name</th>
                                            <th>Section</th>
                                            <th>Year Level</th>
                                            <th>Faculty Name</th>
                                            <th>Professor ID</th>
                                            <th>Schedule (Day & Time)</th>
                                            <th>Room</th>
                                            <th>Units</th>
                                            <th>Academic Year</th>
                                            <th>Status</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($schedules as $schedule)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-primary">{{ $schedule->subject_code }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $schedule->subject_name }}</div>
                                                    <small class="text-muted">{{ $schedule->units }} units ({{ $schedule->hours_per_week }} hrs/week)</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $schedule->section }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $schedule->year_level }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $schedule->faculty->name }}</div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $schedule->faculty->professor_id }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ ucfirst($schedule->schedule_day) }}</div>
                                                    <small class="text-muted">{{ $schedule->start_time }} - {{ $schedule->end_time }}</small>
                                                </td>
                                                <td>
                                                    {{ $schedule->room ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $schedule->units }}
                                                </td>
                                                <td>
                                                    <div>{{ $schedule->academic_year }}</div>
                                                    <small class="text-muted">{{ $schedule->semester }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ ucfirst($schedule->status) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.subject-loads.show', $schedule->id) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.subject-loads.edit', $schedule->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Showing {{ $schedules->firstItem() }} to {{ $schedules->lastItem() }} of {{ $schedules->total() }} results
                                </div>
                                {{ $schedules->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Results Found</h5>
                                <p class="text-muted">No schedules match your search criteria.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- No Search Criteria - Show Search Prompt -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">Search Faculty Schedules</h4>
                        <p class="text-muted mb-4">Use the search form above to find faculty schedules from the Subject Load Tracker.</p>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="list-group text-start">
                                    <div class="list-group-item">
                                        <strong>Search Tips:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Search by faculty name, subject code, or room</li>
                                            <li>Use filters to narrow down results</li>
                                            <li>Select specific academic year or semester</li>
                                            <li>Filter by day of the week</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit on filter change
    $('#facultyFilter, #yearFilter, #semesterFilter, #dayFilter, #yearLevelFilter, #perPageFilter').on('change', function() {
        $('#searchForm').submit();
    });
    
    // Highlight search terms in results
    function highlightSearchTerms() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        if (searchTerm.length > 2) {
            $('tbody tr').each(function() {
                const $row = $(this);
                const text = $row.text().toLowerCase();
                
                if (text.includes(searchTerm)) {
                    $row.addClass('table-warning');
                } else {
                    $row.removeClass('table-warning');
                }
            });
        } else {
            $('tbody tr').removeClass('table-warning');
        }
    }
    
    // Call highlight function on page load
    highlightSearchTerms();
    
    // Loading indicator for search
    $('#searchForm').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin"></i> Searching...');
    });
});
</script>
@endpush

@push('styles')
<style>
.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.search-highlight {
    background-color: yellow;
    font-weight: bold;
}

#searchForm .form-control:focus,
#searchForm .form-select:focus {
    border-color: #4a6cf7;
    box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.25);
}

.card-header {
    background: rgba(74, 108, 247, 0.05);
    border-bottom: 1px solid rgba(74, 108, 247, 0.1);
}
</style>
@endpush
