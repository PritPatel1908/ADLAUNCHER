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
        <div class="container-fluid">
            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Company Details</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('company.index') }}">Companies</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Company Details</li>
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
                        data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse"
                        id="collapse-header"><i class="ti ti-transition-top"></i></a>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <a href="{{ route('company.index') }}"><i class="ti ti-arrow-narrow-left me-1"></i>Back to
                            Companies</a>
                    </div>

                    <!-- Company Header Card -->
                    <div class="card">
                        <div class="card-body pb-2">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xxl avatar-rounded me-3 flex-shrink-0 bg-primary">
                                        <span class="avatar-text">{{ substr($company->name, 0, 1) }}</span>
                                        <span
                                            class="status {{ $company->status == 'active' ? 'online' : 'offline' }}"></span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $company->name }}</h5>
                                        <p class="mb-2">{{ $company->industry }}</p>
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <span
                                                class="badge {{ $company->status == 'active' ? 'badge-soft-success' : 'badge-soft-danger' }} border-0 me-2">
                                                <i
                                                    class="ti {{ $company->status == 'active' ? 'ti-check' : 'ti-lock' }} me-1"></i>
                                                {{ $company->status == 'active' ? 'Active' : 'Inactive' }}
                                            </span>
                                            @if ($company->website)
                                                <p class="d-inline-flex align-items-center mb-0 me-3">
                                                    <i class="ti ti-world text-warning me-1"></i> {{ $company->website }}
                                                </p>
                                            @endif
                                            @if ($company->email)
                                                <p class="d-inline-flex align-items-center mb-0 me-3">
                                                    <i class="ti ti-mail text-info me-1"></i> {{ $company->email }}
                                                </p>
                                            @endif
                                            @if ($company->phone)
                                                <p class="d-inline-flex align-items-center mb-0">
                                                    <i class="ti ti-phone text-success me-1"></i> {{ $company->phone }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvas_edit">
                                        <i class="ti ti-edit me-1"></i>Edit Company
                                    </a>
                                    <form action="{{ route('company.destroy', $company->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this company?')">
                                            <i class="ti ti-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Information Cards -->
            <div class="row">
                <!-- Company Overview -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Company Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Company Name</h6>
                                        <p>{{ $company->name }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Email Address</h6>
                                        <p><a href="mailto:{{ $company->email }}">{{ $company->email }}</a></p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Industry</h6>
                                        <p>{{ $company->industry }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Website</h6>
                                        <p>{{ $company->website }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Phone</h6>
                                        <p>{{ $company->phone }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Status</h6>
                                        <p><span
                                                class="badge {{ $company->status == 'active' ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                                {{ $company->status == 'active' ? 'Active' : 'Inactive' }}
                                            </span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Statistics -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Company Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Locations</h6>
                                        <p class="h4 text-primary">{{ $company->locations->count() }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Addresses</h6>
                                        <p class="h4 text-success">{{ $company->addresses->count() }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Contacts</h6>
                                        <p class="h4 text-warning">{{ $company->contacts->count() }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-semibold">Notes</h6>
                                        <p class="h4 text-info">{{ $company->notes->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="fw-semibold">Created</h6>
                                <p class="text-muted">{{ $company->created_at->format('d M Y, h:i A') }}</p>
                                @if ($company->updated_at)
                                    <h6 class="fw-semibold">Last Updated</h6>
                                    <p class="text-muted">{{ $company->updated_at->format('d M Y, h:i A') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Details Sections -->
            <div class="row">
                <!-- Locations -->
                @if ($company->locations->count() > 0)
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Locations ({{ $company->locations->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($company->locations as $location)
                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3">
                                                <h6 class="fw-semibold">{{ $location->name }}</h6>
                                                <p class="mb-1"><i
                                                        class="ti ti-mail text-info me-2"></i>{{ $location->email }}</p>
                                                <p class="mb-1"><i
                                                        class="ti ti-map-pin text-warning me-2"></i>{{ $location->address }}
                                                </p>
                                                <p class="mb-0"><i
                                                        class="ti ti-building text-success me-2"></i>{{ $location->city }},
                                                    {{ $location->state }}, {{ $location->country }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Addresses -->
                @if ($company->addresses->count() > 0)
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Addresses ({{ $company->addresses->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($company->addresses as $address)
                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3">
                                                <h6 class="fw-semibold">{{ $address->type }}</h6>
                                                <p class="mb-1"><i
                                                        class="ti ti-map-pin text-warning me-2"></i>{{ $address->address }}
                                                </p>
                                                <p class="mb-0"><i
                                                        class="ti ti-building text-success me-2"></i>{{ $address->city }},
                                                    {{ $address->state }}, {{ $address->country }}
                                                    {{ $address->zip_code }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Contacts -->
                @if ($company->contacts->count() > 0)
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Contacts ({{ $company->contacts->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($company->contacts as $contact)
                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <h6 class="fw-semibold mb-0">{{ $contact->name }}</h6>
                                                    @if ($contact->is_primary)
                                                        <span class="badge badge-soft-primary ms-2">Primary</span>
                                                    @endif
                                                </div>
                                                <p class="mb-1"><i class="ti ti-mail text-info me-2"></i><a
                                                        href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></p>
                                                <p class="mb-1"><i
                                                        class="ti ti-phone text-success me-2"></i>{{ $contact->phone }}
                                                </p>
                                                <p class="mb-0"><i
                                                        class="ti ti-user text-warning me-2"></i>{{ $contact->designation }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if ($company->notes->count() > 0)
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Notes ({{ $company->notes->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($company->notes as $note)
                                        <div class="col-md-12 mb-3">
                                            <div class="border rounded p-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span
                                                        class="badge {{ $note->status == 'active' ? 'badge-soft-success' : 'badge-soft-danger' }}">{{ $note->status == 'active' ? 'Active' : 'Inactive' }}</span>
                                                    <small
                                                        class="text-muted">{{ $note->created_at->format('d M Y, h:i A') }}</small>
                                                </div>
                                                <p class="mb-0">{{ $note->note }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- End Content -->

    <!-- Edit Company Offcanvas -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit"
        aria-labelledby="offcanvas_edit_label">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvas_edit_label">Edit Company</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body">
                    <form id="edit-company-form" method="POST" action="{{ route('company.update', $company->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="from_show" value="1">
                        <div class="accordion accordion-bordered" id="main_accordion">
                            <!-- Basic Info -->
                            <div class="accordion-item rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded"
                                        data-bs-toggle="collapse" data-bs-target="#basic">
                                        <span class="avatar avatar-md rounded me-1"><i class="ti ti-user-plus"></i></span>
                                        Basic Info
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse show" id="basic"
                                    data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Company Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name"
                                                        id="edit-name" value="{{ $company->name }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Industry</label>
                                                    <input type="text" class="form-control" name="industry"
                                                        id="edit-industry" value="{{ $company->industry }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Website</label>
                                                    <input type="url" class="form-control" name="website"
                                                        id="edit-website" value="{{ $company->website }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email"
                                                        id="edit-email" value="{{ $company->email }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Phone</label>
                                                    <input type="text" class="form-control" name="phone"
                                                        id="edit-phone" value="{{ $company->phone }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status" id="edit-status">
                                                        <option value="active"
                                                            {{ $company->status == 'active' ? 'selected' : '' }}>Active
                                                        </option>
                                                        <option value="inactive"
                                                            {{ $company->status == 'inactive' ? 'selected' : '' }}>Inactive
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Location --}}
                            <div class="accordion-item border-top rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded"
                                        data-bs-toggle="collapse" data-bs-target="#location">
                                        <span class="avatar avatar-md rounded me-1"><i
                                                class="ti ti-map-pin-cog"></i></span>
                                        Location Info
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse" id="location" data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Locations</label>
                                                    <select class="select2 form-control select2-multiple"
                                                        name="location_ids[]" id="edit-location_ids"
                                                        data-toggle="select2" multiple="multiple"
                                                        data-placeholder="Choose locations...">
                                                        @foreach (\App\Models\Location::where('status', 1)->get() as $location)
                                                            <option value="{{ $location->id }}"
                                                                {{ $company->locations->contains($location->id) ? 'selected' : '' }}>
                                                                {{ $location->name }} - {{ $location->city }},
                                                                {{ $location->country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="accordion-item border-top rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded"
                                        data-bs-toggle="collapse" data-bs-target="#address">
                                        <span class="avatar avatar-md rounded me-1"><i
                                                class="ti ti-map-pin-cog"></i></span>
                                        Address Info
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse" id="address" data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Addresses</label>
                                                    <div id="edit-addresses-container">
                                                        @foreach ($company->addresses as $index => $address)
                                                            <div class="address-item border rounded p-3 mb-2">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <select class="form-select"
                                                                            name="addresses[{{ $index }}][type]">
                                                                            <option value="">Select Type</option>
                                                                            <option value="Head Office"
                                                                                {{ $address->type == 'Head Office' ? 'selected' : '' }}>
                                                                                Head Office</option>
                                                                            <option value="Branch"
                                                                                {{ $address->type == 'Branch' ? 'selected' : '' }}>
                                                                                Branch</option>
                                                                            <option value="Office"
                                                                                {{ $address->type == 'Office' ? 'selected' : '' }}>
                                                                                Office</option>
                                                                            <option value="Warehouse"
                                                                                {{ $address->type == 'Warehouse' ? 'selected' : '' }}>
                                                                                Warehouse</option>
                                                                            <option value="Factory"
                                                                                {{ $address->type == 'Factory' ? 'selected' : '' }}>
                                                                                Factory</option>
                                                                            <option value="Store"
                                                                                {{ $address->type == 'Store' ? 'selected' : '' }}>
                                                                                Store</option>
                                                                            <option value="Billing"
                                                                                {{ $address->type == 'Billing' ? 'selected' : '' }}>
                                                                                Billing</option>
                                                                            <option value="Shipping"
                                                                                {{ $address->type == 'Shipping' ? 'selected' : '' }}>
                                                                                Shipping</option>
                                                                            <option value="Other"
                                                                                {{ $address->type == 'Other' ? 'selected' : '' }}>
                                                                                Other</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control"
                                                                            name="addresses[{{ $index }}][address]"
                                                                            value="{{ $address->address }}"
                                                                            placeholder="Address">
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-2">
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control"
                                                                            name="addresses[{{ $index }}][city]"
                                                                            value="{{ $address->city }}"
                                                                            placeholder="City">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control"
                                                                            name="addresses[{{ $index }}][state]"
                                                                            value="{{ $address->state }}"
                                                                            placeholder="State">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control"
                                                                            name="addresses[{{ $index }}][country]"
                                                                            value="{{ $address->country }}"
                                                                            placeholder="Country">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control"
                                                                            name="addresses[{{ $index }}][zip_code]"
                                                                            value="{{ $address->zip_code }}"
                                                                            placeholder="Zip Code">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="addEditAddress()">
                                                        <i class="ti ti-plus me-1"></i>Add Address
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Contacts --}}
                            <div class="accordion-item border-top rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded"
                                        data-bs-toggle="collapse" data-bs-target="#contacts">
                                        <span class="avatar avatar-md rounded me-1"><i class="ti ti-user-plus"></i></span>
                                        Contacts Info
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse" id="contacts" data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Contacts</label>
                                                    <div id="edit-contacts-container">
                                                        @foreach ($company->contacts as $index => $contact)
                                                            <div class="contact-item border rounded p-3 mb-2">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <input type="text" class="form-control"
                                                                            name="contacts[{{ $index }}][name]"
                                                                            value="{{ $contact->name }}"
                                                                            placeholder="Contact Name">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <input type="email" class="form-control"
                                                                            name="contacts[{{ $index }}][email]"
                                                                            value="{{ $contact->email }}"
                                                                            placeholder="Email">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <input type="text" class="form-control"
                                                                            name="contacts[{{ $index }}][phone]"
                                                                            value="{{ $contact->phone }}"
                                                                            placeholder="Phone">
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-2">
                                                                    <div class="col-md-8">
                                                                        <input type="text" class="form-control"
                                                                            name="contacts[{{ $index }}][designation]"
                                                                            value="{{ $contact->designation }}"
                                                                            placeholder="Designation">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                name="contacts[{{ $index }}][is_primary]"
                                                                                value="1"
                                                                                {{ $contact->is_primary ? 'checked' : '' }}>
                                                                            <label class="form-check-label">Primary
                                                                                Contact</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="addEditContact()">
                                                        <i class="ti ti-plus me-1"></i>Add Contact
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="accordion-item border-top rounded mb-3">
                                <div class="accordion-header">
                                    <a href="#" class="accordion-button accordion-custom-button rounded"
                                        data-bs-toggle="collapse" data-bs-target="#notes">
                                        <span class="avatar avatar-md rounded me-1"><i class="ti ti-note"></i></span>
                                        Notes Info
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse" id="notes" data-bs-parent="#main_accordion">
                                    <div class="accordion-body border-top">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <div id="edit-notes-container">
                                                        @foreach ($company->notes as $index => $note)
                                                            <div class="note-item border rounded p-3 mb-2">
                                                                <div class="row">
                                                                    <div class="col-md-9">
                                                                        <textarea class="form-control" name="notes[{{ $index }}][note]" rows="3" placeholder="Note content">{{ $note->note }}</textarea>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <select class="form-select"
                                                                            name="notes[{{ $index }}][status]">
                                                                            <option value="active"
                                                                                {{ $note->status == 'active' ? 'selected' : '' }}>
                                                                                Active</option>
                                                                            <option value="inactive"
                                                                                {{ $note->status == 'inactive' ? 'selected' : '' }}>
                                                                                Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="addEditNote()">
                                                        <i class="ti ti-plus me-1"></i>Add Note
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="edit-form-alert" class="col-12" style="display: none;">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Company updated successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="button" data-bs-dismiss="offcanvas"
                                class="btn btn-sm btn-light me-2">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary">Update Company</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Select2 CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <!-- Company Show JS -->
    <script src="{{ asset('assets/js/datatable/company-show.js') }}" type="text/javascript"></script>

    <style>
        /* Select2 Custom Styles - Perfect Design Match */
        .select2-container {
            width: 100% !important;
            font-family: inherit !important;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d1d3e2 !important;
            border-radius: 0.375rem !important;
            min-height: 38px !important;
            padding: 0.375rem 0.75rem !important;
            background-color: #fff !important;
            box-sizing: border-box !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
            padding: 0 !important;
            margin: 0 !important;
            line-height: normal !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            position: relative !important;
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 0.25rem !important;
            padding: 0.25rem 0.5rem !important;
            margin: 0 !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            color: #495057 !important;
            display: inline-flex !important;
            align-items: center !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            font-weight: 400 !important;
        }

        /* Fix for duplicate cross symbols - hide default Select2 remove button */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            position: relative !important;
            right: auto !important;
            top: auto !important;
            transform: none !important;
            color: #6c757d !important;
            font-weight: bold !important;
            font-size: 0 !important;
            /* Hide the default text */
            line-height: 0 !important;
            cursor: pointer !important;
            border: none !important;
            background: none !important;
            padding: 0 !important;
            margin: 0 0 0 0.25rem !important;
            width: 16px !important;
            height: 16px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            float: none !important;
            text-decoration: none !important;
            font-family: inherit !important;
            overflow: hidden !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #dc3545 !important;
        }

        /* Show only our custom × symbol */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:before {
            content: "×" !important;
            font-size: 1.125rem !important;
            line-height: 1 !important;
            color: #6c757d !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
        }

        /* Hide any other potential × symbols or text */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:after,
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove span,
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove i {
            display: none !important;
            content: "" !important;
        }

        .select2-container--default .select2-search--inline {
            margin: 0 !important;
            padding: 0 !important;
            flex: 1 !important;
            min-width: 120px !important;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            width: 100% !important;
            background: transparent !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-dropdown {
            border: 1px solid #d1d3e2 !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
            background-color: #fff !important;
            z-index: 9999 !important;
            margin-top: 2px !important;
        }

        .select2-container--default .select2-results__option {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd !important;
            color: white !important;
        }

        .select2-container--default .select2-results__group {
            font-weight: 600 !important;
            color: #6c757d !important;
            padding: 0.5rem 0.75rem 0.25rem !important;
            font-size: 0.875rem !important;
            background-color: #f8f9fa !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e9ecef !important;
            color: #495057 !important;
        }

        /* Focus state */
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        /* Placeholder styling */
        .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
            color: #6c757d !important;
            font-size: 0.875rem !important;
        }
    </style>

    <!-- The inline script has been moved to company-show.js -->
    <script>
        $(document).ready(function() {
            // Initialize Select2 with proper configuration
            $('.select2-multiple').each(function() {
                $(this).select2({
                    theme: 'default',
                    width: '100%',
                    placeholder: 'Choose locations...',
                    allowClear: true,
                    closeOnSelect: false,
                    tags: false,
                    tokenSeparators: [',', ' '],
                    language: {
                        noResults: function() {
                            return "No locations found";
                        },
                        searching: function() {
                            return "Searching...";
                        }
                    }
                });
            });

            // Re-initialize Select2 after dynamic content is added
            $(document).on('shown.bs.offcanvas', function() {
                $('.select2-multiple').each(function() {
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

            // Handle form submission for editing company
            $('#edit-company-form').on('submit', function(e) {
                e.preventDefault();

                // Get form data and action URL
                const formData = $(this).serialize();
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
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            $('#edit-form-alert').removeClass('alert-danger').addClass(
                                'alert-success');
                            $('#edit-form-alert').html(
                                'Company updated successfully! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                            );
                            $('#edit-form-alert').show();

                            // Update the page content with the new company data
                            updateCompanyDetails(response.company);

                            // Close the offcanvas after a delay (matching Index page behavior)
                            setTimeout(function() {
                                $('#offcanvas_edit').offcanvas('hide');
                            }, 2000);
                        } else {
                            $('#edit-form-alert').removeClass('alert-success').addClass(
                                'alert-danger');
                            $('#edit-form-alert').html(
                                `Failed to update company: ${response.message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`
                            );
                            $('#edit-form-alert').show();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error updating company:', xhr);

                        let errorMessage = 'Failed to update company. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = '<ul>';
                            for (const field in xhr.responseJSON.errors) {
                                errorMessage += `<li>${xhr.responseJSON.errors[field][0]}</li>`;
                            }
                            errorMessage += '</ul>';
                        }

                        $('#edit-form-alert').removeClass('alert-success').addClass(
                            'alert-danger');
                        $('#edit-form-alert').html(
                            `${errorMessage} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`
                        );
                        $('#edit-form-alert').show();
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.html(originalBtnText).prop('disabled', false);
                    }
                });
            });

            // Function to update company details on the page without reloading
            function updateCompanyDetails(company) {
                // Update company name
                $('h5.mb-1:contains("' + company.name + '")').text(company.name);

                // Update industry
                $('p.mb-2:contains("' + company.industry + '")').text(company.industry);

                // Update status badge
                const statusClass = company.status == 1 ? 'badge-soft-success' : 'badge-soft-danger';
                const statusIcon = company.status == 1 ? 'ti-check' : 'ti-lock';
                const statusText = company.status == 1 ? 'Active' : 'Inactive';
                $('.badge:contains("Active"), .badge:contains("Inactive")').first()
                    .removeClass('badge-soft-success badge-soft-danger')
                    .addClass(statusClass)
                    .html(`<i class="ti ${statusIcon} me-1"></i>${statusText}`);

                // Update website, email, phone
                if (company.website) {
                    $('.d-inline-flex:contains("' + company.website + '")').html(
                        `<i class="ti ti-world text-warning me-1"></i> ${company.website}`);
                }
                if (company.email) {
                    $('.d-inline-flex:contains("' + company.website + '")').html(
                        `<i class="ti ti-mail text-info me-1"></i> ${company.email}`);
                }
                if (company.phone) {
                    $('.d-inline-flex:contains("' + company.phone + '")').html(
                        `<i class="ti ti-phone text-success me-1"></i> ${company.phone}`);
                }

                // Update company overview section
                $('.col-md-6 .mb-4:contains("Company Name") p').text(company.name);
                $('.col-md-6 .mb-4:contains("Email Address") p a').text(company.email).attr('href', 'mailto:' +
                    company.email);
                $('.col-md-6 .mb-4:contains("Industry") p').text(company.industry);
                $('.col-md-6 .mb-4:contains("Website") p').text(company.website);
                $('.col-md-6 .mb-4:contains("Phone") p').text(company.phone);

                // Update status in overview
                $('.col-md-6 .mb-4:contains("Status") p span')
                    .removeClass('badge-soft-success badge-soft-danger')
                    .addClass(statusClass)
                    .text(statusText);
            }
        });

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
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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
    </script>
@endpush
