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

@endsection
