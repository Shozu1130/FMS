@extends('layouts.professor_admin')

@section('content')
<h1 class="mb-4">My Profile</h1>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <!-- Profile Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('professor.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Professor ID</label>
                                <input type="text" class="form-control" value="{{ $professor->professor_id }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="{{ $professor->name }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $professor->email }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Employment Type</label>
                                <input type="text" class="form-control" value="{{ $professor->employment_type ?? 'Full-Time' }}" disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="picture" class="form-control" accept="image/*">
                                @if($professor->picture)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $professor->picture) }}" 
                                             alt="Profile" width="100" class="img-thumbnail">
                                        <small class="d-block text-muted">Current picture</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>


                    

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- Qualifications Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">My Qualifications</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQualificationModal">
                    <i class="bi bi-plus-circle"></i> Add Qualification
                </button>
            </div>
            <div class="card-body">
                <div id="qualifications-list">
                    @forelse($professor->qualifications->groupBy('type') as $type => $qualifications)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2">
                                <i class="bi bi-{{ $type === 'education' ? 'mortarboard' : ($type === 'experience' ? 'briefcase' : ($type === 'skill' ? 'gear' : ($type === 'certification' ? 'award' : 'trophy'))) }}"></i>
                                {{ \App\Models\ProfessorQualification::TYPES[$type] }}
                            </h6>
                            @foreach($qualifications as $qualification)
                                <div class="qualification-item mb-3 p-3 border rounded" data-id="{{ $qualification->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $qualification->title }}</h6>
                                            @if($qualification->institution_company)
                                                <p class="text-muted mb-1">{{ $qualification->institution_company }}</p>
                                            @endif
                                            @if($qualification->duration)
                                                <small class="text-muted">{{ $qualification->duration }}</small>
                                            @endif
                                            @if($qualification->level)
                                                <span class="badge bg-secondary ms-2">{{ $qualification->level_name }}</span>
                                            @endif
                                            @if($qualification->description)
                                                <p class="mt-2 mb-0">{{ $qualification->description }}</p>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm me-1" onclick="editQualification({{ $qualification->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteQualification({{ $qualification->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="text-center py-4" id="no-qualifications">
                            <i class="bi bi-mortarboard text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No qualifications added yet. Click "Add Qualification" to get started.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('professor.profile.change-password') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8">
                        <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Qualification Modal -->
<div class="modal fade" id="addQualificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qualificationModalTitle">Add Qualification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="qualificationForm">
                <div class="modal-body">
                    <input type="hidden" id="qualification_id" name="qualification_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    @foreach(\App\Models\ProfessorQualification::TYPES as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Institution/Company</label>
                                <input type="text" class="form-control" id="institution_company" name="institution_company">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Level</label>
                                <select class="form-select" id="level" name="level">
                                    <option value="">Select Level</option>
                                    @foreach(\App\Models\ProfessorQualification::LEVELS as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_current" name="is_current">
                            <label class="form-check-label" for="is_current">
                                Currently active/ongoing
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Qualification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qualificationForm = document.getElementById('qualificationForm');
    const modal = new bootstrap.Modal(document.getElementById('addQualificationModal'));
    const isCurrentCheckbox = document.getElementById('is_current');
    const endDateInput = document.getElementById('end_date');

    // Handle current checkbox
    isCurrentCheckbox.addEventListener('change', function() {
        if (this.checked) {
            endDateInput.value = '';
            endDateInput.disabled = true;
        } else {
            endDateInput.disabled = false;
        }
    });

    // Handle form submission
    qualificationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const qualificationId = document.getElementById('qualification_id').value;
        
        const url = qualificationId ? 
            `/professor/qualifications/${qualificationId}` : 
            '/professor/qualifications';
        
        const method = qualificationId ? 'PUT' : 'POST';
        
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.hide();
                location.reload(); // Refresh to show updated qualifications
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the qualification.');
        });
    });
});

function editQualification(id) {
    fetch(`/professor/qualifications/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const qualification = data.qualification;
                
                document.getElementById('qualification_id').value = qualification.id;
                document.getElementById('type').value = qualification.type;
                document.getElementById('title').value = qualification.title;
                document.getElementById('institution_company').value = qualification.institution_company || '';
                document.getElementById('location').value = qualification.location || '';
                document.getElementById('start_date').value = qualification.start_date || '';
                document.getElementById('end_date').value = qualification.end_date || '';
                document.getElementById('level').value = qualification.level || '';
                document.getElementById('is_current').checked = qualification.is_current;
                document.getElementById('description').value = qualification.description || '';
                
                document.getElementById('qualificationModalTitle').textContent = 'Edit Qualification';
                
                // Handle current checkbox state
                document.getElementById('end_date').disabled = qualification.is_current;
                
                const modal = new bootstrap.Modal(document.getElementById('addQualificationModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading qualification data.');
        });
}

function deleteQualification(id) {
    if (confirm('Are you sure you want to delete this qualification?')) {
        fetch(`/professor/qualifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Refresh to show updated qualifications
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the qualification.');
        });
    }
}

// Reset form when modal is closed
document.getElementById('addQualificationModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('qualificationForm').reset();
    document.getElementById('qualification_id').value = '';
    document.getElementById('qualificationModalTitle').textContent = 'Add Qualification';
    document.getElementById('end_date').disabled = false;
});
</script>

@endsection
