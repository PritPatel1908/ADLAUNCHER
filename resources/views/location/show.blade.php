@extends('layout.main')

@section('meta')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('css')
@endpush

@section('content')
<!-- Start Content -->
<div class="content pb-0">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
        <div>
            <h4 class="mb-1">Location Details</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('location.index') }}">Locations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Location Details</li>
                </ol>
            </nav>
        </div>
        <div class="gap-2 d-flex align-items-center flex-wrap">
            <div class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-primary px-2 shadow"
                    data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
                <div class="dropdown-menu dropdown-menu-end">
                    <ul>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="ti ti-file-type-pdf me-1"></i>Export as PDF</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="ti ti-file-type-xls me-1"></i>Export as Excel</a>
                        </li>
                    </ul>
                </div>
            </div>
            <a href="javascript:void(0);" class="btn btn-icon btn-outline-info shadow" data-bs-toggle="tooltip"
                data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh"><i
                    class="ti ti-refresh"></i></a>
            <a href="javascript:void(0);" class="btn btn-icon btn-outline-warning shadow" data-bs-toggle="tooltip"
                data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i
                    class="ti ti-transition-top"></i></a>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="row">
        <div class="col-md-12">

            <div class="mb-3">
                <a href="{{ route('location.index') }}"><i class="ti ti-arrow-narrow-left me-1"></i>Back to Locations</a>
            </div>

            <div class="card">
                <div class="card-body pb-2">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-xxl avatar-rounded me-3 flex-shrink-0 bg-primary">
                                <span class="avatar-text">{{ substr($location->name, 0, 1) }}</span>
                                <span class="status {{ $location->status ? 'online' : 'offline' }}"></span>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $location->name }}</h5>
                                <p class="mb-2">{{ $location->address }}</p>
                                <div class="d-flex align-items-center">
                                    <span class="badge {{ $location->status ? 'badge-soft-success' : 'badge-soft-danger' }} border-0 me-2">
                                        <i class="ti {{ $location->status ? 'ti-check' : 'ti-lock' }} me-1"></i>
                                        {{ $location->status ? 'Active' : 'Inactive' }}
                                    </span>
                                    <p class="d-inline-flex align-items-center mb-0">
                                        <i class="ti ti-map-pin text-warning me-1"></i> {{ $location->city }}, {{ $location->country }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                <i class="ti ti-edit me-1"></i>Edit Location
                            </a>
                            <form action="{{ route('location.destroy', $location->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this location?')">
                                    <i class="ti ti-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Location User -->

        </div>

        <!-- Location Sidebar -->
        <div class="col-xl-3">
            <div class="card">
                <div class="card-body p-3">
                    <h6 class="mb-3 fw-semibold">Location Information</h6>
                    <div class="border-bottom mb-3 pb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="avatar avatar-xs bg-primary p-0 flex-shrink-0 rounded-circle text-white me-2">
                                <i class="ti ti-mail fs-14"></i>
                            </span>
                            <p class="mb-0">
                                <a href="mailto:{{ $location->email }}">{{ $location->email }}</a>
                            </p>
                        </div>

                        <div class="d-flex align-items-center mb-2">
                            <span class="avatar avatar-xs bg-warning p-0 flex-shrink-0 rounded-circle text-white me-2">
                                <i class="ti ti-map-pin fs-14"></i>
                            </span>
                            <p class="mb-0">{{ $location->address }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="avatar avatar-xs bg-success p-0 flex-shrink-0 rounded-circle text-white me-2">
                                <i class="ti ti-building fs-14"></i>
                            </span>
                            <p class="mb-0">{{ $location->city }}, {{ $location->state }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="avatar avatar-xs bg-danger p-0 flex-shrink-0 rounded-circle text-white me-2">
                                <i class="ti ti-world fs-14"></i>
                            </span>
                            <p class="mb-0">{{ $location->country }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="avatar avatar-xs bg-secondary p-0 flex-shrink-0 rounded-circle text-white me-2">
                                <i class="ti ti-mailbox fs-14"></i>
                            </span>
                            <p class="mb-0">{{ $location->zip_code }}</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-xs bg-dark p-0 flex-shrink-0 rounded-circle text-white me-2">
                                <i class="ti ti-calendar-exclamation fs-14"></i>
                            </span>
                            <p class="mb-0">Created: {{ $location->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    <h6 class="mb-3 fw-semibold">Status Information</h6>
                    <ul class="border-bottom mb-3 pb-3">
                        <li class="row mb-2">
                            <span class="col-6">Status</span>
                            <span class="col-6 text-dark">
                                <span class="badge {{ $location->status ? 'badge-soft-success' : 'badge-soft-danger' }} border-0">
                                    {{ $location->status ? 'Active' : 'Inactive' }}
                                </span>
                            </span>
                        </li>
                        @if($location->updated_at)
                        <li class="row mb-2">
                            <span class="col-6">Last Modified</span>
                            <span class="col-6 text-dark">{{ $location->updated_at->format('d M Y, h:i A') }}</span>
                        </li>
                        @endif
                        @if($location->created_by)
                        <li class="row mb-2">
                            <span class="col-6">Created By</span>
                            <span class="col-6 text-dark">{{ $location->createdByUser->name ?? 'N/A' }}</span>
                        </li>
                        @endif
                        @if($location->updated_by)
                        <li class="row mb-2">
                            <span class="col-6">Last Updated By</span>
                            <span class="col-6 text-dark">{{ $location->updatedByUser->name ?? 'N/A' }}</span>
                        </li>
                        @endif
                        <li class="row mb-2">
                            <span class="col-6">Last Update</span>
                            <span class="col-6 text-dark">{{ $location->updated_at ? $location->updated_at->diffForHumans() : 'N/A' }}</span>
                        </li>
                    </ul>
                    <h6 class="mb-3 fw-semibold">Actions</h6>
                    <div class="mb-0">
                        <a href="javascript:void(0);" class="d-block mb-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                            <span class="avatar avatar-xs bg-primary p-0 flex-shrink-0 rounded-circle text-white me-2">
                                <i class="ti ti-edit"></i>
                            </span>Edit Location
                        </a>
                        <form action="{{ route('location.destroy', $location->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <a href="javascript:void(0);" class="d-block mb-0" onclick="if(confirm('Are you sure you want to delete this location?')) { this.closest('form').submit(); }">
                                <span class="avatar avatar-xs bg-danger p-0 flex-shrink-0 rounded-circle text-white me-2">
                                    <i class="ti ti-trash-x"></i>
                                </span>Delete Location
                            </a>
                        </form>
                        <a href="{{ route('location.index') }}" class="d-block mt-2">
                            <span class="avatar avatar-xs bg-info p-0 flex-shrink-0 rounded-circle text-white me-2">
                                <i class="ti ti-list"></i>
                            </span>View All Locations
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Location Sidebar -->

        <!-- Location Content -->
        <div class="col-xl-9">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Location Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Location Name</h6>
                                        <p>{{ $location->name }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Email Address</h6>
                                        <p><a href="mailto:{{ $location->email }}">{{ $location->email }}</a></p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Address</h6>
                                        <p>{{ $location->address }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">City</h6>
                                        <p>{{ $location->city }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">State</h6>
                                        <p>{{ $location->state }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Country</h6>
                                        <p>{{ $location->country }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Zip Code</h6>
                                        <p>{{ $location->zip_code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Location Content -->
    </div>
</div>
<!-- End Content -->

<!-- Edit Location Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas_edit" aria-labelledby="offcanvas_edit_label">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="offcanvas_edit_label">Edit Location</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="row">
            <div class="col-12">
                <form id="edit-location-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="accordion accordion-bordered" id="main_accordion2">
                        <!-- Basic Info -->
                        <div class="accordion-item rounded mb-3">
                            <div class="accordion-header">
                                <a href="#" class="accordion-button accordion-custom-button rounded" data-bs-toggle="collapse"
                                    data-bs-target="#basic2">
                                    <span class="avatar avatar-md rounded me-1"><i class="ti ti-user-plus"></i></span>
                                    Basic Info
                                </a>
                            </div>
                            <div class="accordion-collapse collapse show" id="basic2" data-bs-parent="#main_accordion2">
                                <div class="accordion-body border-top">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Location Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name" id="edit-name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" id="edit-email">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <input type="text" class="form-control" name="address" id="edit-address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" name="city" id="edit-city">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">State</label>
                                                <input type="text" class="form-control" name="state" id="edit-state">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Country</label>
                                                <input type="text" class="form-control" name="country" id="edit-country">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Zip Code</label>
                                                <input type="text" class="form-control" name="zip_code" id="edit-zip_code">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status" id="edit-status">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="edit-form-alert" class="col-12" style="display: none;">
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                Location updated successfully!
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <button type="button" data-bs-dismiss="offcanvas"
                                            class="btn btn-sm btn-light me-2">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Update Location</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Edit Location Offcanvas -->
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Handle edit button click
    $(document).on('click', '[data-bs-target="#offcanvas_edit"]', function() {
        var locationId = {{ $location->id }};

        // Set the location ID to the form
        $('#edit-location-form').data('location-id', locationId);
        $('#edit-location-form').attr('action', '/location/' + locationId);

        // Fetch location data via AJAX
        $.ajax({
            url: '/location/' + locationId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Populate form fields
                $('#edit-name').val(data.name);
                $('#edit-email').val(data.email);
                $('#edit-address').val(data.address);
                $('#edit-city').val(data.city);
                $('#edit-state').val(data.state);
                $('#edit-country').val(data.country);
                $('#edit-zip_code').val(data.zip_code);
                $('#edit-status').val(data.status);
            },
            error: function(xhr) {
                console.error('Error fetching location data');
                alert('Error loading location data. Please try again.');
            }
        });
    });

    // Function to update location data on the page without reloading
    function updateLocationDataOnPage(location) {
        // Update location name and first letter avatar in the header section
        $('.avatar-xxl.avatar-rounded .avatar-text').text(location.name.substr(0, 1));
        $('.avatar-xxl.avatar-rounded').next().find('h5.mb-1').text(location.name);

        // Update address in the header section
        $('.avatar-xxl.avatar-rounded').next().find('p.mb-2').text(location.address);

        // Update status badge and indicators
        // Convert status to boolean to handle both string ('0', '1') and number (0, 1) values
        var isActive = location.status == 1 || location.status === true || location.status === '1';
        var statusClass = isActive ? 'badge-soft-success' : 'badge-soft-danger';
        var statusIcon = isActive ? 'ti-check' : 'ti-lock';
        var statusText = isActive ? 'Active' : 'Inactive';

        // Update status indicator dot in the avatar
        $('.avatar-xxl.avatar-rounded .status').removeClass('online offline').addClass(isActive ? 'online' : 'offline');

        // Update all status badges in Location Details section
        $('.card-body .badge:contains("Active"), .card-body .badge:contains("Inactive")').each(function() {
            $(this).removeClass('badge-soft-success badge-soft-danger').addClass(statusClass);
            if ($(this).find('.ti').length) {
                $(this).html('<i class="ti ' + statusIcon + ' me-1"></i>' + statusText);
            } else {
                $(this).text(statusText);
            }
        });

        // Update city, country display in the header section
        $('.avatar-xxl.avatar-rounded').next().find('p.d-inline-flex').html('<i class="ti ti-map-pin text-warning me-1"></i> ' + location.city + ', ' + location.country);

        // Update sidebar information
        $('.card h6.mb-3.fw-semibold:contains("Location Information")').parent().find('a[href^="mailto:"]').text(location.email).attr('href', 'mailto:' + location.email);
        $('.card h6.mb-3.fw-semibold:contains("Location Information")').parent().find('.ti-map-pin').closest('div').find('p.mb-0').text(location.address);
        $('.card h6.mb-3.fw-semibold:contains("Location Information")').parent().find('.ti-building').closest('div').find('p.mb-0').text(location.city + ', ' + location.state);
        $('.card h6.mb-3.fw-semibold:contains("Location Information")').parent().find('.ti-world').closest('div').find('p.mb-0').text(location.country);
        $('.card h6.mb-3.fw-semibold:contains("Location Information")').parent().find('.ti-mailbox').closest('div').find('p.mb-0').text(location.zip_code);

        // Update status badge in Status Information section
        $('h6.mb-3.fw-semibold:contains("Status Information")').next().find('li.row:contains("Status") .col-6.text-dark .badge')
            .removeClass('badge-soft-success badge-soft-danger')
            .addClass(statusClass)
            .text(statusText);

        // Update last modified time
        var now = new Date();
        var formattedDate = now.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' }) +
                          ', ' + now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        $('h6.mb-3.fw-semibold:contains("Status Information")').next().find('li.row:contains("Last Modified") .col-6.text-dark').text(formattedDate);
        $('h6.mb-3.fw-semibold:contains("Status Information")').next().find('li.row:contains("Last Update") .col-6.text-dark').text('Just now');

        // Update Location Overview section
        var overviewCard = $('.card-title:contains("Location Overview")').closest('.card');
        overviewCard.find('h6.fw-semibold:contains("Location Name")').next().text(location.name);
        overviewCard.find('h6.fw-semibold:contains("Email Address")').next().html('<a href="mailto:' + location.email + '">' + location.email + '</a>');
        overviewCard.find('h6.fw-semibold:contains("Address")').next().text(location.address);
        overviewCard.find('h6.fw-semibold:contains("City")').next().text(location.city);
        overviewCard.find('h6.fw-semibold:contains("State")').next().text(location.state);
        overviewCard.find('h6.fw-semibold:contains("Country")').next().text(location.country);
        overviewCard.find('h6.fw-semibold:contains("Zip Code")').next().text(location.zip_code);

        // Update the created_at and updated_at information if available
        if(location.created_at) {
            $('.avatar-xxl.avatar-rounded').next().find('.ti-calendar-exclamation').closest('div').find('p.mb-0').text('Created: ' + new Date(location.created_at).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' +
                new Date(location.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }));
        }

        // Update created_by information if available
        if(location.created_by && location.created_by.name) {
            $('h6.mb-3.fw-semibold:contains("Status Information")').next().find('li.row:contains("Created By") .col-6.text-dark').text(location.created_by.name);
        }

        // Update updated_by information if available
        if(location.updated_by && location.updated_by.name) {
            $('h6.mb-3.fw-semibold:contains("Status Information")').next().find('li.row:contains("Updated By") .col-6.text-dark').text(location.updated_by.name);
        }
    }

    // Handle edit form submission
    $('#edit-location-form').on('submit', function(e) {
        e.preventDefault();
        var locationId = $(this).data('location-id');
        var formData = $(this).serialize();

        $.ajax({
            url: '/location/' + locationId,
            type: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message in the form
                    $('#edit-form-alert').show();

                    // Close the offcanvas after a short delay
                    setTimeout(function() {
                        $('#offcanvas_edit').offcanvas('hide');
                        $('#edit-form-alert').hide();

                        // Update the location data on the page without reloading
                        updateLocationDataOnPage(response.location);
                    }, 1500);
                } else {
                    alert('Error updating location');
                }
            },
            error: function(xhr) {
                console.error('Error updating location:', xhr);

                // Handle validation errors
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = 'Validation errors:\n';

                    for (var field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }

                    alert(errorMessage);
                } else {
                    alert('Error updating location: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
                }
            }
        });
    });
});
</script>
@endpush
