@extends('Layout.main')

@section('title', 'Role & Permission Management')

@section('content')
    <div class="page-wrapper">
        <!-- Start Content -->
        <div class="content pb-0">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap page-header">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Role & Permission Management<span
                            class="badge bg-danger ms-2 rounded-pill" id="role-count">0</span></h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                                    class="text-decoration-none">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Role & Permission</li>
                        </ol>
                    </nav>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="dropdown">
                        <button type="button" class="dropdown-toggle btn btn-outline-light px-2 shadow"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-package-export me-2"></i>Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <ul>
                                <li>
                                    <button type="button" class="dropdown-item" onclick="exportToPDF()">
                                        <i class="ti ti-file-type-pdf me-1"></i>Export as PDF
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item" onclick="exportToExcel()">
                                        <i class="ti ti-file-type-xls me-1"></i>Export as Excel
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip"
                        data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh"
                        onclick="refreshData()">
                        <i class="ti ti-refresh"></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip"
                        data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse"
                        id="collapse-header">
                        <i class="ti ti-transition-top"></i>
                    </button>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Role Management Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between gap-2 flex-wrap"
                    style="border-radius: 12px 12px 0 0;">
                    <div class="input-icon input-icon-start position-relative">
                        <span class="input-icon-addon text-muted"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control border-0 shadow-sm" placeholder="Search roles..."
                            id="role-search" style="background-color: #f8f9fa; border-radius: 8px;">
                    </div>
                    <button type="button" class="btn btn-danger shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#addRoleModal" style="border-radius: 8px;">
                        <i class="ti ti-square-rounded-plus-filled me-1"></i>Add New Role
                    </button>
                </div>
                <div class="card-body">
                    <!-- Roles List -->
                    <div class="table-responsive custom-table">
                        <table class="table table-hover" id="rolesTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="no-sort border-0">
                                        <div class="form-check form-check-md">
                                            <input class="form-check-input" type="checkbox" id="select-all-roles"
                                                title="Select all roles">
                                        </div>
                                    </th>
                                    <th class="border-0 fw-semibold text-dark">Role Name</th>
                                    <th class="border-0 fw-semibold text-dark">Users Count</th>
                                    <th class="border-0 fw-semibold text-dark">Created</th>
                                    <th class="no-sort border-0 fw-semibold text-dark">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="datatable-length"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="datatable-paginate"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Role Management Card -->

            <!-- Permission Management Card -->
            <div class="card border-0 shadow-sm" id="permission-card" style="display: none; border-radius: 12px;">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between gap-2 flex-wrap"
                    style="border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-dark">Role Name: <span class="text-danger fw-bold"
                            id="selected-role-name">-</span></h6>
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" id="select-all-permissions"
                            title="Allow all modules">
                        <label class="form-check-label fw-medium" for="select-all-permissions">Allow All Modules</label>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Permission List -->
                    <div class="table-responsive custom-table">
                        <table class="table table-nowrap" id="permissionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="no-sort">
                                        <div class="form-check form-check-md">
                                            <input class="form-check-input" type="checkbox"
                                                id="select-all-permission-checkboxes" title="Select all permissions">
                                        </div>
                                    </th>
                                    <th>Modules</th>
                                    <th>Sub Modules</th>
                                    <th>View</th>
                                    <th>Create</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                    <th>Import</th>
                                    <th>Export</th>
                                    <th>Allow All</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="datatable-length"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="datatable-paginate"></div>
                        </div>
                    </div>
                    <!-- Save Permissions Button -->
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-success shadow-sm" id="save-permissions"
                            onclick="savePermissions()" style="border-radius: 8px;">
                            <i class="ti ti-device-floppy me-1"></i>Save Permissions
                        </button>
                    </div>
                </div>
            </div>
            <!-- End Permission Management Card -->
        </div>
        <!-- End Content -->
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header border-0 bg-white" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title fw-bold text-dark" id="addRoleModalLabel">Add New Role</h5>
                    <button type="button" class="btn-close custom-btn-close border p-1 me-0 text-dark"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRoleForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="role_name" class="form-label">Role Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="role_name" name="role_name"
                                placeholder="Enter role name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="role_description" class="form-label">Description</label>
                            <textarea class="form-control" id="role_description" name="role_description" rows="3"
                                placeholder="Enter role description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-white" style="border-radius: 0 0 12px 12px;">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal"
                                style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn btn-danger" style="border-radius: 8px;">Create
                                Role</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close custom-btn-close border p-1 me-0 text-dark"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editRoleForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_role_id" name="role_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_role_name" class="form-label">Role Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_role_name" name="role_name"
                                placeholder="Enter role name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_role_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_role_description" name="role_description" rows="3"
                                placeholder="Enter role description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Role Modal -->
    <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRoleModalLabel">Delete Role</h5>
                    <button type="button" class="btn-close custom-btn-close border p-1 me-0 text-dark"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this role? This action cannot be undone.</p>
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Warning:</strong> All users assigned to this role will lose their permissions.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteRole">Delete Role</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Include Role Management JavaScript --}}
    <script src="{{ asset('assets/js/role-list.js') }}"></script>
    
    {{-- Pass Laravel routes to JavaScript --}}
    <script>
        // Pass Laravel routes and data to JavaScript
        window.Laravel = window.Laravel || {};
        window.Laravel.routes = {
            roles: {
                data: "{{ route('roles.data') }}",
                store: "{{ route('roles.store') }}",
                show: "{{ route('roles.show', ':id') }}",
                update: "{{ route('roles.update', ':id') }}",
                destroy: "{{ route('roles.destroy', ':id') }}",
                permissions: "{{ route('roles.permissions', ':id') }}",
                permissions_store: "{{ route('roles.permissions.store', ':id') }}"
            }
        };
        window.Laravel.csrf_token = "{{ csrf_token() }}";
    </script>
@endpush
