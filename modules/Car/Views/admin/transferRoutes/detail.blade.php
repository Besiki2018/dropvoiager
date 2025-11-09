@extends('admin.layouts.app')
@section('content')
    <form action="{{ $row->id ? route('car.admin.transfer-routes.store', ['id' => $row->id]) : route('car.admin.transfer-routes.store', ['id' => 0]) }}" method="post" class="container-fluid">
        @csrf
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ $row->id ? __('transfers.admin.routes.edit_title', ['name' => $row->display_name]) : __('transfers.admin.routes.create_title') }}</h1>
            <div class="title-actions">
                <button class="btn btn-primary" type="submit">{{ __('transfers.admin.routes.save') }}</button>
            </div>
        </div>
        @include('admin.message')
        <div class="row">
            <div class="col-lg-8">
                <div class="panel">
                    <div class="panel-title"><strong>{{ __('transfers.admin.routes.details_panel') }}</strong></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $row->name) }}" placeholder="{{ __('transfers.admin.routes.field_name_placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_pickup_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="pickup_name" class="form-control" value="{{ old('pickup_name', $row->pickup_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_pickup_address') }}</label>
                            <input type="text" name="pickup_address" class="form-control" value="{{ old('pickup_address', $row->pickup_address) }}" placeholder="{{ __('transfers.admin.routes.address_placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_pickup_coordinates') }}</label>
                            <div class="control-map-group">
                                <div id="pickup_map" style="height: 300px;"></div>
                                <input type="text" placeholder="{{ __('transfers.admin.routes.map_search_placeholder') }}" class="form-control pickup-search" autocomplete="off" onkeydown="return event.key !== 'Enter';">
                                <div class="g-control">
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.routes.field_lat') }}</label>
                                        <input type="text" name="pickup_lat" class="form-control" value="{{ old('pickup_lat', $row->pickup_lat) }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.routes.field_lng') }}</label>
                                        <input type="text" name="pickup_lng" class="form-control" value="{{ old('pickup_lng', $row->pickup_lng) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_dropoff_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="dropoff_name" class="form-control" value="{{ old('dropoff_name', $row->dropoff_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_dropoff_address') }}</label>
                            <input type="text" name="dropoff_address" class="form-control" value="{{ old('dropoff_address', $row->dropoff_address) }}" placeholder="{{ __('transfers.admin.routes.address_placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_dropoff_coordinates') }}</label>
                            <div class="control-map-group">
                                <div id="dropoff_map" style="height: 300px;"></div>
                                <input type="text" placeholder="{{ __('transfers.admin.routes.map_search_placeholder') }}" class="form-control dropoff-search" autocomplete="off" onkeydown="return event.key !== 'Enter';">
                                <div class="g-control">
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.routes.field_lat') }}</label>
                                        <input type="text" name="dropoff_lat" class="form-control" value="{{ old('dropoff_lat', $row->dropoff_lat) }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.routes.field_lng') }}</label>
                                        <input type="text" name="dropoff_lng" class="form-control" value="{{ old('dropoff_lng', $row->dropoff_lng) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel">
                    <div class="panel-title"><strong>{{ __('transfers.admin.routes.settings_panel') }}</strong></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_status') }}</label>
                            <select name="status" class="form-control">
                                <option value="publish" @selected(old('status', $row->status ?? 'publish') === 'publish')>{{ __('transfers.admin.routes.status_publish') }}</option>
                                <option value="draft" @selected(old('status', $row->status ?? 'publish') === 'draft')>{{ __('transfers.admin.routes.status_draft') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('transfers.admin.routes.field_sort_order') }}</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $row->sort_order) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script>
        jQuery(function ($) {
            function initMap(elementId, searchSelector, latField, lngField, defaultLat, defaultLng) {
                var mapEngine = new BravoMapEngine(elementId, {
                    fitBounds: true,
                    center: [defaultLat, defaultLng],
                    zoom: 10,
                    ready: function (engineMap) {
                        var latVal = parseFloat(latField.val());
                        var lngVal = parseFloat(lngField.val());
                        if (!isNaN(latVal) && !isNaN(lngVal)) {
                            engineMap.addMarker([latVal, lngVal], { icon_options: {} });
                            engineMap.fitBounds([[latVal, lngVal]]);
                        }
                        engineMap.on('click', function (latLng) {
                            engineMap.clearMarkers();
                            engineMap.addMarker(latLng, { icon_options: {} });
                            latField.val(latLng[0]);
                            lngField.val(latLng[1]);
                        });
                        if (bookingCore.map_provider === 'gmap') {
                            engineMap.searchBox($(searchSelector), function (latLng) {
                                engineMap.clearMarkers();
                                engineMap.addMarker(latLng, { icon_options: {} });
                                latField.val(latLng[0]);
                                lngField.val(latLng[1]);
                            });
                        }
                    }
                });
                latField.on('change', function () {
                    var lat = parseFloat($(this).val());
                    var lng = parseFloat(lngField.val());
                    if (!isNaN(lat) && !isNaN(lng)) {
                        mapEngine.clearMarkers();
                        mapEngine.addMarker([lat, lng], { icon_options: {} });
                        mapEngine.fitBounds([[lat, lng]]);
                    }
                });
                lngField.on('change', function () {
                    var lat = parseFloat(latField.val());
                    var lng = parseFloat($(this).val());
                    if (!isNaN(lat) && !isNaN(lng)) {
                        mapEngine.clearMarkers();
                        mapEngine.addMarker([lat, lng], { icon_options: {} });
                        mapEngine.fitBounds([[lat, lng]]);
                    }
                });
            }

            initMap(
                'pickup_map',
                '.pickup-search',
                $('input[name="pickup_lat"]'),
                $('input[name="pickup_lng"]'),
                {{ json_encode(old('pickup_lat', $row->pickup_lat ?? setting_item('map_lat_default', 51.505))) }},
                {{ json_encode(old('pickup_lng', $row->pickup_lng ?? setting_item('map_lng_default', -0.09))) }}
            );
            initMap(
                'dropoff_map',
                '.dropoff-search',
                $('input[name="dropoff_lat"]'),
                $('input[name="dropoff_lng"]'),
                {{ json_encode(old('dropoff_lat', $row->dropoff_lat ?? setting_item('map_lat_default', 51.505))) }},
                {{ json_encode(old('dropoff_lng', $row->dropoff_lng ?? setting_item('map_lng_default', -0.09))) }}
            );
        });
    </script>
@endpush
