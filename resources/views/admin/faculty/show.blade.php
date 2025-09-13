@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Professor Details</h1>

    <div class="row">
        <div class="col-md-4">
            <!-- Basic Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    @if($professor->picture)
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . $professor->picture) }}" 
                                 alt="Profile" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    @endif
                    
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Professor ID</dt>
                        <dd class="col-sm-7">{{ $professor->professor_id }}</dd>

                        <dt class="col-sm-5">Name</dt>
                        <dd class="col-sm-7">{{ $professor->name }}</dd>

                        <dt class="col-sm-5">Email</dt>
                        <dd class="col-sm-7">{{ $professor->email }}</dd>

                        <dt class="col-sm-5">Department</dt>
                        <dd class="col-sm-7">{{ $professor->department ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Employment</dt>
                        <dd class="col-sm-7">{{ $professor->employment_type ?? 'Full-Time' }}</dd>

                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-{{ $professor->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($professor->status) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.faculty.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('admin.faculty.edit', $professor->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Professor
                        </a>
                        <form action="{{ route('admin.faculty.destroy', $professor->id) }}" method="POST" onsubmit="return confirm('Delete this professor? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete Professor
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Qualifications Card -->
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-mortarboard"></i> Qualifications & Experience
                    </h5>
                </div>
                <div class="card-body">
                    @if($professor->qualifications && $professor->qualifications->count() > 0)
                        @foreach($professor->qualifications->groupBy('type') as $type => $qualifications)
                            <div class="mb-4">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="bi bi-{{ $type === 'education' ? 'mortarboard' : ($type === 'experience' ? 'briefcase' : ($type === 'skill' ? 'gear' : ($type === 'certification' ? 'award' : 'trophy'))) }}"></i>
                                    {{ \App\Models\ProfessorQualification::TYPES[$type] }}
                                </h6>
                                @foreach($qualifications as $qualification)
                                    <div class="qualification-item mb-3 p-3 border rounded bg-light">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $qualification->title }}</h6>
                                                @if($qualification->institution_company)
                                                    <p class="text-muted mb-1">
                                                        <i class="bi bi-building"></i> {{ $qualification->institution_company }}
                                                    </p>
                                                @endif
                                                @if($qualification->location)
                                                    <p class="text-muted mb-1">
                                                        <i class="bi bi-geo-alt"></i> {{ $qualification->location }}
                                                    </p>
                                                @endif
                                                @if($qualification->duration)
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar"></i> {{ $qualification->duration }}
                                                    </small>
                                                @endif
                                                @if($qualification->level)
                                                    <span class="badge bg-secondary ms-2">{{ $qualification->level_name }}</span>
                                                @endif
                                                @if($qualification->description)
                                                    <p class="mt-2 mb-0">{{ $qualification->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-mortarboard text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No qualifications have been added yet.</p>
                            <small class="text-muted">The professor can add their qualifications through their profile page.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


