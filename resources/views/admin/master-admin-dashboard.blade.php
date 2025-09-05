@extends('layouts.admin')

@section('title', 'Master Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-user-shield text-danger mr-2"></i>
                Master Admin Dashboard
                <span class="badge badge-danger ml-2">Master Admin</span>
            </h1>
            <p class="text-muted mb-0">
                Administrative Account Management System
            </p>
        </div>
        <div class="text-muted small">
            <i class="fas fa-clock mr-1"></i>Last updated: {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>

    <!-- Admin Management Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAdmins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Master Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $masterAdmins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Department Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $regularAdmins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Departments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $departmentStats->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Admin Management Section -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users-cog mr-2"></i>Admin Management
                    </h6>
                    <a href="{{ route('master_admin.admin_management.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>Create New Admin
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5 class="text-primary">Administrative Account Control Center</h5>
                        <p class="text-muted">Manage admin accounts and department assignments</p>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-plus-circle fa-3x text-success mb-2"></i>
                                <h6>Create Admin</h6>
                                <p class="text-muted small">Add new department administrators</p>
                                <a href="{{ route('master_admin.admin_management.create') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus mr-1"></i>Create
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-list fa-3x text-primary mb-2"></i>
                                <h6>Manage Admins</h6>
                                <p class="text-muted small">View and edit existing admins</p>
                                <a href="{{ route('master_admin.admin_management.index') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cog mr-1"></i>Manage
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-chart-pie fa-3x text-info mb-2"></i>
                                <h6>View Reports</h6>
                                <p class="text-muted small">Admin activity and statistics</p>
                                <a href="{{ route('master_admin.admin_management.index') }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-chart-bar mr-1"></i>Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building mr-2"></i>Department Distribution
                    </h6>
                </div>
                <div class="card-body">
                    @if($departmentStats->count() > 0)
                        @foreach($departmentStats as $dept)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-graduation-cap text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold">{{ $dept->department ?? 'Unassigned' }}</div>
                                    <div class="small text-muted">{{ $dept->count }} admin(s)</div>
                                </div>
                                <div class="text-primary font-weight-bold">
                                    {{ $dept->count }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No department admins assigned yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Admin Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i>Recent Admin Accounts
                    </h6>
                    <a href="{{ route('master_admin.admin_management.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    @if($recentAdmins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Admin</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAdmins as $admin)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        <div class="icon-circle bg-{{ $admin->is_master_admin ? 'danger' : 'primary' }}">
                                                            <i class="fas fa-{{ $admin->is_master_admin ? 'user-shield' : 'user-tie' }} text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold">{{ $admin->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $admin->email }}</td>
                                            <td>
                                                @if($admin->department)
                                                    <span class="badge badge-info">{{ $admin->department }}</span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($admin->role === 'master_admin')
                                                    <span class="badge badge-danger">Master Admin</span>
                                                @else
                                                    <span class="badge badge-primary">Department Admin</span>
                                                @endif
                                            </td>
                                            <td>{{ $admin->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('master_admin.admin_management.show', $admin->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($admin->role !== 'master_admin')
                                                    <a href="{{ route('master_admin.admin_management.edit', $admin->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No admin accounts found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    height: 2rem;
    width: 2rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
