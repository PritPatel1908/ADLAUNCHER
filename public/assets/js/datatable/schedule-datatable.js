$(document).ready(function () {
    // Initialize column visibility from database
    let columnVisibility = {};

    // Set default sort option
    window.currentSortBy = 'newest';

    // Apply initial CSS to hide columns that should be hidden
    function applyInitialColumnVisibility() {
        // Initialize all columns as visible by default
        columnVisibility = {
            'schedule_name': true,
            'device': true,
            'layout': true,
            'screen': true,
            'start_at': true,
            'end_at': true,
            'created_at': true,
            'action': true
        };

        // Get saved column visibility from localStorage if available
        const savedVisibility = localStorage.getItem('scheduleColumnVisibility');
        if (savedVisibility) {
            try {
                const parsed = JSON.parse(savedVisibility);
                // Only update if it's a valid object
                if (parsed && typeof parsed === 'object') {
                    // Ensure we're not mixing up columns - process each column independently
                    for (var column in parsed) {
                        if (column in columnVisibility) {
                            columnVisibility[column] = parsed[column];
                        }
                    }
                    console.log('Applied initial column visibility from localStorage:', columnVisibility);
                }

                // We'll apply CSS to hide columns after DataTable is initialized
                // Store the visibility state for now, and it will be applied when DataTable is created
                Object.keys(columnVisibility).forEach(function (column) {
                    if (!columnVisibility[column]) {
                        console.log('Column will be hidden on initialization:', column);
                    }
                });
            } catch (e) {
                console.error('Error parsing saved column visibility:', e);
            }
        }
    }

    // Apply initial visibility on page load before AJAX
    applyInitialColumnVisibility();

    function initializeSelect2() {
        $('select[data-toggle="select2"]').each(function () {
            if (!$(this).hasClass('select2-initialized')) {
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

    initializeSelect2();
    $(document).on('shown.bs.offcanvas', function () {
        setTimeout(initializeSelect2, 100);
    });

    // Function to save column visibility preference
    function saveColumnVisibility(column, isVisible) {
        console.log('Saving column visibility for:', column, 'to:', isVisible);

        // Update local state for the specific column only
        columnVisibility[column] = isVisible;

        // Save to localStorage for immediate use on next page load
        localStorage.setItem('scheduleColumnVisibility', JSON.stringify(columnVisibility));

        // Save to server
        $.ajax({
            url: '/columns',
            type: 'POST',
            data: {
                table: 'schedules',
                column_name: column,  // Only save this specific column
                is_show: isVisible ? 1 : 0  // Convert boolean to 1/0 for Laravel validation
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log(`Column visibility for ${column} saved:`, response);
            },
            error: function (xhr) {
                console.error(`Error saving column visibility for ${column}:`, xhr);
                console.log('Response:', xhr.responseJSON);

                // Revert the UI change if server save fails
                $('.column-visibility-toggle[data-column="' + column + '"]').prop('checked', !isVisible);

                // Also revert the local state and localStorage
                columnVisibility[column] = !isVisible;
                localStorage.setItem('scheduleColumnVisibility', JSON.stringify(columnVisibility));

                // Revert the DataTable column visibility if available
                var columnIndex = scheduleTable ? scheduleTable.column(function (idx, data, node) {
                    return data.name === column;
                }).index() : undefined;
                if (columnIndex !== undefined) {
                    scheduleTable.column(columnIndex).visible(!isVisible);
                }

                // Show error to user
                alert(`Failed to save column preference for ${column}. Please try again.`);
            }
        });
    }

    // Function to load column visibility preferences
    function loadColumnVisibility() {
        return $.ajax({
            url: '/columns',
            type: 'GET',
            data: { table: 'schedules' },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // Initialize all columns as visible by default
                columnVisibility = {
                    'schedule_name': true,
                    'device': true,
                    'layout': true,
                    'screen': true,
                    'start_at': true,
                    'end_at': true,
                    'created_at': true,
                    'action': true
                };

                // Update with saved preferences
                if (response.success && response.columns && response.columns.length > 0) {
                    response.columns.forEach(function (col) {
                        // Convert to boolean if needed
                        let isVisible = col.is_show;
                        if (typeof isVisible !== 'boolean') {
                            isVisible = isVisible === 1 || isVisible === '1' || isVisible === true || isVisible === 'true';
                        }

                        // Ensure we're updating the correct column and not affecting others
                        if (col.column_name in columnVisibility) {
                            // Important: Only update the specific column, don't affect others
                            columnVisibility[col.column_name] = isVisible;
                        }
                    });

                    // Save to localStorage for immediate use on next page load
                    localStorage.setItem('scheduleColumnVisibility', JSON.stringify(columnVisibility));
                } else {
                    // If no server data, try to load from localStorage
                    const savedVisibility = localStorage.getItem('scheduleColumnVisibility');
                    if (savedVisibility) {
                        try {
                            const parsed = JSON.parse(savedVisibility);
                            if (parsed && typeof parsed === 'object') {
                                // Merge with defaults, only updating existing columns
                                for (var column in parsed) {
                                    if (column in columnVisibility) {
                                        columnVisibility[column] = parsed[column];
                                    }
                                }
                                console.log('Applied column visibility from localStorage:', columnVisibility);
                            }
                        } catch (e) {
                            console.error('Error parsing saved column visibility:', e);
                        }
                    }
                }

                // Update toggle switches in the UI
                updateColumnToggles();
                console.log('Loaded column visibility:', columnVisibility);
            },
            error: function (xhr) {
                console.error('Error loading column visibility:', xhr);
                console.log('Response:', xhr.responseJSON);

                // On error, try to load from localStorage as fallback
                const savedVisibility = localStorage.getItem('scheduleColumnVisibility');
                if (savedVisibility) {
                    try {
                        const parsed = JSON.parse(savedVisibility);
                        if (parsed && typeof parsed === 'object') {
                            // Merge with defaults, only updating existing columns
                            for (var column in parsed) {
                                if (column in columnVisibility) {
                                    columnVisibility[column] = parsed[column];
                                }
                            }
                            console.log('Applied column visibility from localStorage (fallback):', columnVisibility);
                        }
                    } catch (e) {
                        console.error('Error parsing saved column visibility:', e);
                    }
                }

                // Update toggle switches in the UI
                updateColumnToggles();
            }
        });
    }

    // Function to update column toggle switches based on loaded preferences
    function updateColumnToggles() {
        console.log('Updating column toggles with current state:', columnVisibility);

        // Update each toggle based on current visibility state
        // Process each column independently to avoid any cross-influence
        $('.column-visibility-toggle').each(function () {
            const column = $(this).data('column');
            if (column in columnVisibility) {
                $(this).prop('checked', columnVisibility[column]);
                console.log('Setting toggle for', column, 'to', columnVisibility[column]);
            }
        });
    }

    // Load column visibility preferences before initializing DataTable
    // Ensure the table initializes even if the columns API fails
    var loadReq = loadColumnVisibility();
    if (loadReq && typeof loadReq.always === 'function') {
        loadReq.always(function () {
            if ($('#scheduleslist').length > 0) {
                initializeDataTable();
            }
        });
    } else {
        // Fallback in case $.ajax compatibility changes
        if ($('#scheduleslist').length > 0) {
            initializeDataTable();
        }
    }

    function initializeDataTable() {
        // Show loading indicator
        $('#error-container').hide();

        // Initialize the DataTable - use window scope to ensure it's accessible everywhere
        window.scheduleTable = $('#scheduleslist').DataTable({
            processing: true,
            serverSide: true,
            bFilter: false,
            bInfo: false,
            ordering: true,
            autoWidth: true,
            order: [[0, 'asc']],
            orderCellsTop: true,
            ajax: {
                url: '/schedules/data',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function (xhr) {
                    console.log('Sending AJAX request to:', this.url);
                    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
                },
                data: function (d) {
                    // Add custom filter parameters
                    d.name_filter = $('.schedule-filter[data-column="schedule_name"]').val();
                    d.device_filter = $('.schedule-filter[data-column="device"]').val();

                    // Add date range filter parameters
                    var dateRange = $('#reportrange span').text().split(' - ');
                    if (dateRange.length === 2) {
                        d.start_date = moment(dateRange[0], 'D MMM YY').format('YYYY-MM-DD');
                        d.end_date = moment(dateRange[1], 'D MMM YY').format('YYYY-MM-DD');
                    }

                    // Add sort by parameter
                    d.sort_by = window.currentSortBy || 'newest';

                    return d;
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTable AJAX error:', xhr.responseText);
                    console.error('Error details:', error, thrown);

                    // Hide loading indicator
                    $('.data-loading').hide();

                    // Show error container
                    $('#error-container').show();

                    // Set error message
                    let errorMessage = 'Failed to load schedule data. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 0) {
                        errorMessage = 'Network error. Please check your connection.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Schedules endpoint not found. Please contact administrator.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Please try again later.';
                    }

                    $('#error-message').text(errorMessage);

                    // Show a more user-friendly error dialog
                    if (typeof alert !== 'undefined') {
                        alert(errorMessage);
                    }
                }
            },
            columns: [
                { data: 'schedule_name', name: 'schedule_name', orderable: true },
                { data: 'device', name: 'device', orderable: true },
                { data: 'layout', name: 'layout', orderable: true },
                { data: 'screen', name: 'screen', orderable: true },
                { data: 'start_at', name: 'start_at', orderable: true, render: function (data) { return data ? new Date(data).toLocaleString() : 'N/A'; } },
                { data: 'end_at', name: 'end_at', orderable: true, render: function (data) { return data ? new Date(data).toLocaleString() : 'N/A'; } },
                { data: 'created_at', name: 'created_at', orderable: true, render: function (data) { return data ? new Date(data).toLocaleString() : 'N/A'; } },
                {
                    data: 'id', orderable: false, name: 'action', render: function (data) {
                        let actions = [];

                        // Check permissions and add actions accordingly
                        if (window.schedulePermissions && window.schedulePermissions.view) {
                            actions.push(`<a class="dropdown-item" href="/schedule/${data}"><i class="ti ti-eye me-2"></i>View</a>`);
                        }

                        if (window.schedulePermissions && window.schedulePermissions.edit) {
                            actions.push(`<a class="dropdown-item edit-schedule" href="javascript:void(0);" data-id="${data}"><i class="ti ti-edit text-blue"></i> Edit</a>`);
                        }

                        if (window.schedulePermissions && window.schedulePermissions.delete) {
                            actions.push(`<a class="dropdown-item delete-schedule" href="javascript:void(0);" data-id="${data}"><i class="ti ti-trash me-2"></i>Delete</a>`);
                        }

                        // If no actions available, return empty
                        if (actions.length === 0) {
                            return '<span class="text-muted">No actions</span>';
                        }

                        return `
                        <div class="dropdown dropdown-action">
                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
                            <div class="dropdown-menu dropdown-menu-end">
                                ${actions.join('')}
                            </div>
                        </div>`;
                    }
                }
            ],
            language: {
                search: ' ', sLengthMenu: '_MENU_', searchPlaceholder: 'Search', info: '_START_ - _END_ of _TOTAL_ items', lengthMenu: 'Show _MENU_ entries',
                paginate: { next: '<i class="ti ti-chevron-right"></i> ', previous: '<i class="ti ti-chevron-left"></i> ' },
            },
            initComplete: function (settings, json) {
                // Hide loading indicator
                $('.data-loading').hide();

                $('.dataTables_paginate').appendTo('.datatable-paginate');
                $('.dataTables_length').appendTo('.datatable-length');
                $('#error-container').hide();

                // Apply column visibility after DataTable is fully initialized
                setTimeout(function () {
                    Object.keys(columnVisibility).forEach(function (column) {
                        const isVisible = columnVisibility[column];

                        // Find the correct column index by matching the column name
                        let columnIndex = null;
                        scheduleTable.columns().every(function (index) {
                            const colName = this.settings()[0].aoColumns[index].name;
                            if (colName === column) {
                                columnIndex = index;
                                return false; // Break the loop
                            }
                        });

                        if (columnIndex !== null) {
                            // Set column visibility in DataTable
                            scheduleTable.column(columnIndex).visible(isVisible, false);
                            console.log('Applied column visibility in initComplete for column:', column, 'with index:', columnIndex, 'to:', isVisible);
                        } else {
                            console.error('Column not found for visibility in initComplete:', column);
                        }
                    });

                    // Redraw the table after applying all column visibility changes
                    scheduleTable.columns.adjust().draw(false);
                }, 200);

                // Initialize sort indicators for default sort
                const initialOrder = this.api().order();
                console.log('Initial order data structure:', JSON.stringify(initialOrder));
                console.log('Available headers at init:', $('#scheduleslist thead th').length);

                if (initialOrder && initialOrder.length > 0) {
                    const columnIndex = initialOrder[0][0];
                    const direction = initialOrder[0][1];
                    console.log('Initial sorting column index:', columnIndex, 'direction:', direction);

                    // Delay the indicator update slightly to ensure the DOM is ready
                    setTimeout(function () {
                        updateSortIndicators(columnIndex, direction);
                    }, 100);
                } else {
                    console.log('No initial order information available');
                }

                // Add click event for sorting after initialization
                this.api().on('order.dt', function (e, settings) {
                    // Use the API instance provided by the event context
                    const api = new $.fn.dataTable.Api(settings);
                    const order = api.order();
                    console.log('DataTable order event triggered');
                    console.log('Order data structure:', JSON.stringify(order));

                    if (order && order.length > 0) {
                        const columnIndex = parseInt(order[0][0]);
                        const direction = order[0][1];
                        console.log('Sorting column index:', columnIndex, 'direction:', direction);
                        console.log('Available headers:', $('#scheduleslist thead th').length);
                        updateSortIndicators(columnIndex, direction);
                    } else {
                        console.error('No order information available');
                    }
                });
            }
        });

        // Store DataTable instance in window for global access
        window.scheduleDataTable = scheduleTable;

        // Handle column visibility toggle
        $(document).on('change', '.column-visibility-toggle', function (e) {
            // Stop event propagation to prevent affecting other columns
            e.stopPropagation();

            const column = $(this).data('column');
            const isVisible = $(this).prop('checked');

            // Find the correct column index by matching the column name
            let columnIndex = null;
            scheduleTable.columns().every(function (index) {
                const colData = this.dataSrc();
                const colName = this.settings()[0].aoColumns[index].name;
                if (colName === column) {
                    columnIndex = index;
                    return false; // Break the loop
                }
            });

            console.log('Toggling column visibility for:', column, 'with index:', columnIndex, 'to:', isVisible);

            if (columnIndex !== null) {
                // Remove any existing style for this column
                $(`style#column-style-${column}`).remove();

                // Update DataTable column visibility using the API
                scheduleTable.column(columnIndex).visible(isVisible, false);

                // Adjust the table layout after changing visibility
                scheduleTable.columns.adjust().draw(false);

                // Save preference to database for this column only
                saveColumnVisibility(column, isVisible);
            } else {
                console.error('Column not found:', column);
            }
        });

        // Add event listeners for live filtering
        $('.schedule-filter').on('keyup', function () {
            $('#error-container').hide();
            scheduleTable.ajax.reload();
        });

        // Add event listener for date range picker
        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            $('#error-container').hide();
            scheduleTable.ajax.reload();
        });

        // Add event listener for sort options
        $(document).on('click', '.sort-option', function () {
            const sortBy = $(this).data('sort');
            const sortText = $(this).text();
            window.currentSortBy = sortBy;

            // Update the dropdown button text to show current sort option
            $('.dropdown-toggle.btn-outline-light').first().html(`<i class="ti ti-sort-ascending-2 me-2"></i>${sortText}`);

            // Show loading and reload the DataTable with the new sort option
            $('#error-container').hide();
            scheduleTable.ajax.reload();
        });

        // Function to update sort indicators in the table header
        function updateSortIndicators(columnIndex, direction) {
            console.log('Updating sort indicators for column:', columnIndex, 'direction:', direction);

            // First, remove all existing sort indicators
            $('#scheduleslist thead th').removeClass('sorting_asc sorting_desc').addClass('sorting');

            // Then, add the appropriate class to the sorted column
            const $thElement = $(`#scheduleslist thead th:eq(${columnIndex})`);
            $thElement.removeClass('sorting');
            $thElement.addClass(direction === 'asc' ? 'sorting_asc' : 'sorting_desc');
        }

        // Handle retry button click
        $(document).on('click', '#retry-load', function () {
            $('#error-container').hide();
            scheduleTable.ajax.reload();
        });

        // Handle create schedule
        $('#create-schedule-form').on('submit', function (e) {
            e.preventDefault();
            console.log('Form submission started');

            // Ensure Schedule Info section is expanded
            $('#basic').collapse('show');

            // Client-side validation
            var scheduleName = $('input[name="schedule_name"]').val();
            var startDate = $('input[name="schedule_start_date_time"]').val();
            var endDate = $('input[name="schedule_end_date_time"]').val();
            var deviceId = $('select[name="device_id"]').val();

            if (!scheduleName) {
                alert('Please enter a schedule name');
                return;
            }
            if (!startDate) {
                alert('Please select a start date and time');
                return;
            }
            if (!endDate) {
                alert('Please select an end date and time');
                return;
            }
            if (!deviceId) {
                alert('Please select a device');
                return;
            }

            console.log('All required fields validated successfully');

            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            submitBtn.html('Creating...').prop('disabled', true);

            // Use FormData for file uploads
            var formData = new FormData(this);

            // Debug: Log form data
            console.log('Form data being sent:');
            for (var pair of formData.entries()) {
                if (pair[1] instanceof File) {
                    console.log(pair[0] + ': [File] ' + pair[1].name + ' (' + pair[1].size + ' bytes)');
                } else {
                    console.log(pair[0] + ': ' + pair[1]);
                }
            }

            // Debug: Check if required fields are present
            console.log('Required field validation:');
            console.log('Schedule Name:', scheduleName);
            console.log('Start Date:', startDate);
            console.log('End Date:', endDate);
            console.log('Device ID:', deviceId);

            // Debug: Check media data
            console.log('Media titles:', $('input[name="media_title[]"]').map(function () { return this.value; }).get());
            console.log('Media types:', $('select[name="media_type[]"]').map(function () { return this.value; }).get());
            console.log('Media files:', $('input[name="media_file[]"]').map(function () { return this.files[0] ? this.files[0].name : 'No file'; }).get());

            // Check file sizes
            $('input[name="media_file[]"]').each(function (index) {
                if (this.files[0]) {
                    var file = this.files[0];
                    console.log('File ' + index + ':', file.name, 'Size:', file.size, 'bytes', 'Type:', file.type);
                    if (file.size > 200 * 1024 * 1024) { // 200MB limit
                        alert('File ' + file.name + ' is too large. Maximum size is 200MB.');
                        submitBtn.html(originalBtnText).prop('disabled', false);
                        return false;
                    }
                }
            });

            $.ajax({
                url: '/schedule',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                timeout: 60000, // 60 second timeout for file uploads
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                beforeSend: function () {
                    console.log('AJAX request being sent to /schedule');
                },
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total * 100;
                            console.log('Upload progress: ' + percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function (response) {
                    console.log('Schedule creation response:', response);
                    if (response.success) {
                        var $wrap = $('#create-form-alert');
                        var $alert = $wrap.find('.alert');
                        if ($alert.length === 0) { $wrap.html('<div class="alert alert-success alert-dismissible fade show" role="alert"></div>'); $alert = $wrap.find('.alert'); }
                        $alert.removeClass('alert-danger').addClass('alert-success');
                        $alert.html('Schedule created successfully! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                        $wrap.show();
                        $('#create-schedule-form')[0].reset();
                        scheduleTable.ajax.reload();
                        setTimeout(function () { $('#offcanvas_add').offcanvas('hide'); }, 1200);
                    } else {
                        console.log('Schedule creation failed:', response);
                        alert(response.message || 'Failed to create schedule');
                    }
                },
                error: function (xhr) {
                    console.error('Schedule creation error:', xhr);
                    const res = xhr.responseJSON;
                    let msg = 'Error creating schedule.';
                    if (res && res.errors) {
                        // Show all validation errors
                        const errorMessages = [];
                        Object.keys(res.errors).forEach(field => {
                            errorMessages.push(`${field}: ${res.errors[field][0]}`);
                        });
                        msg = errorMessages.join('\n');
                    } else if (res && res.message) {
                        msg = res.message;
                    } else {
                        // Check for common issues
                        if (xhr.status === 422) {
                            msg = 'Validation failed. Please check all required fields are filled.';
                        } else if (xhr.status === 500) {
                            msg = 'Server error. Please try again.';
                        } else if (xhr.status === 0) {
                            msg = 'Network error. Please check your connection.';
                        } else if (xhr.statusText === 'timeout') {
                            msg = 'Request timed out. Please try again.';
                        }
                    }
                    alert(msg);
                },
                complete: function (xhr, status) {
                    console.log('AJAX request completed with status:', status);
                    submitBtn.html(originalBtnText).prop('disabled', false);
                }
            });
        });

        // Edit schedule
        $(document).on('click', '.edit-schedule', function () {
            const id = $(this).data('id');
            if (!id) return;

            // Show loading state
            $('#offcanvas_edit').offcanvas('show');

            // Fetch schedule data
            $.ajax({
                url: `/schedule/${id}/edit`,
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    console.log('Schedule edit response:', response);
                    if (response.success && response.schedule) {
                        const schedule = response.schedule;

                        // Populate form fields
                        $('#edit-schedule_name').val(schedule.schedule_name || '');

                        // Format datetime values for datetime-local input (preserve local timezone)
                        if (schedule.schedule_start_date_time) {
                            const startDate = new Date(schedule.schedule_start_date_time);
                            // Use local timezone formatting instead of UTC
                            const year = startDate.getFullYear();
                            const month = String(startDate.getMonth() + 1).padStart(2, '0');
                            const day = String(startDate.getDate()).padStart(2, '0');
                            const hours = String(startDate.getHours()).padStart(2, '0');
                            const minutes = String(startDate.getMinutes()).padStart(2, '0');
                            const startFormatted = `${year}-${month}-${day}T${hours}:${minutes}`;
                            $('#edit-schedule_start_date_time').val(startFormatted);
                        } else {
                            $('#edit-schedule_start_date_time').val('');
                        }

                        if (schedule.schedule_end_date_time) {
                            const endDate = new Date(schedule.schedule_end_date_time);
                            // Use local timezone formatting instead of UTC
                            const year = endDate.getFullYear();
                            const month = String(endDate.getMonth() + 1).padStart(2, '0');
                            const day = String(endDate.getDate()).padStart(2, '0');
                            const hours = String(endDate.getHours()).padStart(2, '0');
                            const minutes = String(endDate.getMinutes()).padStart(2, '0');
                            const endFormatted = `${year}-${month}-${day}T${hours}:${minutes}`;
                            $('#edit-schedule_end_date_time').val(endFormatted);
                        } else {
                            $('#edit-schedule_end_date_time').val('');
                        }

                        // Set device
                        if (schedule.device_id) {
                            $('#edit-device_id').val(schedule.device_id).trigger('change');
                        }

                        // Set play_forever checkbox
                        if (schedule.play_forever) {
                            $('#edit-play_forever').prop('checked', true);
                        } else {
                            $('#edit-play_forever').prop('checked', false);
                        }

                        // Set form action
                        $('#edit-schedule-form').attr('action', `/schedule/${id}`);

                        // Load layouts and screens after device is set
                        if (schedule.device_id) {
                            loadLayoutsForEdit(schedule.device_id, schedule.layout_id, schedule.screen_id);
                        }

                        // Load existing media data
                        loadExistingMedia(schedule.medias || []);
                    } else {
                        alert('Failed to load schedule data');
                        $('#offcanvas_edit').offcanvas('hide');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Schedule edit error:', xhr, status, error);
                    alert('Failed to load schedule data. Please try again.');
                    $('#offcanvas_edit').offcanvas('hide');
                }
            });
        });

        // Delete schedule
        $(document).on('click', '.delete-schedule', function () {
            const id = $(this).data('id');
            if (!id) return;
            if (confirm('Are you sure you want to delete this schedule?')) {
                $.ajax({
                    url: `/schedule/${id}`, type: 'DELETE', headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        if (response.success) {
                            scheduleTable.ajax.reload(null, false);
                            alert('Schedule deleted successfully!');
                        } else {
                            alert(response.message || 'Failed to delete schedule.');
                        }
                    },
                    error: function () { alert('Failed to delete schedule. Please try again.'); }
                });
            }
        });

        // Helper function to load existing media data
        function loadExistingMedia(medias) {
            const $container = $('#edit-media-container');
            $container.empty();

            if (medias && medias.length > 0) {
                medias.forEach(function (media) {
                    const mediaHtml = `
                        <div class="media-item border rounded p-3 mb-3">
                            <input type="hidden" name="edit_media_id[]" value="${media.id}">
                            <div class="row">
                                                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Media Title</label>
                                    <input type="text" class="form-control" name="edit_media_title[]"
                                        value="${media.title || ''}" placeholder="Enter media title">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Media Type</label>
                                    <select class="form-control select2" name="edit_media_type[]"
                                        data-toggle="select2">
                                        <option value="">Select type...</option>
                                        <option value="image" ${media.media_type === 'image' ? 'selected' : ''}>Image</option>
                                        <option value="video" ${media.media_type === 'video' ? 'selected' : ''}>Video</option>
                                        <option value="audio" ${media.media_type === 'audio' ? 'selected' : ''}>Audio</option>
                                        <option value="mp4" ${media.media_type === 'mp4' ? 'selected' : ''}>MP4</option>
                                        <option value="png" ${media.media_type === 'png' ? 'selected' : ''}>PNG</option>
                                        <option value="jpg" ${media.media_type === 'jpg' ? 'selected' : ''}>JPG</option>
                                        <option value="pdf" ${media.media_type === 'pdf' ? 'selected' : ''}>PDF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm w-100 remove-media">
                                        <i class="ti ti-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Media File</label>
                                        <input type="file" class="form-control" name="edit_media_file[]"
                                            accept="image/*,video/*,audio/*,.mp4,.png,.jpg,.pdf">
                                        ${media.media_file ? `<small class="text-muted">Current: ${media.media_file}</small>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $container.append(mediaHtml);
                });
            } else {
                // Add one empty media item if no existing media
                const mediaHtml = `
                    <div class="media-item border rounded p-3 mb-3">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Media Title</label>
                                    <input type="text" class="form-control" name="edit_media_title[]"
                                        placeholder="Enter media title">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Media Type</label>
                                    <select class="form-control select2" name="edit_media_type[]"
                                        data-toggle="select2">
                                        <option value="">Select type...</option>
                                        <option value="image">Image</option>
                                        <option value="video">Video</option>
                                        <option value="audio">Audio</option>
                                        <option value="mp4">MP4</option>
                                        <option value="png">PNG</option>
                                        <option value="jpg">JPG</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm w-100 remove-media">
                                        <i class="ti ti-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Media File</label>
                                    <input type="file" class="form-control" name="edit_media_file[]"
                                        accept="image/*,video/*,audio/*,.mp4,.png,.jpg,.pdf">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $container.append(mediaHtml);
            }

            // Initialize select2 for the new elements
            initializeSelect2();
        }

        // Helper function to load layouts for edit form
        function loadLayoutsForEdit(deviceId, selectedLayoutId, selectedScreenId) {
            var $layout = $('#edit-layout_id');
            var $screen = $('#edit-screen_id');

            $layout.empty().append('<option value="">Loading...</option>').trigger('change.select2');
            $screen.empty().append('<option value="">Select screen...</option>').trigger('change.select2');

            if (!deviceId) {
                $layout.empty().append('<option value="">Select layout...</option>').trigger('change');
                return;
            }

            $.ajax({
                url: '/device/' + deviceId + '/layouts',
                type: 'GET',
                data: { status: 1 },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    $layout.empty().append('<option value="">Select layout...</option>');
                    if (res && res.success && Array.isArray(res.layouts)) {
                        res.layouts.forEach(function (l) {
                            var selected = (l.id == selectedLayoutId) ? ' selected' : '';
                            $layout.append('<option value="' + l.id + '"' + selected + '>' + l.layout_name + '</option>');
                        });
                    }
                    $layout.trigger('change');

                    // Load screens if layout is selected
                    if (selectedLayoutId) {
                        loadScreensForEdit(deviceId, selectedLayoutId, selectedScreenId);
                    }
                },
                error: function () {
                    $layout.empty().append('<option value="">Failed to load layouts</option>');
                }
            });
        }

        // Helper function to load screens for edit form
        function loadScreensForEdit(deviceId, selectedLayoutId, selectedScreenId) {
            var $screen = $('#edit-screen_id');

            $screen.empty().append('<option value="">Loading...</option>').trigger('change.select2');

            var data = {};
            if (selectedLayoutId) {
                data.layout_id = selectedLayoutId;
            }

            $.ajax({
                url: '/device/' + deviceId + '/screens',
                type: 'GET',
                data: data,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    $screen.empty().append('<option value="">Select screen...</option>');
                    if (res && res.success && Array.isArray(res.screens)) {
                        res.screens.forEach(function (s) {
                            var label = 'Screen ' + s.screen_no;
                            if (s.layout && s.layout.layout_name) {
                                label += ' - ' + s.layout.layout_name;
                            }
                            var selected = (s.id == selectedScreenId) ? ' selected' : '';
                            $screen.append('<option value="' + s.id + '"' + selected + '>' + label + '</option>');
                        });
                    }
                    $screen.trigger('change');
                },
                error: function () {
                    $screen.empty().append('<option value="">Failed to load screens</option>');
                }
            });
        }

        // Dependent selects: device -> layouts (active), layout -> screens
        $(document).on('change', 'select[name="device_id"]', function () {
            var deviceId = $(this).val();
            var $layout = $('select[name="layout_id"]');
            var $screen = $('select[name="screen_id"]');

            // Clear and reset screen options
            $screen.empty().append('<option value="">Select screen...</option>');
            if ($screen.hasClass('select2-hidden-accessible')) {
                $screen.select2('destroy');
            }
            $screen.select2();

            // Clear and reset layout options
            $layout.empty().append('<option value="">Loading...</option>');
            if ($layout.hasClass('select2-hidden-accessible')) {
                $layout.select2('destroy');
            }
            $layout.select2();

            if (!deviceId) {
                $layout.empty().append('<option value="">Select layout...</option>');
                $layout.trigger('change');
                return;
            }

            $.ajax({
                url: '/device/' + deviceId + '/layouts', type: 'GET', data: { status: 1 },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    $layout.empty().append('<option value="">Select layout...</option>');
                    if (res && res.success && Array.isArray(res.layouts)) {
                        res.layouts.forEach(function (l) {
                            $layout.append('<option value="' + l.id + '">' + l.layout_name + '</option>');
                        });
                    }
                    // Refresh Select2 after populating options
                    $layout.trigger('change');
                },
                error: function () {
                    $layout.empty().append('<option value="">Failed to load layouts</option>');
                    $layout.trigger('change');
                }
            });
        });

        $(document).on('change', 'select[name="layout_id"]', function () {
            var layoutId = $(this).val();
            var deviceId = $('select[name="device_id"]').val();
            var $screen = $('select[name="screen_id"]');

            // Clear and reset screen options
            $screen.empty().append('<option value="">Loading...</option>');
            if ($screen.hasClass('select2-hidden-accessible')) {
                $screen.select2('destroy');
            }
            $screen.select2();

            if (!deviceId) {
                $screen.empty().append('<option value="">Select screen...</option>');
                $screen.trigger('change');
                return;
            }

            var data = {};
            if (layoutId) { data.layout_id = layoutId; }

            $.ajax({
                url: '/device/' + deviceId + '/screens', type: 'GET', data: data,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    $screen.empty().append('<option value="">Select screen...</option>');
                    if (res && res.success && Array.isArray(res.screens)) {
                        res.screens.forEach(function (s) {
                            var label = 'Screen ' + s.screen_no;
                            if (s.layout && s.layout.layout_name) { label += ' - ' + s.layout.layout_name; }
                            $screen.append('<option value="' + s.id + '">' + label + '</option>');
                        });
                    }
                    // Refresh Select2 after populating options
                    $screen.trigger('change');
                },
                error: function () {
                    $screen.empty().append('<option value="">Failed to load screens</option>');
                    $screen.trigger('change');
                }
            });
        });

        // Edit form dependent selects: device -> layouts (active), layout -> screens
        $(document).on('change', '#edit-device_id', function () {
            var deviceId = $(this).val();
            var $layout = $('#edit-layout_id');
            var $screen = $('#edit-screen_id');

            // Clear and reset screen options
            $screen.empty().append('<option value="">Select screen...</option>');
            if ($screen.hasClass('select2-hidden-accessible')) {
                $screen.select2('destroy');
            }
            $screen.select2();

            // Clear and reset layout options
            $layout.empty().append('<option value="">Loading...</option>');
            if ($layout.hasClass('select2-hidden-accessible')) {
                $layout.select2('destroy');
            }
            $layout.select2();

            if (!deviceId) {
                $layout.empty().append('<option value="">Select layout...</option>');
                $layout.trigger('change');
                return;
            }

            $.ajax({
                url: '/device/' + deviceId + '/layouts', type: 'GET', data: { status: 1 },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    $layout.empty().append('<option value="">Select layout...</option>');
                    if (res && res.success && Array.isArray(res.layouts)) {
                        res.layouts.forEach(function (l) {
                            $layout.append('<option value="' + l.id + '">' + l.layout_name + '</option>');
                        });
                    }
                    // Refresh Select2 after populating options
                    $layout.trigger('change');
                },
                error: function () {
                    $layout.empty().append('<option value="">Failed to load layouts</option>');
                    $layout.trigger('change');
                }
            });
        });

        $(document).on('change', '#edit-layout_id', function () {
            var layoutId = $(this).val();
            var deviceId = $('#edit-device_id').val();
            var $screen = $('#edit-screen_id');

            // Clear and reset screen options
            $screen.empty().append('<option value="">Loading...</option>');
            if ($screen.hasClass('select2-hidden-accessible')) {
                $screen.select2('destroy');
            }
            $screen.select2();

            if (!deviceId) {
                $screen.empty().append('<option value="">Select screen...</option>');
                $screen.trigger('change');
                return;
            }

            var data = {};
            if (layoutId) { data.layout_id = layoutId; }

            $.ajax({
                url: '/device/' + deviceId + '/screens', type: 'GET', data: data,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    $screen.empty().append('<option value="">Select screen...</option>');
                    if (res && res.success && Array.isArray(res.screens)) {
                        res.screens.forEach(function (s) {
                            var label = 'Screen ' + s.screen_no;
                            if (s.layout && s.layout.layout_name) { label += ' - ' + s.layout.layout_name; }
                            $screen.append('<option value="' + s.id + '">' + label + '</option>');
                        });
                    }
                    // Refresh Select2 after populating options
                    $screen.trigger('change');
                },
                error: function () {
                    $screen.empty().append('<option value="">Failed to load screens</option>');
                    $screen.trigger('change');
                }
            });
        });

        // Handle edit schedule form submission
        $('#edit-schedule-form').on('submit', function (e) {
            e.preventDefault();
            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            submitBtn.html('Updating...').prop('disabled', true);

            // Use FormData for file uploads
            var formData = new FormData(this);

            // Debug: Log form data
            console.log('Edit form data:');
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.success) {
                        var $wrap = $('#edit-form-alert');
                        var $alert = $wrap.find('.alert');
                        if ($alert.length === 0) {
                            $wrap.html('<div class="alert alert-success alert-dismissible fade show" role="alert"></div>');
                            $alert = $wrap.find('.alert');
                        }
                        $alert.removeClass('alert-danger').addClass('alert-success');
                        $alert.html('Schedule updated successfully! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                        $wrap.show();
                        scheduleTable.ajax.reload();
                        setTimeout(function () { $('#offcanvas_edit').offcanvas('hide'); }, 1200);
                    } else {
                        alert(response.message || 'Failed to update schedule');
                    }
                },
                error: function (xhr) {
                    console.error('Schedule update error:', xhr);
                    const res = xhr.responseJSON;
                    let msg = 'Error updating schedule.';
                    if (res && res.errors) {
                        msg = Object.values(res.errors)[0][0];
                    } else if (res && res.message) {
                        msg = res.message;
                    }
                    alert(msg);
                },
                complete: function () { submitBtn.html(originalBtnText).prop('disabled', false); }
            });
        });

        // Handle add media functionality for create form
        $(document).on('click', '#add-media', function () {
            const mediaHtml = `
                <div class="media-item border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Media Title</label>
                                <input type="text" class="form-control" name="media_title[]"
                                    placeholder="Enter media title">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Media Type</label>
                                <select class="form-control select2" name="media_type[]"
                                    data-toggle="select2">
                                    <option value="">Select type...</option>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                    <option value="audio">Audio</option>
                                    <option value="mp4">MP4</option>
                                    <option value="png">PNG</option>
                                    <option value="jpg">JPG</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm w-100 remove-media">
                                    <i class="ti ti-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Media File</label>
                                <input type="file" class="form-control" name="media_file[]"
                                    accept="image/*,video/*,audio/*,.mp4,.png,.jpg,.pdf">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#media-container').append(mediaHtml);
            initializeSelect2();
        });

        // Handle add media functionality for edit form
        $(document).on('click', '#edit-add-media', function () {
            const mediaHtml = `
                <div class="media-item border rounded p-3 mb-3">
                    <input type="hidden" name="edit_media_id[]" value="">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Media Title</label>
                                <input type="text" class="form-control" name="edit_media_title[]"
                                    placeholder="Enter media title">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Media Type</label>
                                <select class="form-control select2" name="edit_media_type[]"
                                    data-toggle="select2">
                                    <option value="">Select type...</option>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                    <option value="audio">Audio</option>
                                    <option value="mp4">MP4</option>
                                    <option value="png">PNG</option>
                                    <option value="jpg">JPG</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm w-100 remove-media">
                                    <i class="ti ti-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Media File</label>
                                <input type="file" class="form-control" name="edit_media_file[]"
                                    accept="image/*,video/*,audio/*,.mp4,.png,.jpg,.pdf">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#edit-media-container').append(mediaHtml);
            initializeSelect2();
        });

        // Handle remove media functionality
        $(document).on('click', '.remove-media', function () {
            $(this).closest('.media-item').remove();
        });

        // Reset forms once the offcanvas fully closes
        $(document).on('hidden.bs.offcanvas', '#offcanvas_add', function () {
            var $form = $('#create-schedule-form');
            if ($form.length) {
                try {
                    $form[0].reset();
                    // Hide alerts
                    $('#create-form-alert').hide();
                } catch (err) {
                    console.error('Error resetting create form after close:', err);
                }
            }
        });

        $(document).on('hidden.bs.offcanvas', '#offcanvas_edit', function () {
            var $form = $('#edit-schedule-form');
            if ($form.length) {
                try {
                    $form[0].reset();
                    $('#edit-form-alert').hide();
                } catch (err) {
                    console.error('Error resetting edit form after close:', err);
                }
            }
        });
    }
});


