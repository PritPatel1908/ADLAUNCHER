$(document).ready(function () {
    // Set up AJAX to always send the CSRF token and proper headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        // Ensure proper error handling
        error: function (xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                response: xhr.responseText,
                url: xhr.responseURL
            });
        }
    });

    // Set up global AJAX error handler for better debugging
    $(document).ajaxError(function (event, xhr, settings, thrownError) {
        console.error('Global AJAX Error:', {
            url: settings.url,
            method: settings.type,
            status: xhr.status,
            error: thrownError,
            response: xhr.responseText
        });

        // Show user-friendly error message for common issues
        if (xhr.status === 404) {
            console.error('Route not found. Check if the route is properly defined.');
        } else if (xhr.status === 419) {
            console.error('CSRF token mismatch. Page may need to be refreshed.');
        } else if (xhr.status === 500) {
            console.error('Server error. Check server logs for details.');
        }
    });
});