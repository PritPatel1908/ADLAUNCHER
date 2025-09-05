@extends('layout.main')

@section('meta')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('css')
@endpush

@section('content')
    <div class="content pb-0">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Schedule Details</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('schedule.index') }}">Schedules</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Schedule Details</li>
                        </ol>
                    </nav>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-primary px-2 shadow"
                            data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <ul>
                                <li><a href="javascript:void(0);" class="dropdown-item"><i
                                            class="ti ti-file-type-pdf me-1"></i>Export as PDF</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item"><i
                                            class="ti ti-file-type-xls me-1"></i>Export as Excel</a></li>
                            </ul>
                        </div>
                    </div>
                    <a href="javascript:void(0);" class="btn btn-icon btn-outline-info shadow" data-bs-toggle="tooltip"
                        data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh"><i
                            class="ti ti-refresh"></i></a>
                    <a href="javascript:void(0);" class="btn btn-icon btn-outline-warning shadow" data-bs-toggle="tooltip"
                        data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse"
                        id="collapse-header"><i class="ti ti-transition-top"></i></a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <a href="{{ route('schedule.index') }}"><i class="ti ti-arrow-narrow-left me-1"></i>Back to
                            Schedules</a>
                    </div>
                    <div class="card">
                        <div class="card-body pb-2">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xxl avatar-rounded me-3 flex-shrink-0 bg-primary">
                                        <i class="ti ti-calendar"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $schedule->schedule_name }}</h5>
                                        <p class="mb-2">
                                            {{ optional($schedule->device)->name ?? optional($schedule->device)->unique_id }}
                                        </p>
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <span class="badge badge-soft-info me-2"><i class="ti ti-clock me-1"></i>Start:
                                                {{ \Carbon\Carbon::parse($schedule->schedule_start_date_time)->format('d M Y, h:i A') }}</span>
                                            <span class="badge badge-soft-warning"><i class="ti ti-clock me-1"></i>End:
                                                {{ \Carbon\Carbon::parse($schedule->schedule_end_date_time)->format('d M Y, h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvas_edit"><i class="ti ti-edit me-1"></i>Edit Schedule</a>
                                    <form action="{{ route('schedule.destroy', $schedule->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this schedule?')">
                                            <i class="ti ti-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Schedule Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Name</h6>
                                        <p>{{ $schedule->schedule_name }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Start At</h6>
                                        <p>{{ \Carbon\Carbon::parse($schedule->schedule_start_date_time)->format('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">End At</h6>
                                        <p>{{ \Carbon\Carbon::parse($schedule->schedule_end_date_time)->format('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Created</h6>
                                        <p class="text-muted">{{ $schedule->created_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Target</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Device</h6>
                                        <p>{{ optional($schedule->device)->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Layout</h6>
                                        <p>{{ optional($schedule->layout)->layout_name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Screen</h6>
                                        <p>{{ optional($schedule->screen)->screen_no ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-0">Schedule Medias</h5>
                            </div>
                            <a class="btn btn-primary btn-sm"
                                href="{{ route('schedule-media.index') }}?schedule_id={{ $schedule->id }}"><i
                                    class="ti ti-plus me-1"></i>Manage Medias</a>
                        </div>
                        <div class="card-body">
                            @if ($schedule->medias && $schedule->medias->count())
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Type</th>
                                                <th>Duration</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($schedule->medias as $m)
                                                <tr>
                                                    <td>{{ $m->title }}</td>
                                                    <td>{{ ucfirst($m->media_type) }}</td>
                                                    <td>{{ $m->duration_seconds }}s</td>
                                                    <td>{{ optional($m->created_at)->format('d M Y, h:i A') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="ti ti-photo text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="text-muted mt-2">No media found</h6>
                                    <p class="text-muted">Add media items to this schedule.</p>
                                    <a class="btn btn-primary btn-sm"
                                        href="{{ route('schedule-media.index') }}?schedule_id={{ $schedule->id }}"><i
                                            class="ti ti-plus me-1"></i>Add Media</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit"
        aria-labelledby="offcanvas_edit_label">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvas_edit_label">Edit Schedule</h5>
            <button type="button"
                class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                data-bs-dismiss="offcanvas" aria-label="Close"><i class="ti ti-x"></i></button>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body">
                    <form id="edit-schedule-form" method="POST" action="{{ route('schedule.update', $schedule->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="accordion accordion-bordered" id="main_accordion">
                            <div class="accordion-item rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded"
                                        data-bs-toggle="collapse" data-bs-target="#basic">
                                        <span class="avatar avatar-md rounded me-1"><i class="ti ti-calendar"></i></span>
                                        Schedule Info
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse show" id="basic"
                                    data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="schedule_name"
                                                        id="edit-schedule_name" value="{{ $schedule->schedule_name }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Start At <span
                                                            class="text-danger">*</span></label>
                                                    <input type="datetime-local" class="form-control"
                                                        name="schedule_start_date_time" id="edit-start_at"
                                                        value="{{ \Carbon\Carbon::parse($schedule->schedule_start_date_time)->format('Y-m-d\TH:i') }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">End At <span
                                                            class="text-danger">*</span></label>
                                                    <input type="datetime-local" class="form-control"
                                                        name="schedule_end_date_time" id="edit-end_at"
                                                        value="{{ \Carbon\Carbon::parse($schedule->schedule_end_date_time)->format('Y-m-d\TH:i') }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Device</label>
                                                    <select class="form-control select2" name="device_id"
                                                        id="edit-device_id" data-toggle="select2">
                                                        <option value="">Select device...</option>
                                                        @foreach (\App\Models\Device::where('status', 1)->get() as $device)
                                                            <option value="{{ $device->id }}"
                                                                {{ $schedule->device_id == $device->id ? 'selected' : '' }}>
                                                                {{ $device->name }} - {{ $device->unique_id }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Layout</label>
                                                    <select class="form-control select2" name="layout_id"
                                                        id="edit-layout_id" data-toggle="select2">
                                                        <option value="">Select layout...</option>
                                                        @if ($schedule->device_id)
                                                            @foreach (\App\Models\DeviceLayout::where('device_id', $schedule->device_id)->where('status', 1)->get() as $layout)
                                                                <option value="{{ $layout->id }}"
                                                                    {{ $schedule->layout_id == $layout->id ? 'selected' : '' }}>
                                                                    {{ $layout->layout_name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Screen</label>
                                                    <select class="form-control select2" name="screen_id"
                                                        id="edit-screen_id" data-toggle="select2">
                                                        <option value="">Select screen...</option>
                                                        @if ($schedule->device_id)
                                                            @foreach (\App\Models\DeviceScreen::where('device_id', $schedule->device_id)->get() as $screen)
                                                                <option value="{{ $screen->id }}"
                                                                    {{ $schedule->screen_id == $screen->id ? 'selected' : '' }}>
                                                                    Screen {{ $screen->screen_no }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded collapsed"
                                        data-bs-toggle="collapse" data-bs-target="#edit-media">
                                        <span class="avatar avatar-md rounded me-1"><i class="ti ti-photo"></i></span>
                                        Schedule Media
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse" id="edit-media"
                                    data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div id="edit-media-container">
                                            @if ($schedule->medias && $schedule->medias->count())
                                                @foreach ($schedule->medias as $index => $media)
                                                    <div class="media-item border rounded p-3 mb-3"
                                                        data-media-id="{{ $media->id }}">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Media Title</label>
                                                                    <input type="text" class="form-control"
                                                                        name="edit_media_title[]"
                                                                        value="{{ $media->title }}"
                                                                        placeholder="Enter media title">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Media Type</label>
                                                                    <select class="form-control select2"
                                                                        name="edit_media_type[]" data-toggle="select2">
                                                                        <option value="">Select type...</option>
                                                                        <option value="image"
                                                                            {{ $media->media_type == 'image' ? 'selected' : '' }}>
                                                                            Image</option>
                                                                        <option value="video"
                                                                            {{ $media->media_type == 'video' ? 'selected' : '' }}>
                                                                            Video</option>
                                                                        <option value="audio"
                                                                            {{ $media->media_type == 'audio' ? 'selected' : '' }}>
                                                                            Audio</option>
                                                                        <option value="mp4"
                                                                            {{ $media->media_type == 'mp4' ? 'selected' : '' }}>
                                                                            MP4</option>
                                                                        <option value="png"
                                                                            {{ $media->media_type == 'png' ? 'selected' : '' }}>
                                                                            PNG</option>
                                                                        <option value="jpg"
                                                                            {{ $media->media_type == 'jpg' ? 'selected' : '' }}>
                                                                            JPG</option>
                                                                        <option value="pdf"
                                                                            {{ $media->media_type == 'pdf' ? 'selected' : '' }}>
                                                                            PDF</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Duration (seconds)</label>
                                                                    <input type="number" class="form-control"
                                                                        name="edit_duration_seconds[]"
                                                                        value="{{ $media->duration_seconds }}"
                                                                        placeholder="Duration in seconds" min="1">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">&nbsp;</label>
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm w-100 remove-edit-media"
                                                                        data-media-id="{{ $media->id }}">
                                                                        <i class="ti ti-trash"></i> Remove
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Current Media File</label>
                                                                    <div class="form-control-plaintext">
                                                                        <small
                                                                            class="text-muted">{{ $media->media_file }}</small>
                                                                    </div>
                                                                    <input type="file" class="form-control mt-2"
                                                                        name="edit_media_file[]"
                                                                        accept="image/*,video/*,audio/*,.mp4,.png,.jpg,.pdf">
                                                                    <input type="hidden" name="edit_media_id[]"
                                                                        value="{{ $media->id }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="media-item border rounded p-3 mb-3">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Media Title</label>
                                                                <input type="text" class="form-control"
                                                                    name="edit_media_title[]"
                                                                    placeholder="Enter media title">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Media Type</label>
                                                                <select class="form-control select2"
                                                                    name="edit_media_type[]" data-toggle="select2">
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
                                                                <input type="number" class="form-control"
                                                                    name="edit_duration_seconds[]"
                                                                    placeholder="Duration in seconds" min="1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm w-100 remove-edit-media">
                                                                    <i class="ti ti-trash"></i> Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label class="form-label">Media File</label>
                                                                <input type="file" class="form-control"
                                                                    name="edit_media_file[]"
                                                                    accept="image/*,video/*,audio/*,.mp4,.png,.jpg,.pdf">
                                                                <input type="hidden" name="edit_media_id[]"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                id="add-edit-media">
                                                <i class="ti ti-plus me-1"></i>Add Another Media
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="edit-form-alert" class="col-12" style="display: none;">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Schedule updated successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="button" data-bs-dismiss="offcanvas"
                                class="btn btn-sm btn-light me-2">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary">Update Schedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/schedule-show.js') }}" type="text/javascript"></script>
@endpush
