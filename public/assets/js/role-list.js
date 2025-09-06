$(document).ready(function() {
    // Global variables
    let selectedRoleId = null;
    let rolesTable = null;
    let permissionsTable = null;

    // Safety check for Laravel object
    if (typeof window.Laravel === 'undefined') {
        console.error('Laravel routes not available. Please ensure the blade template includes the Laravel routes script.');
        return;
    }

    // Initialize DataTables and load data
    initializeRolesTable();
    initializePermissionsTable();
    loadRoles();

    // Event listeners
    $('#addRoleForm').on('submit', handleAddRole);
    $('#editRoleForm').on('submit', handleEditRole);
    $('#confirmDeleteRole').on('click', handleDeleteRole);
    $('#select-all-roles').on('change', toggleAllRoleCheckboxes);
    $('#select-all-permissions').on('change', toggleAllPermissions);
    $('#select-all-permission-checkboxes').on('change', toggleAllPermissionCheckboxes);
    $('#role-search').on('keyup', debounce(filterRoles, 300));

    // Utility event handlers
    $('#collapse-header').on('click', function() {
        $(this).find('i').toggleClass('ti-transition-top ti-transition-bottom');
        $('.page-header').slideToggle();
    });

    // Initialize Roles DataTable
    function initializeRolesTable() {
        rolesTable = $('#rolesTable').DataTable({
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
                    },
                    "orderable": false
                },
                {
                    "data": "role_name",
                    "orderable": true
                },
                {
                    "data": "users_count",
                    "orderable": true,
                    "render": function(data, type, row) {
                        return data ? `<span class="badge bg-primary">${data}</span>` : '<span class="badge bg-secondary">0</span>';
                    }
                },
                {
                    "data": "created_at",
                    "orderable": true,
                    "render": function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString() : 'N/A';
                    }
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
                    },
                    "orderable": false
                }
            ]
        });
    }

    // Initialize Permissions DataTable
    function initializePermissionsTable() {
        permissionsTable = $('#permissionsTable').DataTable({
            "bFilter": false,
            "bInfo": false,
            "ordering": false,
            "autoWidth": true,
            "pageLength": -1,
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
            "columns": [{
                    "render": function(data, type, row) {
                        return '<div class="form-check form-check-md"><input class="form-check-input permission-checkbox" type="checkbox" data-module="' +
                            row.module + '" title="Select permission"></div>';
                    }
                },
                {
                    "data": "module",
                    "render": function(data, type, row) {
                        return `<span class="fw-semibold text-primary">${data}</span>`;
                    }
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
                            row.module + '" title="Allow all permissions for this module" onchange="toggleModulePermissions(this)"></div>';
                    }
                }
            ]
        });

        // Handle allow all checkbox for individual modules
        $(document).on('change', '.permission-allow-all', function() {
            const module = $(this).data('module');
            const isChecked = $(this).is(':checked');
            
            // Toggle all permission actions for this module
            $(`.permission-action[data-module="${module}"]`).prop('checked', isChecked);
        });

        // Handle individual permission changes to update allow all checkbox
        $(document).on('change', '.permission-action', function() {
            const module = $(this).data('module');
            const totalActions = $(`.permission-action[data-module="${module}"]`).length;
            const checkedActions = $(`.permission-action[data-module="${module}"]:checked`).length;
            
            // Update allow all checkbox based on individual permissions
            $(`.permission-allow-all[data-module="${module}"]`).prop('checked', totalActions === checkedActions);
        });
    }

    // Load roles data
    function loadRoles() {
        // Show loading state
        showAlert('Loading roles...', 'info');
        
        $.ajax({
            url: window.Laravel.routes.roles.data,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrf_token || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data) {
                    const table = $('#rolesTable').DataTable();
                    table.clear();
                    table.rows.add(response.data).draw();
                    $('#role-count').text(response.data.length);
                    showAlert('Roles loaded successfully', 'success', 2000);
                } else {
                    showAlert('No roles found', 'warning');
                }
            },
            error: function(xhr) {
                console.error('Error loading roles:', xhr);
                showAlert('Error loading roles: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
            }
        });
    }

    // Load permissions data for a specific role
    function loadPermissions(roleId) {
        selectedRoleId = roleId;
        
        // Show loading state
        showAlert('Loading permissions...', 'info');
        
        $.ajax({
            url: window.Laravel.routes.roles.permissions.replace(':id', roleId),
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrf_token || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const table = $('#permissionsTable').DataTable();
                    table.clear();
                    table.rows.add(response.data || []).draw();

                    // Clear all checkboxes first
                    $('.permission-action, .permission-allow-all').prop('checked', false);

                    // Set existing permissions
                    if (response.permissions && response.permissions.length > 0) {
                        response.permissions.forEach(function(permission) {
                            const module = permission.modules;
                            
                            // Set individual permission checkboxes
                            if (permission.view) $('input[data-module="' + module + '"][data-action="view"]').prop('checked', true);
                            if (permission.create) $('input[data-module="' + module + '"][data-action="create"]').prop('checked', true);
                            if (permission.edit) $('input[data-module="' + module + '"][data-action="edit"]').prop('checked', true);
                            if (permission.delete) $('input[data-module="' + module + '"][data-action="delete"]').prop('checked', true);
                            if (permission.import) $('input[data-module="' + module + '"][data-action="import"]').prop('checked', true);
                            if (permission.export) $('input[data-module="' + module + '"][data-action="export"]').prop('checked', true);
                            
                            // Check if all permissions are enabled for this module
                            const totalActions = $(`.permission-action[data-module="${module}"]`).length;
                            const checkedActions = $(`.permission-action[data-module="${module}"]:checked`).length;
                            $(`.permission-allow-all[data-module="${module}"]`).prop('checked', totalActions === checkedActions);
                        });
                    }
                    
                    showAlert('Permissions loaded successfully', 'success', 2000);
                } else {
                    showAlert('Error loading permissions: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr) {
                console.error('Error loading permissions:', xhr);
                showAlert('Error loading permissions: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
            }
        });
    }

    // Handle add role form submission
    function handleAddRole(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.text('Creating...').prop('disabled', true);

        $.ajax({
            url: window.Laravel.routes.roles.store,
            type: 'POST',
            data: $('#addRoleForm').serialize(),
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrf_token || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#addRoleModal').modal('hide');
                    $('#addRoleForm')[0].reset();
                    loadRoles();
                    showAlert('Role created successfully', 'success');
                } else {
                    showAlert('Error creating role: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        const input = $('#' + key);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    showAlert('Error creating role: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
                }
            },
            complete: function() {
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    }

    // Handle edit role form submission
    function handleEditRole(e) {
        e.preventDefault();

        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const roleId = $('#edit_role_id').val();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.text('Updating...').prop('disabled', true);

        $.ajax({
            url: window.Laravel.routes.roles.update.replace(':id', roleId),
            type: 'PUT',
            data: $('#editRoleForm').serialize(),
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrf_token || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editRoleModal').modal('hide');
                    loadRoles();
                    showAlert('Role updated successfully', 'success');
                } else {
                    showAlert('Error updating role: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        const input = $('#edit_' + key);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    showAlert('Error updating role: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
                }
            },
            complete: function() {
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    }

    // Handle delete role
    function handleDeleteRole() {
        const roleId = $('#confirmDeleteRole').data('role-id');
        const submitBtn = $('#confirmDeleteRole');
        const originalText = submitBtn.text();
        submitBtn.text('Deleting...').prop('disabled', true);

        $.ajax({
            url: window.Laravel.routes.roles.destroy.replace(':id', roleId),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrf_token || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteRoleModal').modal('hide');
                    loadRoles();
                    showAlert('Role deleted successfully', 'success');
                } else {
                    showAlert('Error deleting role: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr) {
                showAlert('Error deleting role: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
            },
            complete: function() {
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    }

    // Global functions for button actions
    window.editRole = function(roleId) {
        $.ajax({
            url: window.Laravel.routes.roles.show.replace(':id', roleId),
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrf_token || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data) {
                    $('#edit_role_id').val(response.data.id);
                    $('#edit_role_name').val(response.data.role_name);
                    $('#edit_role_description').val(response.data.description || '');
                    $('#editRoleModal').modal('show');
                } else {
                    showAlert('Error loading role data: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr) {
                showAlert('Error loading role data: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
            }
        });
    };

    window.deleteRole = function(roleId, roleName) {
        $('#confirmDeleteRole').data('role-id', roleId);
        $('#deleteRoleModal .modal-body p').html(
            `Are you sure you want to delete the role "<strong>${roleName}</strong>"? This action cannot be undone.`
        );
        $('#deleteRoleModal').modal('show');
    };

    window.managePermissions = function(roleId, roleName) {
        $('#selected-role-name').text(roleName);
        $('#permission-card').show();
        $('#permission-card').data('role-id', roleId);
        loadPermissions(roleId);
        
        // Scroll to permissions section
        $('html, body').animate({
            scrollTop: $('#permission-card').offset().top - 100
        }, 500);
    };

    // Save permissions
    window.savePermissions = function() {
        const roleId = selectedRoleId || $('#permission-card').data('role-id');
        if (!roleId) {
            showAlert('No role selected for permissions', 'error');
            return;
        }

        const permissions = [];
        const processedModules = new Set();

        $('.permission-action:checked').each(function() {
            const module = $(this).data('module');
            const action = $(this).data('action');

            if (!processedModules.has(module)) {
                processedModules.add(module);
                permissions.push({
                    modules: module,
                    view: false,
                    create: false,
                    edit: false,
                    delete: false,
                    import: false,
                    export: false
                });
            }

            const permission = permissions.find(p => p.modules === module);
            if (permission && action in permission) {
                permission[action] = true;
            }
        });

        const submitBtn = $('#save-permissions');
        const originalText = submitBtn.text();
        submitBtn.text('Saving...').prop('disabled', true);

        $.ajax({
            url: window.Laravel.routes.roles.permissions_store.replace(':id', roleId),
            type: 'POST',
            data: {
                permissions: permissions
            },
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrf_token || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('Permissions saved successfully', 'success');
                } else {
                    showAlert('Error saving permissions: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr) {
                showAlert('Error saving permissions: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
            },
            complete: function() {
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    };

    // Toggle functions
    function toggleAllRoleCheckboxes() {
        const isChecked = $('#select-all-roles').is(':checked');
        $('.role-checkbox').prop('checked', isChecked);
    }

    function toggleAllPermissions() {
        const isChecked = $('#select-all-permissions').is(':checked');
        $('.permission-action, .permission-allow-all').prop('checked', isChecked);
    }

    function toggleAllPermissionCheckboxes() {
        const isChecked = $('#select-all-permission-checkboxes').is(':checked');
        $('.permission-checkbox').prop('checked', isChecked);
    }

    // Filter roles
    function filterRoles() {
        const searchTerm = $('#role-search').val().toLowerCase();
        const table = $('#rolesTable').DataTable();
        table.search(searchTerm).draw();
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

    function showAlert(message, type = 'info', duration = 5000) {
        // Remove existing alerts
        $('.custom-alert').remove();
        
        // Create alert element
        const alertClass = type === 'error' ? 'danger' : type;
        const alertHtml = `
            <div class="alert alert-${alertClass} alert-dismissible fade show custom-alert" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Add to body
        $('body').append(alertHtml);
        
        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                $('.custom-alert').alert('close');
            }, duration);
        }
    }

    // Utility functions for export and refresh
    window.refreshData = function() {
        loadRoles();
        if (selectedRoleId) {
            loadPermissions(selectedRoleId);
        }
        showAlert('Data refreshed successfully', 'success');
    };

    window.exportToPDF = function() {
        showAlert('PDF export functionality will be implemented soon', 'info');
    };

    window.exportToExcel = function() {
        showAlert('Excel export functionality will be implemented soon', 'info');
    };

    // Handle modal events
    $('#addRoleModal').on('hidden.bs.modal', function() {
        $('#addRoleForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });

    $('#editRoleModal').on('hidden.bs.modal', function() {
        $('#editRoleForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });

    // Handle window resize for responsive tables
    $(window).on('resize', function() {
        if (rolesTable) {
            rolesTable.columns.adjust().draw();
        }
        if (permissionsTable) {
            permissionsTable.columns.adjust().draw();
        }
    });
});