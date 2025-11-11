@extends('layouts.user')
@section('content')
    <div class="row y-gap-20 justify-between items-end pb-40">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600">{{ $page_title ?? __('transfers.admin.locations.create_title') }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('car.vendor.transfer-locations.index') }}" class="button -outline-blue-1 h-50 px-24">{{ __('transfers.admin.locations.cancel_button') }}</a>
        </div>
    </div>
    @include('admin.message')
    <div class="card px-30 py-30 rounded-4 bg-white shadow-3">
        <form method="post" action="{{ $row->id ? route('car.vendor.transfer-locations.store', $row) : route('car.vendor.transfer-locations.store', 0) }}" class="row y-gap-20">
            @csrf
            <div class="col-12">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.locations.field_name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $row->name) }}" required>
                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.locations.field_address') }}</label>
                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $row->address) }}" placeholder="{{ __('transfers.admin.locations.address_placeholder') }}">
                @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.locations.field_lat') }}</label>
                <input type="text" name="lat" class="form-control @error('lat') is-invalid @enderror" value="{{ old('lat', $row->lat) }}">
                @error('lat')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.locations.field_lng') }}</label>
                <input type="text" name="lng" class="form-control @error('lng') is-invalid @enderror" value="{{ old('lng', $row->lng) }}">
                @error('lng')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.locations.field_zoom') }}</label>
                <input type="number" name="map_zoom" min="1" max="20" class="form-control @error('map_zoom') is-invalid @enderror" value="{{ old('map_zoom', $row->map_zoom) }}">
                @error('map_zoom')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" value="1" id="locationActive" name="is_active" @checked(old('is_active', $row->is_active ?? true))>
                    <label class="form-check-label" for="locationActive">{{ __('transfers.admin.locations.field_active') }}</label>
                </div>
            </div>
            <div class="col-12 d-flex justify-end gap-10 pt-20">
                <button class="button h-50 px-24 -dark-1 bg-blue-1 text-white">{{ __('transfers.admin.locations.save_button') }}</button>
            </div>
        </form>
    </div>
@endsection
