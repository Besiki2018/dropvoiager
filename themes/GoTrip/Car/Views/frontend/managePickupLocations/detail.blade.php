@extends('layouts.user')
@section('content')
    <div class="row y-gap-20 justify-between items-end pb-60 lg:pb-40 md:pb-32">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600">{{ $page_title ?? __('transfers.admin.pickup_locations.vendor_create') }}</h1>
            <div class="text-15 text-light-1">{{ __('transfers.admin.pickup_locations.vendor_detail_subtitle') }}</div>
        </div>
        <div class="col-auto">
            <a href="{{ route('car.vendor.pickup-locations.index') }}" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">{{ __('transfers.admin.pickup_locations.back_to_list') }}</a>
        </div>
    </div>
    @include('admin.message')
    <div class="rounded-4 bg-white shadow-3 px-30 py-30">
        <form action="{{ route('car.vendor.pickup-locations.store', ['id' => $row->id ?? 0]) }}" method="post">
            @csrf
            <div class="row y-gap-20">
                <div class="col-xl-8">
                    <div class="row y-gap-20">
                        <div class="col-md-6">
                            <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.name') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $row->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.car') }}</label>
                            <select name="car_id" class="form-control">
                                <option value="">{{ __('transfers.admin.pickup_locations.car_placeholder') }}</option>
                                @foreach($cars as $car)
                                    <option value="{{ $car->id }}" @selected((string) old('car_id', $row->car_id) === (string) $car->id)>{{ $car->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.address') }}</label>
                            <input type="text" class="form-control" name="address" value="{{ old('address', $row->address) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.place_id') }}</label>
                            <input type="text" class="form-control" name="place_id" value="{{ old('place_id', $row->place_id) }}">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-30">
                                <input class="form-check-input" type="checkbox" value="1" name="is_active" id="is_active" @checked(old('is_active', $row->is_active))>
                                <label class="form-check-label" for="is_active">{{ __('transfers.admin.pickup_locations.active_hint') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-30">
                        <h3 class="text-18 fw-600 mb-20">{{ __('transfers.admin.pickup_locations.map_section') }}</h3>
                        <div class="control-map-group">
                            <div id="pickup_vendor_map" style="min-height: 320px;"></div>
                            <input type="text" placeholder="{{ __('transfers.admin.pickup_locations.search_placeholder') }}" class="form-control mt-20 pickup-search" autocomplete="off" onkeydown="return event.key !== 'Enter';">
                            <div class="row y-gap-20 mt-20">
                                <div class="col-md-4">
                                    <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.lat') }}</label>
                                    <input type="text" class="form-control" name="lat" value="{{ old('lat', $row->lat) }}" required onkeydown="return event.key !== 'Enter';">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.lng') }}</label>
                                    <input type="text" class="form-control" name="lng" value="{{ old('lng', $row->lng) }}" required onkeydown="return event.key !== 'Enter';">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.zoom') }}</label>
                                    <input type="number" class="form-control" name="map_zoom" value="{{ old('map_zoom', $row->map_zoom ?? 10) }}" min="1" max="20" onkeydown="return event.key !== 'Enter';">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-40">
                        <h3 class="text-18 fw-600 mb-20">{{ __('transfers.admin.pickup_locations.service_section') }}</h3>
                        <div class="row y-gap-20">
                            <div class="col-md-6">
                                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.service_center_name') }}</label>
                                <input type="text" class="form-control" name="service_center_name" value="{{ old('service_center_name', $row->service_center_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.service_center_address') }}</label>
                                <input type="text" class="form-control" name="service_center_address" value="{{ old('service_center_address', $row->service_center_address) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.service_center_lat') }}</label>
                                <input type="text" class="form-control" name="service_center_lat" value="{{ old('service_center_lat', $row->service_center_lat) }}" onkeydown="return event.key !== 'Enter';">
                            </div>
                            <div class="col-md-6">
                                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.fields.service_center_lng') }}</label>
                                <input type="text" class="form-control" name="service_center_lng" value="{{ old('service_center_lng', $row->service_center_lng) }}" onkeydown="return event.key !== 'Enter';">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="border-light rounded-4 px-30 py-30">
                        <h3 class="text-18 fw-600 mb-20">{{ __('transfers.admin.pickup_locations.publish_section') }}</h3>
                        <button class="button h-50 px-24 -dark-1 bg-blue-1 text-white" type="submit">{{ __('transfers.admin.pickup_locations.save_button') }}</button>
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
            var pickupZoom = parseInt({{ json_encode(old('map_zoom', $row->map_zoom ?? 10)) }}, 10) || 10;
            var defaultLat = {{ json_encode($row->lat ?? setting_item('map_lat_default', 41.7151)) }};
            var defaultLng = {{ json_encode($row->lng ?? setting_item('map_lng_default', 44.8271)) }};

            new BravoMapEngine('pickup_vendor_map', {
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
        });
    </script>
@endpush
