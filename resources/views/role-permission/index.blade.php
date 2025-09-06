@extends('Layout.main')

@section('title', 'Role & Permission Management')

@section('content')
    <div class="page-wrapper">
        <!-- Start Content -->
        <div class="content pb-0">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
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
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            initializeRolesTable();
            initializePermissionsTable();

            // Load initial data
            loadRoles();

            // Event listeners
            $('#addRoleForm').on('submit', handleAddRole);
            $('#editRoleForm').on('submit', handleEditRole);
            $('#confirmDeleteRole').on('click', handleDeleteRole);
            $('#select-all-roles').on('change', toggleAllRoleCheckboxes);
            $('#select-all-permissions').on('change', toggleAllPermissions);
            $('#select-all-permission-checkboxes').on('change', toggleAllPermissionCheckboxes);
            $('#role-search').on('keyup', debounce(filterRoles, 300));
        });

        // Initialize Roles DataTable
        function initializeRolesTable() {
            $('#rolesTable').DataTable({
                "bFilter": false,
                "bInfo": false,
                "ordering": true,
                "autoWidth": true,
                "language": {
                    search: ' ',
                    sLengthMenu: '_MENU_',
                    searchPlaceholder: "Search",
                    info: "_START_ - _END_ of _TOTAL_ items",
                    "lengthMenu": "Show _MENU_ entries",
                    paginate: {
                        next: '<i class="ti ti-chevron-right"></i> ',
                        previous: '<i class="ti ti-chevron-left"></i> '
                    },
                },
                initComplete: (settings, json) => {
                    $('.dataTables_paginate').appendTo('.datatable-paginate');
                    $('.dataTables_length').appendTo('.datatable-length');
                },
                "columns": [{
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input role-checkbox" type="checkbox" data-role-id="' +
                                row.id + '" title="Select role"></div>';
                        }
                    },
                    {
                        "data": "role_name"
                    },
                    {
                        "data": "users_count"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "render": function(data, type, row) {
                            return `
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon btn btn-xs shadow btn-icon btn-outline-light" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="editRole(${row.id})">
                                    <i class="ti ti-edit text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="managePermissions(${row.id}, '${row.role_name}')">
                                    <i class="ti ti-shield"></i> Permissions
                                </a>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteRole(${row.id}, '${row.role_name}')">
                                    <i class="ti ti-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    `;
                        }
                    }
                ]
            });
        }

        // Initialize Permissions DataTable
        function initializePermissionsTable() {
            $('#permissionsTable').DataTable({
                "bFilter": false,
                "bInfo": false,
                "ordering": true,
                "autoWidth": true,
                "language": {
                    search: ' ',
                    sLengthMenu: '_MENU_',
                    searchPlaceholder: "Search",
                    info: "_START_ - _END_ of _TOTAL_ items",
                    "lengthMenu": "Show _MENU_ entries",
                    paginate: {
                        next: '<i class="ti ti-chevron-right"></i> ',
                        previous: '<i class="ti ti-chevron-left"></i> '
                    },
                },
                initComplete: (settings, json) => {
                    $('.dataTables_paginate').appendTo('.datatable-paginate');
                    $('.dataTables_length').appendTo('.datatable-length');
                },
                "columns": [{
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input permission-checkbox" type="checkbox" data-module="' +
                                row.module + '" title="Select permission"></div>';
                        }
                    },
                    {
                        "data": "module"
                    },
                    {
                        "data": "sub_module"
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input permission-action" type="checkbox" data-module="' +
                                row.module + '" data-action="view" title="View permission"></div>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input permission-action" type="checkbox" data-module="' +
                                row.module + '" data-action="create" title="Create permission"></div>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input permission-action" type="checkbox" data-module="' +
                                row.module + '" data-action="edit" title="Edit permission"></div>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input permission-action" type="checkbox" data-module="' +
                                row.module + '" data-action="delete" title="Delete permission"></div>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input permission-action" type="checkbox" data-module="' +
                                row.module + '" data-action="import" title="Import permission"></div>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input permission-action" type="checkbox" data-module="' +
                                row.module + '" data-action="export" title="Export permission"></div>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="form-check form-check-md"><input class="form-check-input permission-allow-all" type="checkbox" data-module="' +
                                row.module + '" title="Allow all permissions for this module"></div>';
                        }
                    }
                ]
            });
        }

        // Load roles data
        function loadRoles() {
            $.ajax({
                url: '{{ route('roles.data') }}',
                type: 'GET',
                success: function(response) {
                    const table = $('#rolesTable').DataTable();
                    table.clear();
                    table.rows.add(response.data).draw();
                    $('#role-count').text(response.data.length);
                },
                error: function(xhr) {
                    showAlert('Error loading roles', 'error');
                }
            });
        }

        // Load permissions data for a specific role
        function loadPermissions(roleId) {
            $.ajax({
                url: '{{ route('roles.permissions', ':id') }}'.replace(':id', roleId),
                type: 'GET',
                success: function(response) {
                    const table = $('#permissionsTable').DataTable();
                    table.clear();
                    table.rows.add(response.data).draw();

                    // Set existing permissions
                    response.permissions.forEach(function(permission) {
                        const module = permission.modules;
                        if (permission.view) $('input[data-module="' + module +
                            '"][data-action="view"]').prop('checked', true);
                        if (permission.create) $('input[data-module="' + module +
                            '"][data-action="create"]').prop('checked', true);
                        if (permission.edit) $('input[data-module="' + module +
                            '"][data-action="edit"]').prop('checked', true);
                        if (permission.delete) $('input[data-module="' + module +
                            '"][data-action="delete"]').prop('checked', true);
                        if (permission.import) $('input[data-module="' + module +
                            '"][data-action="import"]').prop('checked', true);
                        if (permission.export) $('input[data-module="' + module +
                            '"][data-action="export"]').prop('checked', true);
                    });
                },
                error: function(xhr) {
                    showAlert('Error loading permissions', 'error');
                }
            });
        }

        // Handle add role form submission
        function handleAddRole(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('roles.store') }}',
                type: 'POST',
                data: $('#addRoleForm').serialize(),
                success: function(response) {
                    $('#addRoleModal').modal('hide');
                    $('#addRoleForm')[0].reset();
                    loadRoles();
                    showAlert('Role created successfully', 'success');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            const input = $('#' + key);
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(errors[key][0]);
                        });
                    } else {
                        showAlert('Error creating role', 'error');
                    }
                }
            });
        }

        // Handle edit role form submission
        function handleEditRole(e) {
            e.preventDefault();

            const roleId = $('#edit_role_id').val();

            $.ajax({
                url: '{{ route('roles.update', ':id') }}'.replace(':id', roleId),
                type: 'PUT',
                data: $('#editRoleForm').serialize(),
                success: function(response) {
                    $('#editRoleModal').modal('hide');
                    loadRoles();
                    showAlert('Role updated successfully', 'success');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            const input = $('#edit_' + key);
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(errors[key][0]);
                        });
                    } else {
                        showAlert('Error updating role', 'error');
                    }
                }
            });
        }

        // Handle delete role
        function handleDeleteRole() {
            const roleId = $('#confirmDeleteRole').data('role-id');

            $.ajax({
                url: '{{ route('roles.destroy', ':id') }}'.replace(':id', roleId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#deleteRoleModal').modal('hide');
                    loadRoles();
                    showAlert('Role deleted successfully', 'success');
                },
                error: function(xhr) {
                    showAlert('Error deleting role', 'error');
                }
            });
        }

        // Edit role
        function editRole(roleId) {
            $.ajax({
                url: '{{ route('roles.show', ':id') }}'.replace(':id', roleId),
                type: 'GET',
                success: function(response) {
                    $('#edit_role_id').val(response.id);
                    $('#edit_role_name').val(response.role_name);
                    $('#edit_role_description').val(response.description || '');
                    $('#editRoleModal').modal('show');
                },
                error: function(xhr) {
                    showAlert('Error loading role data', 'error');
                }
            });
        }

        // Delete role
        function deleteRole(roleId, roleName) {
            $('#confirmDeleteRole').data('role-id', roleId);
            $('#deleteRoleModal .modal-body p').html(
                `Are you sure you want to delete the role "<strong>${roleName}</strong>"? This action cannot be undone.`
            );
            $('#deleteRoleModal').modal('show');
        }

        // Manage permissions
        function managePermissions(roleId, roleName) {
            $('#selected-role-name').text(roleName);
            $('#permission-card').show();
            $('#permission-card').data('role-id', roleId);
            loadPermissions(roleId);
        }

        // Save permissions
        function savePermissions() {
            const roleId = $('#permission-card').data('role-id');
            const permissions = [];

            $('.permission-action:checked').each(function() {
                const module = $(this).data('module');
                const action = $(this).data('action');

                let permission = permissions.find(p => p.modules === module);
                if (!permission) {
                    permission = {
                        modules: module,
                        view: false,
                        create: false,
                        edit: false,
                        delete: false,
                        import: false,
                        export: false
                    };
                    permissions.push(permission);
                }
                permission[action] = true;
            });

            $.ajax({
                url: '{{ route('roles.permissions.store', ':id') }}'.replace(':id', roleId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    permissions: permissions
                },
                success: function(response) {
                    showAlert('Permissions saved successfully', 'success');
                },
                error: function(xhr) {
                    showAlert('Error saving permissions', 'error');
                }
            });
        }

        // Toggle all role checkboxes
        function toggleAllRoleCheckboxes() {
            const isChecked = $('#select-all-roles').is(':checked');
            $('.role-checkbox').prop('checked', isChecked);
        }

        // Toggle all permissions
        function toggleAllPermissions() {
            const isChecked = $('#select-all-permissions').is(':checked');
            $('.permission-action, .permission-allow-all').prop('checked', isChecked);
        }

        // Toggle all permission checkboxes
        function toggleAllPermissionCheckboxes() {
            const isChecked = $('#select-all-permission-checkboxes').is(':checked');
            $('.permission-checkbox').prop('checked', isChecked);
        }

        // Filter roles
        function filterRoles() {
            const searchTerm = $('#role-search').val().toLowerCase();
            $('#rolesTable tbody tr').each(function() {
                const roleName = $(this).find('td:eq(1)').text().toLowerCase();
                if (roleName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Utility functions
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function showAlert(message, type) {
            // You can implement your own alert system here
            alert(message);
        }

        function refreshData() {
            loadRoles();
        }

        function exportToPDF() {
            // Implement PDF export functionality
            showAlert('PDF export functionality will be implemented', 'info');
        }

        function exportToExcel() {
            // Implement Excel export functionality
            showAlert('Excel export functionality will be implemented', 'info');
        }
    </script>
@endpush
