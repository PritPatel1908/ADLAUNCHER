$(document).ready(function () {
    let columnVisibility = {};
    window.currentSortBy = 'newest';

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

    function applyInitialColumnVisibility() {
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
        const savedVisibility = localStorage.getItem('scheduleColumnVisibility');
        if (savedVisibility) {
            try {
                const parsed = JSON.parse(savedVisibility);
                if (parsed && typeof parsed === 'object') {
                    for (var column in parsed) {
                        if (column in columnVisibility) {
                            columnVisibility[column] = parsed[column];
                        }
                    }
                }
            } catch (e) { }
        }
    }

    function saveColumnVisibility(column, isVisible) {
        columnVisibility[column] = isVisible;
        localStorage.setItem('scheduleColumnVisibility', JSON.stringify(columnVisibility));
        // Optional: persist via ShowColumnController if needed for 'schedules' table
        $.ajax({
            url: '/columns',
            type: 'POST',
            data: { table: 'schedules', column_name: column, is_show: isVisible ? 1 : 0 },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
    }

    function loadColumnVisibility() {
        return $.ajax({
            url: '/columns',
            type: 'GET',
            data: { table: 'schedules' },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                applyInitialColumnVisibility();
                if (response.success && Array.isArray(response.columns)) {
                    response.columns.forEach(function (col) {
                        let isVisible = col.is_show;
                        if (typeof isVisible !== 'boolean') {
                            isVisible = isVisible === 1 || isVisible === '1' || isVisible === true || isVisible === 'true';
                        }
                        if (col.column_name in columnVisibility) {
                            columnVisibility[col.column_name] = isVisible;
                        }
                    });
                    localStorage.setItem('scheduleColumnVisibility', JSON.stringify(columnVisibility));
                }
                updateColumnToggles();
            },
            error: function () {
                applyInitialColumnVisibility();
                updateColumnToggles();
            }
        });
    }

    function updateColumnToggles() {
        $('.column-visibility-toggle').each(function () {
            const column = $(this).data('column');
            if (column in columnVisibility) {
                $(this).prop('checked', columnVisibility[column]);
            }
        });
    }

    applyInitialColumnVisibility();
    var loadReq = loadColumnVisibility();
    if (loadReq && typeof loadReq.always === 'function') {
        loadReq.always(function () {
            if ($('#scheduleslist').length > 0) {
                initializeDataTable();
            }
        });
    } else {
        if ($('#scheduleslist').length > 0) {
            initializeDataTable();
        }
    }

    function initializeDataTable() {
        $('#error-container').hide();
        $('.data-loading').show();

        // Check if moment is available
        if (typeof moment === 'undefined') {
            $('.data-loading').hide();
            $('#error-container').show();
            $('#error-message').text('Moment.js library is not loaded. Please refresh the page.');
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#scheduleslist')) {
            $('#scheduleslist').DataTable().destroy();
        }

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
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function (d) {
                    d.name_filter = $('.schedule-filter[data-column="schedule_name"]').val();
                    d.device_filter = $('.schedule-filter[data-column="device"]').val();
                    var dateRange = $('#reportrange span').text().split(' - ');
                    if (dateRange.length === 2) {
                        d.start_date = moment(dateRange[0], 'D MMM YY').format('YYYY-MM-DD');
                        d.end_date = moment(dateRange[1], 'D MMM YY').format('YYYY-MM-DD');
                    }
                    d.sort_by = window.currentSortBy || 'newest';
                    return d;
                },
                success: function (data) {
                    $('.data-loading').hide();
                },
                error: function (xhr) {
                    $('.data-loading').hide();
                    $('#error-container').show();
                    let msg = 'Failed to load schedules. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    else if (xhr.status === 0) msg = 'Network error. Please check your connection.';
                    else if (xhr.status === 404) msg = 'Endpoint not found. Please contact administrator.';
                    else if (xhr.status === 500) msg = 'Server error. Please try again later.';
                    $('#error-message').text(msg);
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
                        return `
                        <div class="dropdown dropdown-action">
                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="/schedule/${data}"><i class="ti ti-eye me-2"></i>View</a>
                                <a class="dropdown-item edit-schedule" href="javascript:void(0);" data-id="${data}"><i class="ti ti-edit text-blue"></i> Edit</a>
                                <a class="dropdown-item delete-schedule" href="javascript:void(0);" data-id="${data}"><i class="ti ti-trash me-2"></i>Delete</a>
                            </div>
                        </div>`;
                    }
                }
            ],
            language: {
                search: ' ', sLengthMenu: '_MENU_', searchPlaceholder: 'Search', info: '_START_ - _END_ of _TOTAL_ items', lengthMenu: 'Show _MENU_ entries',
                paginate: { next: '<i class="ti ti-chevron-right"></i> ', previous: '<i class="ti ti-chevron-left"></i> ' },
            },
            initComplete: function () {
                $('.data-loading').hide();
                $('.dataTables_paginate').appendTo('.datatable-paginate');
                $('.dataTables_length').appendTo('.datatable-length');
                $('#error-container').hide();
                setTimeout(function () {
                    Object.keys(columnVisibility).forEach(function (column) {
                        const isVisible = columnVisibility[column];
                        let columnIndex = null;
                        scheduleTable.columns().every(function (index) {
                            const colName = this.settings()[0].aoColumns[index].name;
                            if (colName === column) { columnIndex = index; return false; }
                        });
                        if (columnIndex !== null) scheduleTable.column(columnIndex).visible(isVisible, false);
                    });
                    scheduleTable.columns.adjust().draw(false);
                }, 200);
            }
        });

        $(document).on('change', '.column-visibility-toggle', function (e) {
            e.stopPropagation();
            const column = $(this).data('column');
            const isVisible = $(this).prop('checked');
            let columnIndex = null;
            scheduleTable.columns().every(function (index) {
                const colName = this.settings()[0].aoColumns[index].name;
                if (colName === column) { columnIndex = index; return false; }
            });
            if (columnIndex !== null) {
                scheduleTable.column(columnIndex).visible(isVisible, false);
                scheduleTable.columns.adjust().draw(false);
                saveColumnVisibility(column, isVisible);
            }
        });

        $('.schedule-filter').on('keyup change', function () {
            $('#error-container').hide();
            scheduleTable.ajax.reload();
        });

        $('#reportrange').on('apply.daterangepicker', function () {
            $('#error-container').hide();
            scheduleTable.ajax.reload();
        });

        $(document).on('click', '.sort-option', function () {
            const sortBy = $(this).data('sort');
            const sortText = $(this).text();
            window.currentSortBy = sortBy;
            $('.dropdown-toggle.btn-outline-light').first().html(`<i class="ti ti-sort-ascending-2 me-2"></i>${sortText}`);
            $('#error-container').hide();
            scheduleTable.ajax.reload();
        });

        $(document).on('click', '#retry-load', function () {
            $('#error-container').hide();
            scheduleTable.ajax.reload();
        });

        // Handle create schedule
        $('#create-schedule-form').on('submit', function (e) {
            e.preventDefault();
            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            submitBtn.html('Creating...').prop('disabled', true);

            // Use FormData for file uploads
            var formData = new FormData(this);

            $.ajax({
                url: '/schedule',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
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
                        alert(response.message || 'Failed to create schedule');
                    }
                },
                error: function (xhr) {
                    const res = xhr.responseJSON;
                    let msg = 'Error creating schedule.';
                    if (res && res.errors) { msg = Object.values(res.errors)[0][0]; }
                    alert(msg);
                },
                complete: function () { submitBtn.html(originalBtnText).prop('disabled', false); }
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

                        // Format datetime values for datetime-local input
                        if (schedule.schedule_start_date_time) {
                            const startDate = new Date(schedule.schedule_start_date_time);
                            const startFormatted = startDate.toISOString().slice(0, 16);
                            $('#edit-schedule_start_date_time').val(startFormatted);
                        } else {
                            $('#edit-schedule_start_date_time').val('');
                        }

                        if (schedule.schedule_end_date_time) {
                            const endDate = new Date(schedule.schedule_end_date_time);
                            const endFormatted = endDate.toISOString().slice(0, 16);
                            $('#edit-schedule_end_date_time').val(endFormatted);
                        } else {
                            $('#edit-schedule_end_date_time').val('');
                        }

                        // Set device
                        if (schedule.device_id) {
                            $('#edit-device_id').val(schedule.device_id).trigger('change');
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
                        if (response.success) { scheduleTable.ajax.reload(null, false); alert('Schedule deleted successfully!'); }
                        else { alert(response.message || 'Failed to delete schedule.'); }
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
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Media Title</label>
                                        <input type="text" class="form-control" name="edit_media_title[]"
                                            value="${media.title || ''}" placeholder="Enter media title">
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Duration (seconds)</label>
                                        <input type="number" class="form-control" name="edit_duration_seconds[]"
                                            value="${media.duration_seconds || ''}" placeholder="Duration in seconds" min="1">
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Media Title</label>
                                    <input type="text" class="form-control" name="edit_media_title[]"
                                        placeholder="Enter media title">
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Duration (seconds)</label>
                                    <input type="number" class="form-control" name="edit_duration_seconds[]"
                                        placeholder="Duration in seconds" min="1">
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
            $layout.empty().append('<option value="">Loading...</option>').trigger('change.select2');
            $screen.empty().append('<option value="">Select screen...</option>').trigger('change.select2');
            if (!deviceId) {
                $layout.empty().append('<option value="">Select layout...</option>').trigger('change');
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
                    $layout.trigger('change');
                },
                error: function () {
                    $layout.empty().append('<option value="">Failed to load layouts</option>');
                }
            });
        });

        $(document).on('change', 'select[name="layout_id"]', function () {
            var layoutId = $(this).val();
            var deviceId = $('select[name="device_id"]').val();
            var $screen = $('select[name="screen_id"]');
            $screen.empty().append('<option value="">Loading...</option>').trigger('change.select2');
            if (!deviceId) {
                $screen.empty().append('<option value="">Select screen...</option>').trigger('change');
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
                    $screen.trigger('change');
                },
                error: function () {
                    $screen.empty().append('<option value="">Failed to load screens</option>');
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Media Title</label>
                                <input type="text" class="form-control" name="media_title[]"
                                    placeholder="Enter media title">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Duration (seconds)</label>
                                <input type="number" class="form-control" name="duration_seconds[]"
                                    placeholder="Duration in seconds" min="1">
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Media Title</label>
                                <input type="text" class="form-control" name="edit_media_title[]"
                                    placeholder="Enter media title">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Duration (seconds)</label>
                                <input type="number" class="form-control" name="edit_duration_seconds[]"
                                    placeholder="Duration in seconds" min="1">
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
    }
});


