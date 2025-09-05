$(document).ready(function () {
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
    $(document).on('shown.bs.offcanvas', function () { setTimeout(initializeSelect2, 100); });

    // Edit schedule form submission
    $('#edit-schedule-form').on('submit', function (e) {
        e.preventDefault();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = submitBtn.html();
        submitBtn.html('Updating...').prop('disabled', true);

        // Use FormData for file uploads
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        const actionUrl = $(this).attr('action');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.success) {
                    var $wrap = $('#edit-form-alert');
                    var $alert = $wrap.find('.alert');
                    if ($alert.length === 0) { $wrap.html('<div class="alert alert-success alert-dismissible fade show" role="alert"></div>'); $alert = $wrap.find('.alert'); }
                    $alert.removeClass('alert-danger').addClass('alert-success');
                    $alert.html('Schedule updated successfully! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                    $wrap.show();

                    // Optionally update key fields on page
                    if (response.schedule) {
                        const s = response.schedule;
                        $('h5.mb-1').first().text(s.schedule_name || '');
                        $('.badge-soft-info').first().html('<i class="ti ti-clock me-1"></i>Start: ' + (s.schedule_start_date_time ? new Date(s.schedule_start_date_time).toLocaleString() : ''));
                        $('.badge-soft-warning').first().html('<i class="ti ti-clock me-1"></i>End: ' + (s.schedule_end_date_time ? new Date(s.schedule_end_date_time).toLocaleString() : ''));
                    }

                    setTimeout(function () { $('#offcanvas_edit').offcanvas('hide'); }, 1200);
                } else {
                    showError(response.message || 'Failed to update schedule');
                }
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                let msg = 'Failed to update schedule.';
                if (res && res.errors) { msg = Object.values(res.errors)[0][0]; }
                showError(msg);
            },
            complete: function () {
                submitBtn.html(originalBtnText).prop('disabled', false);
            }
        });
    });

    function showError(message) {
        var $wrap = $('#edit-form-alert');
        var $alert = $wrap.find('.alert');
        if ($alert.length === 0) { $wrap.html('<div class="alert alert-danger alert-dismissible fade show" role="alert"></div>'); $alert = $wrap.find('.alert'); }
        $alert.removeClass('alert-success').addClass('alert-danger');
        $alert.html(message + ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
        $wrap.show();
    }

    // Handle add edit media functionality
    $(document).on('click', '#add-edit-media', function () {
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
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-edit-media">
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
                                accept="image/*,video/*,audio/*">
                            <input type="hidden" name="edit_media_id[]" value="">
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#edit-media-container').append(mediaHtml);
        initializeSelect2();
    });

    // Handle remove edit media functionality
    $(document).on('click', '.remove-edit-media', function () {
        const mediaId = $(this).data('media-id');
        if (mediaId) {
            // If it's an existing media item, add a hidden input to mark it for deletion
            const deleteInput = `<input type="hidden" name="delete_media_ids[]" value="${mediaId}">`;
            $('#edit-schedule-form').append(deleteInput);
        }
        $(this).closest('.media-item').remove();
    });
});


