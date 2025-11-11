@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ $page_title ?? __('transfers.admin.pickup_locations.title') }}</h1>
            <div class="title-actions">
                <a href="{{ route('car.admin.pickup-locations.index') }}" class="btn btn-default">{{ __('transfers.admin.pickup_locations.back_to_list') }}</a>
            </div>
        </div>
        @include('admin.message')
        <form action="{{ route('car.admin.pickup-locations.store', ['id' => $row->id ?? 0]) }}" method="post" class="needs-validation" novalidate>
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('transfers.admin.pickup_locations.general_section') }}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pickup_locations.fields.name') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $row->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pickup_locations.fields.address') }}</label>
                                <input type="text" class="form-control" name="address" value="{{ old('address', $row->address) }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pickup_locations.fields.place_id') }}</label>
                                <input type="text" class="form-control" name="place_id" value="{{ old('place_id', $row->place_id) }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pickup_locations.fields.car') }}</label>
                                <select name="car_id" class="form-control">
                                    <option value="">{{ __('transfers.admin.pickup_locations.car_placeholder') }}</option>
                                    @foreach($cars as $car)
                                        <option value="{{ $car->id }}" @selected((string) old('car_id', $row->car_id) === (string) $car->id)>{{ $car->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pickup_locations.fields.vendor') }}</label>
                                <select name="vendor_id" class="form-control">
                                    <option value="">{{ __('transfers.admin.pickup_locations.vendor_placeholder') }}</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" @selected((string) old('vendor_id', $row->vendor_id) === (string) $vendor->id)>{{ $vendor->name ?? ('#' . $vendor->id) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="d-block">{{ __('transfers.admin.pickup_locations.fields.active') }}</label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $row->is_active))>
                                    {{ __('transfers.admin.pickup_locations.active_hint') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('transfers.admin.pickup_locations.map_section') }}</strong></div>
                        <div class="panel-body">
                            <div class="control-map-group">
                                <div id="pickup_location_map" style="min-height: 320px;"></div>
                                <input type="text" placeholder="{{ __('transfers.admin.pickup_locations.search_placeholder') }}" class="form-control mt-2 pickup-search" autocomplete="off" onkeydown="return event.key !== 'Enter';">
                                <div class="g-control">
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.pickup_locations.fields.lat') }}</label>
                                        <input type="text" class="form-control" name="lat" value="{{ old('lat', $row->lat) }}" required onkeydown="return event.key !== 'Enter';">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.pickup_locations.fields.lng') }}</label>
                                        <input type="text" class="form-control" name="lng" value="{{ old('lng', $row->lng) }}" required onkeydown="return event.key !== 'Enter';">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.pickup_locations.fields.zoom') }}</label>
                                        <input type="number" class="form-control" name="map_zoom" value="{{ old('map_zoom', $row->map_zoom ?? 10) }}" min="1" max="20" onkeydown="return event.key !== 'Enter';">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('transfers.admin.pickup_locations.service_section') }}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pickup_locations.fields.service_center_name') }}</label>
                                <input type="text" class="form-control" name="service_center_name" value="{{ old('service_center_name', $row->service_center_name) }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pickup_locations.fields.service_center_address') }}</label>
                                <input type="text" class="form-control" name="service_center_address" value="{{ old('service_center_address', $row->service_center_address) }}">
                            </div>
                            <div class="control-map-group">
                                <div id="service_center_map" style="min-height: 280px;"></div>
                                <input type="text" placeholder="{{ __('transfers.admin.pickup_locations.search_placeholder') }}" class="form-control mt-2 service-search" autocomplete="off" onkeydown="return event.key !== 'Enter';">
                                <div class="g-control">
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.pickup_locations.fields.service_center_lat') }}</label>
                                        <input type="text" class="form-control" name="service_center_lat" value="{{ old('service_center_lat', $row->service_center_lat) }}" onkeydown="return event.key !== 'Enter';">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('transfers.admin.pickup_locations.fields.service_center_lng') }}</label>
                                        <input type="text" class="form-control" name="service_center_lng" value="{{ old('service_center_lng', $row->service_center_lng) }}" onkeydown="return event.key !== 'Enter';">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('transfers.admin.pickup_locations.publish_section') }}</strong></div>
                        <div class="panel-body">
                            <button class="btn btn-primary" type="submit">{{ __('transfers.admin.pickup_locations.save_button') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('js')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script>
        jQuery(function ($) {
            var pickupLat = {{ json_encode(old('lat', $row->lat)) }};
            var pickupLng = {{ json_encode(old('lng', $row->lng)) }};
            var pickupZoom = parseInt({{ json_encode(old('map_zoom', $row->map_zoom ?? 10)) }} , 10) || 10;
            var defaultLat = {{ json_encode($row->lat ?? setting_item('map_lat_default', 41.7151)) }};
            var defaultLng = {{ json_encode($row->lng ?? setting_item('map_lng_default', 44.8271)) }};

            new BravoMapEngine('pickup_location_map', {
                disableScripts: true,
                fitBounds: true,
                center: [pickupLat !== null ? pickupLat : defaultLat, pickupLng !== null ? pickupLng : defaultLng],
                zoom: pickupZoom,
                ready: function (engineMap) {
                    if (pickupLat !== null && pickupLng !== null) {
                        engineMap.addMarker([pickupLat, pickupLng], {icon_options: {}});
                    }
                    engineMap.on('click', function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {icon_options: {}});
                        $("input[name=lat]").val(dataLatLng[0]);
                        $("input[name=lng]").val(dataLatLng[1]);
                    });
                    engineMap.on('zoom_changed', function (zoom) {
                        $("input[name=map_zoom]").val(zoom);
                    });
                    engineMap.searchBox($('.pickup-search'), function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {icon_options: {}});
                        $("input[name=lat]").val(dataLatLng[0]);
                        $("input[name=lng]").val(dataLatLng[1]);
                    });
                }
            });

            var serviceLat = {{ json_encode(old('service_center_lat', $row->service_center_lat)) }};
            var serviceLng = {{ json_encode(old('service_center_lng', $row->service_center_lng)) }};
            if ($('#service_center_map').length) {
                new BravoMapEngine('service_center_map', {
                    disableScripts: true,
                    fitBounds: true,
                    center: [serviceLat !== null ? serviceLat : defaultLat, serviceLng !== null ? serviceLng : defaultLng],
                    zoom: pickupZoom,
                    ready: function (engineMap) {
                        if (serviceLat !== null && serviceLng !== null) {
                            engineMap.addMarker([serviceLat, serviceLng], {icon_options: {}});
                        }
                        engineMap.on('click', function (dataLatLng) {
                            engineMap.clearMarkers();
                            engineMap.addMarker(dataLatLng, {icon_options: {}});
                            $("input[name=service_center_lat]").val(dataLatLng[0]);
                            $("input[name=service_center_lng]").val(dataLatLng[1]);
                        });
                        engineMap.searchBox($('.service-search'), function (dataLatLng) {
                            engineMap.clearMarkers();
                            engineMap.addMarker(dataLatLng, {icon_options: {}});
                            $("input[name=service_center_lat]").val(dataLatLng[0]);
                            $("input[name=service_center_lng]").val(dataLatLng[1]);
                        });
                    }
                });
            }
        });
    </script>
@endpush
