@extends('layouts.user')
@section('content')
    <div class="row y-gap-20 justify-between items-end pb-40">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600">{{ $page_title ?? __('transfers.admin.service_centers.create_title') }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('car.vendor.transfer-service-centers.index') }}" class="button -outline-blue-1 h-50 px-24">{{ __('transfers.admin.service_centers.cancel_button') }}</a>
        </div>
    </div>
    @include('admin.message')
    <div class="card px-30 py-30 rounded-4 bg-white shadow-3">
        <form method="post" action="{{ $row->id ? route('car.vendor.transfer-service-centers.store', $row) : route('car.vendor.transfer-service-centers.store', 0) }}" class="row y-gap-20">
            @csrf
            <div class="col-12">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.service_centers.field_name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $row->name) }}" required>
                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.service_centers.field_address') }}</label>
                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $row->address) }}" placeholder="{{ __('transfers.admin.service_centers.address_placeholder') }}">
                @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.service_centers.field_lat') }}</label>
                <input type="text" name="lat" class="form-control @error('lat') is-invalid @enderror" value="{{ old('lat', $row->lat) }}">
                @error('lat')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.service_centers.field_lng') }}</label>
                <input type="text" name="lng" class="form-control @error('lng') is-invalid @enderror" value="{{ old('lng', $row->lng) }}">
                @error('lng')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.service_centers.field_location') }}</label>
                <select name="location_id" class="form-select @error('location_id') is-invalid @enderror">
                    <option value="">{{ __('transfers.admin.service_centers.location_any') }}</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" @selected(old('location_id', $row->location_id) == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('location_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-end gap-10 pt-20">
                <button class="button h-50 px-24 -dark-1 bg-blue-1 text-white">{{ __('transfers.admin.service_centers.save_button') }}</button>
            </div>
        </form>
    </div>
@endsection
