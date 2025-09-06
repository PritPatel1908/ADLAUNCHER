$(document).ready(function () {
    let permissionTable;
    let currentRoleId = null;
    let currentRoleName = '';
    let permissionsData = [];
    let existingPermissions = {};

    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize the page
    initializePage();

    function initializePage() {
        loadRoles();
        initializeEventHandlers();
        checkForRoleId();
    }

    function checkForRoleId() {
        // Check if role_id is passed in URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const roleId = urlParams.get('role_id');

        if (roleId) {
            // Show loading message
            showAlert('Loading role permissions...', 'info');

            // Wait for roles to load, then select the role
            setTimeout(() => {
                $('#role-selector').val(roleId).trigger('change');
                // Automatically click manage permissions button
                setTimeout(() => {
                    $('#manage-permissions-btn').click();
                    // Clear the URL parameter after loading
                    window.history.replaceState({}, document.title, window.location.pathname);
                }, 500);
            }, 1000);
        }
    }

    function loadRoles() {
        $.ajax({
            url: '/roles/data',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    populateRoleSelector(response.data);
                } else {
                    showAlert('Error loading roles: ' + response.message, 'danger');
                }
            },
            error: function(xhr) {
                showAlert('Error loading roles', 'danger');
            }
        });
    }

    function populateRoleSelector(roles) {
        const selector = $('#role-selector');
        selector.empty().append('<option value="">Select a Role</option>');

        roles.forEach(role => {
            selector.append(`<option value="${role.id}">${role.role_name}</option>`);
        });
    }

    function initializeEventHandlers() {
        // Role selector change
        $('#role-selector').on('change', function() {
            const roleId = $(this).val();
            const roleName = $(this).find('option:selected').text();

            if (roleId) {
                currentRoleId = roleId;
                currentRoleName = roleName;
                $('#manage-permissions-btn').prop('disabled', false);
                $('#selected-role-name').text(roleName);
            } else {
                currentRoleId = null;
                currentRoleName = '';
                $('#manage-permissions-btn').prop('disabled', true);
                $('#permission-card').hide();
            }
        });

        // Manage permissions button
        $('#manage-permissions-btn').on('click', function() {
            if (currentRoleId) {
                loadPermissions(currentRoleId);
                $('#permission-card').show();
            }
        });

        // Save permissions button
        $('#save-permissions').on('click', function() {
            savePermissions();
        });

        // Select all modules checkbox
        $('#select-all-modules').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.module-checkbox').prop('checked', isChecked);
            $('.permission-checkbox').prop('checked', isChecked);
        });

        // Select all checkbox in table
        $('#select-all').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
        });
    }

    function loadPermissions(roleId) {
        $.ajax({
            url: `/roles/${roleId}/permissions`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    permissionsData = response.data;
                    existingPermissions = response.permissions;
                    initializePermissionTable();
                } else {
                    showAlert('Error loading permissions: ' + response.message, 'danger');
                }
            },
            error: function(xhr) {
                showAlert('Error loading permissions', 'danger');
            }
        });
    }

    function initializePermissionTable() {
        if (permissionTable) {
            permissionTable.destroy();
        }

        permissionTable = $('#permission_list').DataTable({
			"bFilter": false,
				"bInfo": false,
					"ordering": true,
				"autoWidth": true,
            "paging": false,
            "data": permissionsData,
            "columns": [
                {
                    "render": function (data, type, row) {
                        return `<div class="form-check form-check-md">
                                    <input class="form-check-input row-checkbox" type="checkbox" data-module="${row.sub_module}">
                                </div>`;
                    }
                },
                { "data": "module" },
                { "data": "sub_module" },
                {
                    "render": function (data, type, row) {
                        const isChecked = existingPermissions[row.sub_module]?.create || false;
                        return `<div class="form-check form-check-md">
                                    <input class="form-check-input permission-checkbox create-checkbox" type="checkbox"
                                           data-module="${row.sub_module}" data-permission="create" ${isChecked ? 'checked' : ''}>
                                </div>`;
                    }
                },
                {
                    "render": function (data, type, row) {
                        const isChecked = existingPermissions[row.sub_module]?.edit || false;
                        return `<div class="form-check form-check-md">
                                    <input class="form-check-input permission-checkbox edit-checkbox" type="checkbox"
                                           data-module="${row.sub_module}" data-permission="edit" ${isChecked ? 'checked' : ''}>
                                </div>`;
                    }
                },
                {
                    "render": function (data, type, row) {
                        const isChecked = existingPermissions[row.sub_module]?.view || false;
                        return `<div class="form-check form-check-md">
                                    <input class="form-check-input permission-checkbox view-checkbox" type="checkbox"
                                           data-module="${row.sub_module}" data-permission="view" ${isChecked ? 'checked' : ''}>
                                </div>`;
                    }
                },
                {
                    "render": function (data, type, row) {
                        const isChecked = existingPermissions[row.sub_module]?.delete || false;
                        return `<div class="form-check form-check-md">
                                    <input class="form-check-input permission-checkbox delete-checkbox" type="checkbox"
                                           data-module="${row.sub_module}" data-permission="delete" ${isChecked ? 'checked' : ''}>
                                </div>`;
                    }
                },
                {
                    "render": function (data, type, row) {
                        const hasAllPermissions = existingPermissions[row.sub_module] &&
                            existingPermissions[row.sub_module].create &&
                            existingPermissions[row.sub_module].edit &&
                            existingPermissions[row.sub_module].view &&
                            existingPermissions[row.sub_module].delete;
                        return `<div class="form-check form-check-md">
                                    <input class="form-check-input module-checkbox" type="checkbox"
                                           data-module="${row.sub_module}" ${hasAllPermissions ? 'checked' : ''}>
                                </div>`;
                    }
                }
            ],
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
                initializePermissionCheckboxes();
            }
        });
    }

    function initializePermissionCheckboxes() {
        // Module checkbox (Allow All) - when checked, check all permissions for that module
        $('.module-checkbox').on('change', function() {
            const module = $(this).data('module');
            const isChecked = $(this).is(':checked');

            $(`.permission-checkbox[data-module="${module}"]`).prop('checked', isChecked);
        });

        // Individual permission checkboxes - when all are checked, check the module checkbox
        $('.permission-checkbox').on('change', function() {
            const module = $(this).data('module');
            const moduleCheckbox = $(`.module-checkbox[data-module="${module}"]`);
            const allPermissions = $(`.permission-checkbox[data-module="${module}"]`);
            const checkedPermissions = $(`.permission-checkbox[data-module="${module}"]:checked`);

            if (checkedPermissions.length === allPermissions.length) {
                moduleCheckbox.prop('checked', true);
            } else {
                moduleCheckbox.prop('checked', false);
            }
        });

        // Row checkbox - when checked, check all permissions for that row
        $('.row-checkbox').on('change', function() {
            const module = $(this).data('module');
            const isChecked = $(this).is(':checked');

            $(`.permission-checkbox[data-module="${module}"]`).prop('checked', isChecked);
            $(`.module-checkbox[data-module="${module}"]`).prop('checked', isChecked);
        });
    }

    function savePermissions() {
        if (!currentRoleId) {
            showAlert('Please select a role first', 'warning');
            return;
        }

        const permissions = [];

        $('.permission-checkbox').each(function() {
            const module = $(this).data('module');
            const permission = $(this).data('permission');
            const isChecked = $(this).is(':checked');

            // Find or create permission object for this module
            let permissionObj = permissions.find(p => p.modules === module);
            if (!permissionObj) {
                permissionObj = {
                    modules: module,
                    view: false,
                    create: false,
                    edit: false,
                    delete: false,
                    import: false,
                    export: false
                };
                permissions.push(permissionObj);
            }

            permissionObj[permission] = isChecked;
        });

        // Show loading state
        const saveBtn = $('#save-permissions');
        const originalText = saveBtn.html();
        saveBtn.html('<i class="ti ti-loader me-1"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: `/roles/${currentRoleId}/permissions`,
            type: 'POST',
            data: {
                permissions: permissions
            },
            success: function(response) {
                if (response.success) {
                    showAlert('Permissions saved successfully!', 'success');
                    // Reload permissions to reflect changes
                    loadPermissions(currentRoleId);
                } else {
                    showAlert('Error saving permissions: ' + response.message, 'danger');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error saving permissions';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'danger');
            },
            complete: function() {
                saveBtn.html(originalText).prop('disabled', false);
            }
        });
    }

    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Remove existing alerts
        $('.alert').remove();

        // Add new alert at the top of the content
        $('.content .container-fluid').prepend(alertHtml);

        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Refresh button functionality
    $('a[data-bs-original-title="Refresh"]').on('click', function() {
        if (currentRoleId) {
            loadPermissions(currentRoleId);
        } else {
            loadRoles();
        }
    });

});
