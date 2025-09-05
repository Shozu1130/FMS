@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Admin Account</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('master_admin.admin_management.update', $admin) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $admin->name) }}" 
                                           readonly>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Name will be auto-updated based on selected department</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $admin->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('department') is-invalid @enderror" 
                                    id="department" 
                                    name="department" 
                                    required>
                                <option value="">Select Department</option>
                                @foreach($departments as $key => $value)
                                    <option value="{{ $key }}" {{ old('department', $admin->department) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('master_admin.admin_management.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Admin Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department');
    const nameInput = document.getElementById('name');
    
    // Department name mappings
    const departmentNames = {
        'BSIT': 'BSIT Administrator',
        'BSHM': 'BSHM Administrator',
        'BSAIS': 'BSAIS Administrator',
        'BSTM': 'BSTM Administrator',
        'BSOA': 'BSOA Administrator',
        'BSENTREP': 'BSENTREP Administrator',
        'BSBA': 'BSBA Administrator',
        'BLIS': 'BLIS Administrator',
        'BSCpE': 'BSCpE Administrator',
        'BSP': 'BSP Administrator',
        'BSCRIM': 'BSCRIM Administrator',
        'BPED': 'BPED Administrator',
        'BTLED': 'BTLED Administrator',
        'BEED': 'BEED Administrator',
        'BSED': 'BSED Administrator',
        'MASTER ADMIN': 'Master Administrator'
    };
    
    departmentSelect.addEventListener('change', function() {
        const selectedDepartment = this.value;
        if (selectedDepartment && departmentNames[selectedDepartment]) {
            nameInput.value = departmentNames[selectedDepartment];
        } else {
            nameInput.value = '';
        }
    });
    
    // Set initial value if department is already selected (for old input)
    if (departmentSelect.value && departmentNames[departmentSelect.value]) {
        nameInput.value = departmentNames[departmentSelect.value];
    }
});
</script>
@endsection
