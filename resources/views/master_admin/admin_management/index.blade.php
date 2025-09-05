@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-users-cog text-primary mr-2"></i>
                Admin Account Management
            </h1>
            <p class="text-muted mb-0">
                Manage administrative accounts and department assignments
            </p>
        </div>
        <a href="{{ route('master_admin.admin_management.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Create New Admin
        </a>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-2"></i>Admin Accounts
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($admins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admins as $admin)
                                        <tr>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>
                                                @if($admin->department)
                                                    <span class="badge bg-primary text-white">{{ $admin->department }}</span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('master_admin.admin_management.show', ['admin_management' => $admin->id]) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('master_admin.admin_management.edit', ['admin_management' => $admin->id]) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit Admin">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('master_admin.admin_management.destroy', ['admin_management' => $admin->id]) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this admin account?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Admin">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $admins->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No admin accounts found</h5>
                            <p class="text-muted">Create your first admin account to get started.</p>
                            <a href="{{ route('master_admin.admin_management.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Admin Account
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
