$(document).ready(function () {
    // Initialize Select2 with proper configuration
    $('.select2-multiple').each(function () {
        $(this).select2({
            theme: 'default',
            width: '100%',
            placeholder: 'Choose locations...',
            allowClear: true,
            closeOnSelect: false,
            tags: false,
            tokenSeparators: [',', ' '],
            language: {
                noResults: function () {
                    return "No locations found";
                },
                searching: function () {
                    return "Searching...";
                }
            }
        });
    });

    // Reset form when offcanvas is opened
    $('#offcanvas_edit').on('show.bs.offcanvas', function () {
        // Reset submit button text and state
        var submitBtn = $('#edit-user-form').find('button[type="submit"]');
        submitBtn.html('Update User').prop('disabled', false);

        // Hide any previous alerts
        $('#edit-form-alert').hide();
    });

    // Re-initialize Select2 after dynamic content is added
    $(document).on('shown.bs.offcanvas', function () {
        $('.select2-multiple').each(function () {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    theme: 'default',
                    width: '100%',
                    placeholder: 'Choose locations...',
                    allowClear: true,
                    closeOnSelect: false,
                    tags: false,
                    tokenSeparators: [',', ' ']
                });
            }
        });
    });

    // Handle form submission for editing user
    $('#edit-user-form').on('submit', function (e) {
        e.preventDefault();

        // Get form data and action URL (map numeric status to backend strings)
        var formArray = $(this).serializeArray();
        var statusMap = { '0': 'delete', '1': 'active', '2': 'deactivate', '3': 'block' };
        for (var i = 0; i < formArray.length; i++) {
            if (formArray[i].name === 'status') {
                var v = String(formArray[i].value);
                if (statusMap.hasOwnProperty(v)) {
                    formArray[i].value = statusMap[v];
                }
                break;
            }
        }
        const formData = $.param(formArray);
        const actionUrl = $(this).attr('action');

        // Disable submit button to prevent double submission
        var submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = submitBtn.html();
        submitBtn.html('Updating...').prop('disabled', true);

        $.ajax({
            url: actionUrl,
            type: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    // Show success message using inner alert element (keeps background styles)
                    var $wrapper = $('#edit-form-alert');
                    var $alert = $wrapper.find('.alert');
                    if ($alert.length === 0) {
                        $wrapper.html('<div class="alert alert-success alert-dismissible fade show" role="alert"></div>');
                        $alert = $wrapper.find('.alert');
                    }
                    $alert.removeClass('alert-danger').addClass('alert-success');
                    $alert.html('User updated successfully! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                    $wrapper.show();

                    // Update the page content with the new user data
                    updateUserDetails(response.user);

                    // Close the offcanvas after a delay (matching Index page behavior)
                    setTimeout(function () {
                        $('#offcanvas_edit').offcanvas('hide');
                    }, 2000);
                } else {
                    var $wrapper2 = $('#edit-form-alert');
                    var $alert2 = $wrapper2.find('.alert');
                    if ($alert2.length === 0) {
                        $wrapper2.html('<div class="alert alert-danger alert-dismissible fade show" role="alert"></div>');
                        $alert2 = $wrapper2.find('.alert');
                    }
                    $alert2.removeClass('alert-success').addClass('alert-danger');
                    $alert2.html(`Failed to update user: ${response.message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`);
                    $wrapper2.show();
                }
            },
            error: function (xhr) {
                console.error('Error updating user:', xhr);

                let errorMessage = 'Failed to update user. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = '<ul>';
                    for (const field in xhr.responseJSON.errors) {
                        errorMessage += `<li>${xhr.responseJSON.errors[field][0]}</li>`;
                    }
                    errorMessage += '</ul>';
                }

                var $wrapper3 = $('#edit-form-alert');
                var $alert3 = $wrapper3.find('.alert');
                if ($alert3.length === 0) {
                    $wrapper3.html('<div class="alert alert-danger alert-dismissible fade show" role="alert"></div>');
                    $alert3 = $wrapper3.find('.alert');
                }
                $alert3.removeClass('alert-success').addClass('alert-danger');
                $alert3.html(`${errorMessage} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`);
                $wrapper3.show();
            },
            complete: function () {
                // Re-enable submit button
                submitBtn.html(originalBtnText).prop('disabled', false);
            }
        });
    });

    // Function to update user details on the page without reloading
    function updateUserDetails(user) {
        // Update user name
        $('h5.mb-1:contains("' + user.full_name + '")').text(user.full_name);

        // Update username
        $('p.mb-2:contains("' + user.username + '")').text(user.username);

        // Update status badge
        var statusClass = 'badge-soft-secondary';
        var statusIcon = 'ti-circle';
        var statusText = 'Unknown';
        if (user.status == 0) {
            statusClass = 'badge-soft-secondary';
            statusIcon = 'ti-trash';
            statusText = 'Delete';
        } else if (user.status == 1) {
            statusClass = 'badge-soft-success';
            statusIcon = 'ti-check';
            statusText = 'Active';
        } else if (user.status == 2) {
            statusClass = 'badge-soft-warning';
            statusIcon = 'ti-player-pause';
            statusText = 'Deactivate';
        } else if (user.status == 3) {
            statusClass = 'badge-soft-danger';
            statusIcon = 'ti-lock';
            statusText = 'Block';
        }
        // Update header status badge by id
        $('#header-status-badge')
            .removeClass('badge-soft-success badge-soft-danger badge-soft-warning badge-soft-secondary')
            .addClass(statusClass)
            .html(`<i class="ti ${statusIcon} me-1"></i>${statusText}`);

        // Update email, mobile, employee_id
        if (user.email) {
            $('.d-inline-flex:contains("' + user.email + '")').html(
                `<i class="ti ti-mail text-info me-1"></i> ${user.email}`);
        }
        if (user.mobile) {
            $('.d-inline-flex:contains("' + user.mobile + '")').html(
                `<i class="ti ti-phone text-success me-1"></i> ${user.mobile}`);
        }
        if (user.employee_id) {
            $('.d-inline-flex:contains("' + user.employee_id + '")').html(
                `<i class="ti ti-id text-warning me-1"></i> ${user.employee_id}`);
        }

        // Update user overview section
        $('.col-md-6 .mb-4:contains("Full Name") p').text(user.full_name);
        $('.col-md-6 .mb-4:contains("Email Address") p a').text(user.email).attr('href', 'mailto:' + user.email);
        $('.col-md-6 .mb-4:contains("Username") p').text(user.username);
        $('.col-md-6 .mb-4:contains("Employee ID") p').text(user.employee_id || 'N/A');
        $('.col-md-6 .mb-4:contains("Mobile") p').text(user.mobile || 'N/A');

        // Update status in overview
        $('#overview-status-badge')
            .removeClass('badge-soft-success badge-soft-danger badge-soft-warning badge-soft-secondary')
            .addClass(statusClass)
            .text(statusText);
    }

    // Function to add new address in edit form
    function addEditAddress() {
        const container = document.getElementById('edit-addresses-container');
        const addressCount = container.children.length;

        const addressHtml = `
            <div class="address-item border rounded p-3 mb-2">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-select" name="addresses[${addressCount}][type]" required>
                            <option value="">Select Type</option>
                            <option value="Head Office">Head Office</option>
                            <option value="Branch">Branch</option>
                            <option value="Office">Office</option>
                            <option value="Warehouse">Warehouse</option>
                            <option value="Factory">Factory</option>
                            <option value="Store">Store</option>
                            <option value="Billing">Billing</option>
                            <option value="Shipping">Shipping</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="addresses[${addressCount}][address]" placeholder="Address">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="addresses[${addressCount}][city]" placeholder="City">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="addresses[${addressCount}][state]" placeholder="State">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="addresses[${addressCount}][country]" placeholder="Country">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="addresses[${addressCount}][zip_code]" placeholder="Zip Code">
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="this.parentElement.remove()">
                    <i class="ti ti-trash me-1"></i>Remove
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', addressHtml);
    }

    // Function to add new contact in edit form
    function addEditContact() {
        const container = document.getElementById('edit-contacts-container');
        const contactCount = container.children.length;

        const contactHtml = `
            <div class="contact-item border rounded p-3 mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="contacts[${contactCount}][name]" placeholder="Contact Name">
                    </div>
                    <div class="col-md-4">
                        <input type="email" class="form-control" name="contacts[${contactCount}][email]" placeholder="Email">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="contacts[${contactCount}][phone]" placeholder="Phone">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="contacts[${contactCount}][designation]" placeholder="Designation">
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="contacts[${contactCount}][is_primary]" value="1">
                            <label class="form-check-label">Primary Contact</label>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="this.parentElement.remove()">
                    <i class="ti ti-trash me-1"></i>Remove
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', contactHtml);
    }

    // Function to add new note in edit form
    function addEditNote() {
        const container = document.getElementById('edit-notes-container');
        const noteCount = container.children.length;

        const noteHtml = `
            <div class="note-item border rounded p-3 mb-2">
                <div class="row">
                    <div class="col-md-9">
                        <textarea class="form-control" name="notes[${noteCount}][note]" rows="3" placeholder="Note content"></textarea>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="notes[${noteCount}][status]">
                            <option value="0">Delete</option>
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            <option value="3">Block</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="this.parentElement.remove()">
                    <i class="ti ti-trash me-1"></i>Remove
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', noteHtml);
    }

    // Make functions globally available
    window.addEditAddress = addEditAddress;
    window.addEditContact = addEditContact;
    window.addEditNote = addEditNote;

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle collapse header button
    $('#collapse-header').on('click', function () {
        const icon = $(this).find('i');
        if (icon.hasClass('ti-transition-top')) {
            icon.removeClass('ti-transition-top').addClass('ti-transition-bottom');
        } else {
            icon.removeClass('ti-transition-bottom').addClass('ti-transition-top');
        }
    });

    // Handle refresh button
    $('.btn-outline-info[aria-label="Refresh"]').on('click', function () {
        location.reload();
    });

    // Handle export dropdown items
    $('.dropdown-item').on('click', function (e) {
        e.preventDefault();
        const exportType = $(this).text().toLowerCase();
        if (exportType.includes('pdf')) {
            // Handle PDF export
            console.log('PDF export functionality to be implemented');
        } else if (exportType.includes('excel')) {
            // Handle Excel export
            console.log('Excel export functionality to be implemented');
        }
    });
});
