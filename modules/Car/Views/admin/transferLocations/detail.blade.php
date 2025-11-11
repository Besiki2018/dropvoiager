@extends('layouts.admin')
@section('content')
    <h1 class="fw-500 mb-3">{{ $page_title ?? __('transfers.admin.locations.title') }}</h1>
    @include('admin.message')
    <form method="post" action="{{ $row->id ? route('car.admin.transfer-locations.store', $row) : route('car.admin.transfer-locations.store', 0) }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('transfers.admin.locations.field_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $row->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('transfers.admin.locations.field_address') }}</label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $row->address) }}" placeholder="{{ __('transfers.admin.locations.address_placeholder') }}">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('transfers.admin.locations.field_lat') }}</label>
                        <input type="text" name="lat" class="form-control @error('lat') is-invalid @enderror" value="{{ old('lat', $row->lat) }}" placeholder="41.715137">
                        @error('lat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('transfers.admin.locations.field_lng') }}</label>
                        <input type="text" name="lng" class="form-control @error('lng') is-invalid @enderror" value="{{ old('lng', $row->lng) }}" placeholder="44.827095">
                        @error('lng')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('transfers.admin.locations.field_zoom') }}</label>
                        <input type="number" min="1" max="20" name="map_zoom" class="form-control @error('map_zoom') is-invalid @enderror" value="{{ old('map_zoom', $row->map_zoom) }}" placeholder="10">
                        @error('map_zoom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('transfers.admin.locations.field_vendor') }}</label>
                        <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                            <option value="">{{ __('transfers.admin.locations.vendor_any') }}</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @selected(old('vendor_id', $row->vendor_id) == $vendor->id)>{{ $vendor->name ?? $vendor->email }}</option>
                            @endforeach
                        </select>
                        @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check mt-4">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" value="1" id="location-active" name="is_active" @checked(old('is_active', $row->is_active ?? true))>
                            <label class="form-check-label" for="location-active">{{ __('transfers.admin.locations.field_active') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary">{{ __('transfers.admin.locations.save_button') }}</button>
                <a href="{{ route('car.admin.transfer-locations.index') }}" class="btn btn-outline-secondary">{{ __('transfers.admin.locations.cancel_button') }}</a>
            </div>
        </div>
    </form>
@endsection
