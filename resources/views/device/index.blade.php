@extends('layout.main')

@section('meta')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('css')
@endpush

@section('content')
    <div class="content pb-0">
        <div class="container-fluid">
            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Devices</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Devices</li>
                        </ol>
                    </nav>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-primary px-2 shadow"
                            data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <ul>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item"><i
                                            class="ti ti-file-type-pdf me-1"></i>Export
                                        as PDF</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item"><i
                                            class="ti ti-file-type-xls me-1"></i>Export
                                        as Excel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="javascript:void(0);" class="btn btn-icon btn-outline-info shadow" data-bs-toggle="tooltip"
                        data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh"><i
                            class="ti ti-refresh"></i></a>
                    <a href="javascript:void(0);" class="btn btn-icon btn-outline-warning shadow" data-bs-toggle="tooltip"
                        data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse"
                        id="collapse-header"><i class="ti ti-transition-top"></i></a>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- start card -->
            <div class="card border-0 rounded-0">
                <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
                    <div class="input-icon input-icon-start position-relative">
                        <span class="input-icon-addon text-dark"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus-filled me-1"></i>Add
                        Device</a>
                </div>
                <div class="card-body">
                    <!-- table header -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-light px-2 shadow"
                                    data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort By</a>
                                <div class="dropdown-menu">
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item sort-option"
                                                data-sort="newest">Newest</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item sort-option"
                                                data-sort="oldest">Oldest</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div id="reportrange" class="reportrange-picker d-flex align-items-center shadow">
                                <i class="ti ti-calendar-due text-dark fs-14 me-1"></i><span
                                    class="reportrange-picker-field">9 Jun
                                    25 - 9 Jun 25</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="btn btn-outline-light shadow px-2"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                        class="ti ti-filter me-2"></i>Filter<i class="ti ti-chevron-down ms-2"></i></a>
                                <div class="filter-dropdown-menu dropdown-menu dropdown-menu-lg p-0">
                                    <div
                                        class="filter-header d-flex align-items-center justify-content-between border-bottom">
                                        <h6 class="mb-0"><i class="ti ti-filter me-1"></i>Filter</h6>
                                        <button type="button" class="btn-close close-filter-btn"
                                            data-bs-dismiss="dropdown-menu" aria-label="Close"></button>
                                    </div>
                                    <div class="filter-set-view p-3">
                                        <div class="accordion" id="accordionExample">
                                            <!-- Name Filter -->
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" data-bs-toggle="collapse"
                                                        data-bs-target="#collapseName" aria-expanded="true"
                                                        aria-controls="collapseName">Name</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse show"
                                                    id="collapseName" data-bs-parent="#accordionExample">
                                                    <div
                                                        class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                        <div class="mb-2">
                                                            <div class="input-icon-start input-icon position-relative">
                                                                <span class="input-icon-addon fs-12">
                                                                    <i class="ti ti-search"></i>
                                                                </span>
                                                                <input type="text"
                                                                    class="form-control form-control-md device-filter"
                                                                    placeholder="Search" data-column="name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Unique ID Filter -->
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#collapseUniqueId" aria-expanded="false"
                                                        aria-controls="collapseUniqueId">Unique ID</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                    id="collapseUniqueId" data-bs-parent="#accordionExample">
                                                    <div
                                                        class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                        <div class="mb-2">
                                                            <div class="input-icon-start input-icon position-relative">
                                                                <span class="input-icon-addon fs-12">
                                                                    <i class="ti ti-search"></i>
                                                                </span>
                                                                <input type="text"
                                                                    class="form-control form-control-md device-filter"
                                                                    placeholder="Search" data-column="unique_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- IP Filter -->
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#collapseIP" aria-expanded="false"
                                                        aria-controls="collapseIP">IP</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                    id="collapseIP" data-bs-parent="#accordionExample">
                                                    <div
                                                        class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                        <div class="mb-2">
                                                            <div class="input-icon-start input-icon position-relative">
                                                                <span class="input-icon-addon fs-12">
                                                                    <i class="ti ti-search"></i>
                                                                </span>
                                                                <input type="text"
                                                                    class="form-control form-control-md device-filter"
                                                                    placeholder="Search" data-column="ip">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Employee ID Filter -->
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#collapseEmployeeId" aria-expanded="false"
                                                        aria-controls="collapseEmployeeId">Employee ID</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                    id="collapseEmployeeId" data-bs-parent="#accordionExample">
                                                    <div
                                                        class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                        <div class="mb-2">
                                                            <div class="input-icon-start input-icon position-relative">
                                                                <span class="input-icon-addon fs-12">
                                                                    <i class="ti ti-search"></i>
                                                                </span>
                                                                <input type="text"
                                                                    class="form-control form-control-md user-filter"
                                                                    placeholder="Search" data-column="employee_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Mobile Filter -->
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#collapseMobile" aria-expanded="false"
                                                        aria-controls="collapseMobile">Mobile</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                    id="collapseMobile" data-bs-parent="#accordionExample">
                                                    <div
                                                        class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                        <div class="mb-2">
                                                            <div class="input-icon-start input-icon position-relative">
                                                                <span class="input-icon-addon fs-12">
                                                                    <i class="ti ti-search"></i>
                                                                </span>
                                                                <input type="text"
                                                                    class="form-control form-control-md user-filter"
                                                                    placeholder="Search" data-column="mobile">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Status Filter -->
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#collapseStatus" aria-expanded="false"
                                                        aria-controls="collapseStatus">Status</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                    id="collapseStatus" data-bs-parent="#accordionExample">
                                                    <div
                                                        class="filter-content-list bg-light rounded border p-2 shadow mt-2">
                                                        <div class="mb-2">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input status-filter"
                                                                    type="checkbox" id="status-delete" value="0"
                                                                    data-column="status">
                                                                <label class="form-check-label"
                                                                    for="status-delete">Delete</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input status-filter"
                                                                    type="checkbox" id="status-active" value="1"
                                                                    data-column="status">
                                                                <label class="form-check-label"
                                                                    for="status-active">Active</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input status-filter"
                                                                    type="checkbox" id="status-deactivate" value="2"
                                                                    data-column="status">
                                                                <label class="form-check-label"
                                                                    for="status-deactivate">Deactivate</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input status-filter"
                                                                    type="checkbox" id="status-block" value="3"
                                                                    data-column="status">
                                                                <label class="form-check-label"
                                                                    for="status-block">Block</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        {{-- <div class="d-flex align-items-center gap-2">
                                            <a href="javascript:void(0);" class="btn btn-outline-light w-100">Reset</a>
                                            <a href="contacts-list.html" class="btn btn-primary w-100">Filter</a>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="btn bg-soft-indigo px-2 border-0"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                        class="ti ti-columns-3 me-2"></i>Manage Columns</a>
                                <div class="dropdown-menu dropdown-menu-md dropdown-md p-3">
                                    <ul id="column-visibility-list">
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Name</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="name">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Unique ID</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="unique_id">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Company</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="company">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Location</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="location">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Area</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="area">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>IP</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="ip">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Employee ID</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="employee_id">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Gender</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="gender">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Date of Birth</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked
                                                        data-column="date_of_birth">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Date of Joining</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked
                                                        data-column="date_of_joining">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Companies</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked
                                                        data-column="companies_count">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Locations</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked
                                                        data-column="locations_count">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Areas</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="areas_count">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Is Admin</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="is_admin">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Is Client</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="is_client">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Is User</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="is_user">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Last Login</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked
                                                        data-column="last_login_at">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Created At</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="created_at">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Updated At</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="updated_at">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center mb-2">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Status</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="status">
                                                </label>
                                            </div>
                                        </li>
                                        <li class="gap-1 d-flex align-items-center">
                                            <i class="ti ti-columns me-1"></i>
                                            <div class="form-check form-switch w-100 ps-0">
                                                <label class="form-check-label d-flex align-items-center gap-2 w-100">
                                                    <span>Action</span>
                                                    <input class="form-check-input column-visibility-toggle ms-auto"
                                                        type="checkbox" role="switch" checked data-column="action">
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex align-items-center shadow p-1 rounded border view-icons bg-white">
                                <a href="contacts-list.html" class="btn btn-sm p-1 border-0 fs-14 active"><i
                                        class="ti ti-list-tree"></i></a>
                                <a href="contacts.html" class="flex-shrink-0 btn btn-sm p-1 border-0 ms-1 fs-14"><i
                                        class="ti ti-grid-dots"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- table header -->

                    <!-- Company List -->
                    <div class="data-loading" style="display: none;">
                        <div class="d-flex justify-content-center align-items-center p-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">Loading data...</span>
                        </div>
                    </div>
                    <div class="table-responsive custom-table">
                        <table class="table table-nowrap" id="userslist">
                            <thead class="table-light">
                                <tr>
                                    <th>name</th>
                                    <th>unique_id</th>
                                    <th>company</th>
                                    <th>location</th>
                                    <th>area</th>
                                    <th>ip</th>
                                    <th>created_at</th>
                                    <th>updated_at</th>
                                    <th>status</th>
                                    <th class="text-end no-sort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="error-container" class="alert alert-danger mt-3" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>Error!</strong> <span id="error-message">Unable to load data.</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="retry-load">
                                <i class="ti ti-refresh me-1"></i>Retry
                            </button>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="datatable-length"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="datatable-paginate"></div>
                        </div>
                    </div>
                    <!-- /Company List -->
                </div>
            </div>
            <!-- end card -->
        </div>
    </div>

    <!-- Start Add Device -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Add Device</h5>
            <button type="button"
                class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body">
                    <form id="create-device-form" method="POST" action="{{ route('device.store') }}">
                        @csrf
                        <div class="accordion accordion-bordered" id="main_accordion">
                            <!-- Basic Info -->
                            <div class="accordion-item rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded"
                                        data-bs-toggle="collapse" data-bs-target="#basic">
                                        <span class="avatar avatar-md rounded me-1"><i
                                                class="ti ti-device-laptop"></i></span>
                                        Device Info
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse show" id="basic"
                                    data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Unique ID <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="unique_id" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">IP</label>
                                                    <input type="text" class="form-control" name="ip">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Company</label>
                                                    <select class="form-control select2" name="company_id" data-toggle="select2">
                                                        <option value="">Select company...</option>
                                                        @foreach (\App\Models\Company::where('status', 1)->get() as $company)
                                                            <option value="{{ $company->id }}">{{ $company->name }} -
                                                                {{ $company->industry }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Location</label>
                                                    <select class="form-control select2" name="location_id" data-toggle="select2">
                                                        <option value="">Select location...</option>
                                                        @foreach (\App\Models\Location::where('status', 1)->get() as $location)
                                                            <option value="{{ $location->id }}">{{ $location->name }} -
                                                                {{ $location->city }}, {{ $location->country }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Area</label>
                                                    <select class="form-control select2" name="area_id" data-toggle="select2">
                                                        <option value="">Select area...</option>
                                                        @foreach (\App\Models\Area::where('status', 1)->get() as $area)
                                                            <option value="{{ $area->id }}">{{ $area->name }} -
                                                                {{ $area->city }}, {{ $area->state }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status">
                                                        <option value="delete">Delete</option>
                                                        <option value="active" selected>Active</option>
                                                        <option value="deactivate">Inactive</option>
                                                        <option value="block">Block</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="create-form-alert" class="col-12" style="display: none;">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Device created successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="button" data-bs-dismiss="offcanvas"
                                class="btn btn-sm btn-light me-2">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary">Create Device</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Start Edit Device --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas_edit" aria-labelledby="offcanvas_edit_label">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvas_edit_label">Edit Device</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="row">
                <div class="col-12">
                    <form id="edit-device-form" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="from_index" value="1">
                        <div class="accordion accordion-bordered" id="main_accordion">
                            <!-- Basic Info -->
                            <div class="accordion-item rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded"
                                        data-bs-toggle="collapse" data-bs-target="#basic">
                                        <span class="avatar avatar-md rounded me-1"><i
                                                class="ti
ti-device-laptop"></i></span>
                                        Device Info
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse show" id="basic"
                                    data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name"
                                                        id="edit-name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Unique ID</label>
                                                    <input type="text" class="form-control" name="unique_id"
                                                        id="edit-unique_id">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">IP</label>
                                                    <input type="text" class="form-control" name="ip"
                                                        id="edit-ip">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Company</label>
                                                    <select class="form-control select2" name="company_id"
                                                        id="edit-company_id" data-toggle="select2">
                                                        <option value="">Select company...</option>
                                                        @foreach (\App\Models\Company::where('status', 1)->get() as $company)
                                                            <option value="{{ $company->id }}">{{ $company->name }} -
                                                                {{ $company->industry }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Location</label>
                                                    <select class="form-control select2" name="location_id"
                                                        id="edit-location_id" data-toggle="select2">
                                                        <option value="">Select location...</option>
                                                        @foreach (\App\Models\Location::where('status', 1)->get() as $location)
                                                            <option value="{{ $location->id }}">{{ $location->name }} -
                                                                {{ $location->city }}, {{ $location->country }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Area</label>
                                                    <select class="form-control select2" name="area_id"
                                                        id="edit-area_id" data-toggle="select2">
                                                        <option value="">Select area...</option>
                                                        @foreach (\App\Models\Area::where('status', 1)->get() as $area)
                                                            <option value="{{ $area->id }}">{{ $area->name }} -
                                                                {{ $area->city }}, {{ $area->state }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status" id="edit-status">
                                                        <option value="delete">Delete</option>
                                                        <option value="active">Active</option>
                                                        <option value="deactivate">Inactive</option>
                                                        <option value="block">Block</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Employee ID</label>
                                                    <input type="text" class="form-control" name="employee_id"
                                                        id="edit-employee_id">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Gender</label>
                                                    <select class="form-select" name="gender" id="edit-gender">
                                                        <option value="">Select Gender</option>
                                                        <option value="1">Male</option>
                                                        <option value="2">Female</option>
                                                        <option value="3">Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Date of Birth</label>
                                                    <input type="date" class="form-control" name="date_of_birth"
                                                        id="edit-date_of_birth">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Date of Joining</label>
                                                    <input type="date" class="form-control" name="date_of_joining"
                                                        id="edit-date_of_joining">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status" id="edit-status">
                                                        <option value="delete">Delete</option>
                                                        <option value="active">Active</option>
                                                        <option value="deactivate">Deactivate</option>
                                                        <option value="block">Block</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="edit-form-alert" class="col-12" style="display: none;">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Device updated successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end">
                                <button type="button" data-bs-dismiss="offcanvas"
                                    class="btn btn-sm btn-light me-2">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Update Device</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Location Modal -->
    <div class="modal fade" id="delete_location" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this location?</p>
                    <p>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="delete-location-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Location Offcanvas -->

    <!-- Delete Location Modal -->
    <div class="modal fade" id="delete_location" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this location?</p>
                    <p>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="delete-location-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Detail Modal -->
    <div class="modal fade" id="location_detail" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Device Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Name:</label>
                            <p id="detail-name"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Unique ID:</label>
                            <p id="detail-unique_id"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Company:</label>
                            <p id="detail-company"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Location:</label>
                            <p id="detail-location"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Area:</label>
                            <p id="detail-area"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">IP:</label>
                            <p id="detail-ip"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Status:</label>
                            <p id="detail-status"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Created At:</label>
                            <p id="detail-created_at"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Select2 CSS and JS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Device DataTable JS -->
    <script src="{{ asset('assets/js/datatable/device-datatable.js') }}" type="text/javascript"></script>

    <style>
        .highlight-row {
            animation: highlight 3s;
        }

        @keyframes highlight {
            0% {
                background-color: rgba(255, 255, 140, 0.5);
            }

            100% {
                background-color: transparent;
            }
        }

        /* Select2 Custom Styles */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #d1d3e2 !important;
            border-radius: 0.375rem !important;
            padding: 0.375rem 0.75rem !important;
            background-color: #fff !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding-left: 0 !important;
            padding-right: 20px !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6c757d !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .select2-dropdown {
            border: 1px solid #d1d3e2 !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .select2-container--default .select2-results__option {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd !important;
            color: white !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e9ecef !important;
            color: #495057 !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            console.log('jQuery loaded:', typeof $ !== 'undefined');
            console.log('Select2 loaded:', typeof $.fn.select2 !== 'undefined');
            console.log('Initializing Select2...');

            // Initialize Select2 for single select elements
            function initializeSelect2() {
                $('select[data-toggle="select2"]').each(function() {
                    if (!$(this).hasClass('select2-initialized')) {
                        console.log('Initializing Select2 for:', this);
                        $(this).select2({
                            placeholder: 'Select...',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $(this).closest('.offcanvas-body, .modal-body, body')
                        });
                        $(this).addClass('select2-initialized');
                    }
                });
            }

            // Initialize Select2 on page load
            initializeSelect2();

            // Re-initialize Select2 after dynamic content is added
            $(document).on('shown.bs.offcanvas', function() {
                console.log('Offcanvas shown, re-initializing Select2...');
                setTimeout(function() {
                    initializeSelect2();
                }, 100);
            });

            // Force re-initialization after a short delay
            setTimeout(function() {
                console.log('Force re-initializing Select2...');
                initializeSelect2();
            }, 1000);
        });
    </script>
@endpush
