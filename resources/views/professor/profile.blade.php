@extends('layouts.professor')

@section('content')
<h1 class="text-purple mb-4">My Profile</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
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

                        <div class="mb-3">
                            <label class="form-label">Skills (comma separated)</label>
                            <input type="text" name="skills" class="form-control" 
                                   value="{{ old('skills', $professor->skills) }}"
                                   placeholder="Teaching, Research, Programming, Leadership">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Experiences</label>
                            <textarea name="experiences" class="form-control" rows="4"
                                      placeholder="- PhD in Computer Science, 2015&#10;- Senior Developer, 2010-2015">{{ old('experiences', $professor->experiences) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-purple">
                            <i class="bi bi-check-circle"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
 
@endsection