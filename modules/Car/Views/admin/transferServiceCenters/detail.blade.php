@extends('layouts.admin')
@section('content')
    <h1 class="fw-500 mb-3">{{ $page_title ?? __('transfers.admin.service_centers.title') }}</h1>
    @include('admin.message')
    <form method="post" action="{{ $row->id ? route('car.admin.transfer-service-centers.store', $row) : route('car.admin.transfer-service-centers.store', 0) }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('transfers.admin.service_centers.field_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $row->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('transfers.admin.service_centers.field_address') }}</label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $row->address) }}" placeholder="{{ __('transfers.admin.service_centers.address_placeholder') }}">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('transfers.admin.service_centers.field_lat') }}</label>
                        <input type="text" name="lat" class="form-control @error('lat') is-invalid @enderror" value="{{ old('lat', $row->lat) }}">
                        @error('lat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('transfers.admin.service_centers.field_lng') }}</label>
                        <input type="text" name="lng" class="form-control @error('lng') is-invalid @enderror" value="{{ old('lng', $row->lng) }}">
                        @error('lng')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('transfers.admin.service_centers.field_location') }}</label>
                        <select name="location_id" class="form-select @error('location_id') is-invalid @enderror">
                            <option value="">{{ __('transfers.admin.service_centers.location_any') }}</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('location_id', $row->location_id) == $location->id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">{{ __('transfers.admin.service_centers.field_vendor') }}</label>
                    <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                        <option value="">{{ __('transfers.admin.service_centers.vendor_any') }}</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" @selected(old('vendor_id', $row->vendor_id) == $vendor->id)>{{ $vendor->name ?? $vendor->email }}</option>
                        @endforeach
                    </select>
                    @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary">{{ __('transfers.admin.service_centers.save_button') }}</button>
                <a href="{{ route('car.admin.transfer-service-centers.index') }}" class="btn btn-outline-secondary">{{ __('transfers.admin.service_centers.cancel_button') }}</a>
            </div>
        </div>
    </form>
@endsection
