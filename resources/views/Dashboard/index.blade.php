@extends('Layout.main')

@section('title', 'Dashboard')

@section('content')
    <div class="page-wrapper">
        <!-- Start Content -->
        <div class="content pb-0">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Dashboard</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Dashboard Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 rounded-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-primary-subtle rounded">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded fs-18">
                                            <i class="ti ti-users"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted mb-1">Total Users</p>
                                    <h4 class="mb-0">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 rounded-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-success-subtle rounded">
                                        <span class="avatar-title bg-success-subtle text-success rounded fs-18">
                                            <i class="ti ti-shield"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted mb-1">Total Roles</p>
                                    <h4 class="mb-0">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 rounded-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-warning-subtle rounded">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded fs-18">
                                            <i class="ti ti-building"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted mb-1">Total Companies</p>
                                    <h4 class="mb-0">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 rounded-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-info-subtle rounded">
                                        <span class="avatar-title bg-info-subtle text-info rounded fs-18">
                                            <i class="ti ti-map-pin"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted mb-1">Total Locations</p>
                                    <h4 class="mb-0">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 rounded-0">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('role-permission.index') }}" class="btn btn-primary w-100">
                                        <i class="ti ti-shield me-2"></i>Manage Roles & Permissions
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('user.index') }}" class="btn btn-success w-100">
                                        <i class="ti ti-users me-2"></i>Manage Users
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('company.index') }}" class="btn btn-warning w-100">
                                        <i class="ti ti-building me-2"></i>Manage Companies
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('location.index') }}" class="btn btn-info w-100">
                                        <i class="ti ti-map-pin me-2"></i>Manage Locations
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Content -->
    </div>
@endsection
