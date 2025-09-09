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
        const urlWithMethod = actionUrl.indexOf('?') === -1
            ? actionUrl + '?_method=PUT'
            : actionUrl + '&_method=PUT';

        var startTime = Date.now();
        $.ajax({
            url: urlWithMethod,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (evt) {
                    if (evt.lengthComputable) {
                        var loaded = evt.loaded;
                        var total = evt.total;
                        var percent = (loaded / total) * 100;
                        var $overall = $('#show-edit-overall-upload');
                        if ($overall.length) {
                            var elapsedSec = Math.max(0.001, (Date.now() - startTime) / 1000);
                            var speed = loaded / elapsedSec;
                            var remaining = Math.max(0, total - loaded);
                            var etaSec = speed > 0 ? remaining / speed : 0;
                            $overall.show();
                            $overall.find('.progress-bar').css('width', percent.toFixed(0) + '%').attr('aria-valuenow', percent.toFixed(0));
                            $overall.find('.overall-progress-text').text(
                                formatBytes(loaded) + ' / ' + formatBytes(total) + ' • ' + formatBytes(speed) + '/s • ETA ' + formatDuration(etaSec)
                            );
                        }
                    }
                }, false);
                return xhr;
            },
            success: function (response) {
                if (response.success) {
                    // Show success message
                    var $wrap = $('#edit-form-alert');
                    var $alert = $wrap.find('.alert');
                    if ($alert.length === 0) { $wrap.html('<div class="alert alert-success alert-dismissible fade show" role="alert"></div>'); $alert = $wrap.find('.alert'); }
                    $alert.removeClass('alert-danger').addClass('alert-success');
                    $alert.html('Schedule updated successfully! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                    $wrap.show();

                    // Show success message at top of page
                    showPageSuccessMessage('Schedule updated successfully!');

                    // Update page content with new data
                    if (response.schedule) {
                        updateScheduleContent(response.schedule);
                    }

                    // Close the offcanvas
                    setTimeout(function () {
                        $('#offcanvas_edit').offcanvas('hide');
                    }, 1200);
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
                var $overall = $('#show-edit-overall-upload');
                if ($overall.length) { $overall.hide(); $overall.find('.progress-bar').css('width', '0%').attr('aria-valuenow', 0); $overall.find('.overall-progress-text').text('0%'); }
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

    function showPageSuccessMessage(message) {
        // Check if there's already a success alert at the top
        var existingAlert = $('.container-fluid .alert-success');
        if (existingAlert.length > 0) {
            existingAlert.remove();
        }

        // Add success message at the top of the page
        var successHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        $('.container-fluid').prepend(successHtml);

        // Auto-hide after 5 seconds
        setTimeout(function () {
            $('.container-fluid .alert-success').fadeOut();
        }, 5000);
    }


    function updateScheduleContent(schedule) {
        // Update main header section
        $('h5.mb-1').first().text(schedule.schedule_name || '');
        $('p.mb-2').first().text((schedule.device ? schedule.device.name : '') || (schedule.device ? schedule.device.unique_id : '') || 'N/A');

        // Update date badges using pre-formatted dates from server
        if (schedule.formatted_start_date) {
            $('.badge-soft-info').first().html('<i class="ti ti-clock me-1"></i>Start: ' + schedule.formatted_start_date);
        }

        if (schedule.formatted_end_date) {
            $('.badge-soft-warning').first().html('<i class="ti ti-clock me-1"></i>End: ' + schedule.formatted_end_date);
        }

        // Update Schedule Overview section
        $('.card-body .row .col-md-6:first .mb-4:first p').text(schedule.schedule_name || '');

        if (schedule.formatted_start_date) {
            $('.card-body .row .col-md-6:first .mb-4:nth-child(2) p').text(schedule.formatted_start_date);
        }

        if (schedule.formatted_end_date) {
            $('.card-body .row .col-md-6:first .mb-4:nth-child(3) p').text(schedule.formatted_end_date);
        }

        // Update Target section
        $('.card-body .row .col-md-6:last .mb-4:first p').text((schedule.device ? schedule.device.name : '') || 'N/A');
        $('.card-body .row .col-md-6:last .mb-4:nth-child(2) p').text((schedule.layout ? schedule.layout.layout_name : '') || 'N/A');
        $('.card-body .row .col-md-6:last .mb-4:nth-child(3) p').text((schedule.screen ? 'Screen ' + schedule.screen.screen_no : '') || 'N/A');

        // Update media section
        updateMediaSection(schedule.medias);
    }

    function updateMediaSection(medias) {
        const mediaContainer = $('.card-body .table-responsive tbody');
        const noMediaContainer = $('.text-center.py-4');

        if (medias && medias.length > 0) {
            // Hide no media message
            noMediaContainer.hide();

            // Clear existing media rows
            mediaContainer.empty();

            // Add new media rows
            medias.forEach(function (media) {
                const createdDate = media.formatted_created_date || 'N/A';

                const mediaRow = `
                    <tr>
                        <td>${media.title || ''}</td>
                        <td>${media.media_type ? media.media_type.charAt(0).toUpperCase() + media.media_type.slice(1) : ''}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary preview-media-btn"
                                    data-media-file="${media.media_file || ''}"
                                    data-media-type="${media.media_type || ''}"
                                    data-media-title="${media.title || ''}">
                                <i class="ti ti-eye"></i> Preview
                            </button>
                        </td>
                        <td>${createdDate}</td>
                    </tr>
                `;
                mediaContainer.append(mediaRow);
            });

            // Show table
            $('.table-responsive').show();
        } else {
            // Show no media message
            $('.table-responsive').hide();
            noMediaContainer.show();
        }
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

    // Media preview functionality
    $(document).on('click', '.preview-media-btn', function () {
        const mediaFile = $(this).data('media-file');
        const mediaType = $(this).data('media-type');
        const mediaTitle = $(this).data('media-title');

        // Update modal title
        $('#mediaPreviewModalLabel').text('Preview: ' + mediaTitle);

        // Clear previous content
        $('#mediaPreviewContent').empty();

        // Generate media URL
        const mediaUrl = window.location.origin + '/storage/' + mediaFile;

        let mediaHtml = '';

        // Create appropriate media element based on type
        if (mediaType === 'image' || mediaType === 'png' || mediaType === 'jpg' || mediaType === 'jpeg') {
            mediaHtml = `
                <div class="media-preview-container">
                    <img src="${mediaUrl}" alt="${mediaTitle}" class="img-fluid rounded">
                </div>
            `;
        } else if (mediaType === 'video' || mediaType === 'mp4') {
            mediaHtml = `
                <div class="media-preview-container">
                    <video controls class="w-100">
                        <source src="${mediaUrl}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            `;
        } else if (mediaType === 'audio') {
            mediaHtml = `
                <div class="media-preview-container">
                    <audio controls class="w-100">
                        <source src="${mediaUrl}" type="audio/mpeg">
                        Your browser does not support the audio tag.
                    </audio>
                </div>
            `;
        } else if (mediaType === 'pdf') {
            mediaHtml = `
                <div class="media-preview-container">
                    <iframe src="${mediaUrl}" type="application/pdf">
                        <p>Your browser does not support PDFs. <a href="${mediaUrl}" target="_blank">Click here to download the PDF</a></p>
                    </iframe>
                </div>
            `;
        } else {
            mediaHtml = `
                <div class="media-preview-placeholder">
                    <i class="ti ti-file" style="font-size: 3rem;"></i>
                    <h6 class="mt-2">Preview not available</h6>
                    <p>This file type cannot be previewed.</p>
                    <a href="${mediaUrl}" target="_blank" class="btn btn-primary">
                        <i class="ti ti-download"></i> Download File
                    </a>
                </div>
            `;
        }

        $('#mediaPreviewContent').html(mediaHtml);

        // Show modal
        $('#mediaPreviewModal').modal('show');
    });
});

// Helpers (duplicated minimal to keep file standalone on details page)
function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    var k = 1024;
    var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    var val = bytes / Math.pow(k, i);
    return val.toFixed(val >= 100 ? 0 : val >= 10 ? 1 : 2) + ' ' + sizes[i];
}

function formatDuration(seconds) {
    seconds = Math.max(0, Math.round(seconds));
    var h = Math.floor(seconds / 3600);
    var m = Math.floor((seconds % 3600) / 60);
    var s = seconds % 60;
    var parts = [];
    if (h) parts.push(h + 'h');
    if (m || h) parts.push(m + 'm');
    parts.push(s + 's');
    return parts.join(' ');
}


