@php $style = $style ?? 'default';
    $classes = ' form-search-all-service mainSearch bg-white px-10 py-10 lg:px-20 lg:pt-5 lg:pb-20 rounded-4 mt-30';
    $button_classes = " -dark-1 py-15 col-12 bg-blue-1 text-white w-100 rounded-4";
    if($style == 'sidebar'){
        $classes = ' form-search-sidebar';
        $button_classes = " -dark-1 py-15 col-12 bg-blue-1 h-60 text-white w-100 rounded-4";
    }
    if($style == 'normal'){
        $classes = ' px-10 py-10 lg:px-20 lg:pt-5 lg:pb-20 rounded-100 form-search-all-service mainSearch -w-900 bg-white';
        $button_classes = " -dark-1 py-15 h-60 col-12 rounded-100 bg-blue-1 text-white w-100";
    }
    if($style == 'normal2'){
        $classes = 'mainSearch bg-white pr-20 py-20 lg:px-20 lg:pt-5 lg:pb-20 rounded-4 shadow-1';
        $button_classes = " -dark-1 py-15 h-60 col-12 rounded-100 bg-blue-1 text-white w-100";
    }
    if($style == 'carousel_v2'){
        $classes = " w-100";
        $button_classes = " -dark-1 py-15 px-35 h-60 col-12 rounded-4 bg-yellow-1 text-dark-1";
    }
    if($style == 'map'){
        $classes = " w-100";
        $button_classes = " -dark-1 size-60 col-12 rounded-4 bg-blue-1 text-white";
    }
    if($style == 'car_carousel'){
        $classes = " mainSearch -col-5 -w-1070 mx-auto bg-white pr-20 py-20 lg:px-20 lg:pt-5 lg:pb-20 rounded-4 shadow-1";
        $button_classes = " -dark-1 py-15 px-35 h-60 col-12 rounded-4 bg-dark-1 text-white";
    }
@endphp

@php
    $pickupLocations = collect($pickup_locations ?? $pickupLocations ?? []);
    $selectedPickupLocation = $selected_pickup_location ?? null;
    $selectedPickupLocationId = $selected_pickup_location_id ?? ($selectedPickupLocation->id ?? request()->input('pickup_location_id'));
    if (!$selectedPickupLocation && $selectedPickupLocationId) {
        $selectedPickupLocation = $pickupLocations->firstWhere('id', (int) $selectedPickupLocationId);
    }
    $dropoffData = $selected_dropoff ?? request()->input('dropoff', []);
    $selectedPickupPayload = $selected_pickup_payload ?? ($selectedPickupLocation ? $selectedPickupLocation->toFrontendArray() : null);
    $transferDatetime = request()->input('transfer_datetime');
    $transferDate = '';
    $transferTime = '';
    if($transferDatetime){
        try {
            $transferCarbon = \Carbon\Carbon::parse($transferDatetime, 'Asia/Tbilisi')->setTimezone('Asia/Tbilisi');
            $transferDate = $transferCarbon->toDateString();
            $transferTime = $transferCarbon->format('H:i');
        } catch (\Exception $exception) {
            $transferDate = '';
            $transferTime = '';
        }
    }
@endphp

<form action="{{ route("car.search") }}" class="gotrip_form_search bravo_form_search bravo_form form-search-all-service form js-transfer-form {{$classes }}" method="get">
    @if( !empty(Request::query('_layout')) )
        <input type="hidden" name="_layout" value="{{Request::query('_layout')}}">
    @endif
    @php $search_style = setting_item('car_location_search_style','normal');
         $car_search_fields = setting_item_array('car_search_fields');
         $car_search_fields = array_values(array_filter($car_search_fields, function ($field) {
             return ($field['field'] ?? '') !== 'location' && ($field['field'] ?? '') !== 'date';
         }));
            $space_search_fields = array_values(\Illuminate\Support\Arr::sort($car_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));
    @endphp
    <div class="field-items">
        <div class="row w-100 m-0">
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-loc item">
                    <div>
                        <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.from_label') }}</h4>
                        <div class="text-15 text-light-1 ls-2 lh-16">
                            <select name="pickup_location_id" class="form-control js-transfer-pickup" @if($pickupLocations->isEmpty()) disabled @endif>
                                <option value="">{{ __('transfers.form.select_pickup_option') }}</option>
                                <option value="__mylocation__" data-source="mylocation">{{ __('transfers.form.use_my_location') }}</option>
                                @foreach($pickupLocations as $location)
                                    @php
                                        $payload = $location->toFrontendArray();
                                        $label = $location->name;
                                        if (!empty($location->car?->title)) {
                                            $label .= ' — ' . $location->car->title;
                                        }
                                    @endphp
                                    <option value="{{ $location->id }}" data-source="backend" data-payload='@json($payload)' @if($location->id == $selectedPickupLocationId) selected @endif>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($pickupLocations->isEmpty())
                                <small class="text-danger d-block mt-2">{{ __('transfers.form.no_pickups_available') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-loc item">
                    <div>
                        <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.to_label') }}</h4>
                        <div class="text-15 text-light-1 ls-2 lh-16">
                            <input type="text" class="form-control js-transfer-dropoff-display" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}" placeholder="{{ __('transfers.form.to_placeholder') }}">
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="dropoff[address]" class="js-transfer-dropoff-address" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}">
            <input type="hidden" name="dropoff[name]" class="js-transfer-dropoff-name" value="{{ $dropoffData['name'] ?? $dropoffData['address'] ?? '' }}">
            <input type="hidden" name="dropoff[lat]" class="js-transfer-dropoff-lat" value="{{ $dropoffData['lat'] ?? '' }}">
            <input type="hidden" name="dropoff[lng]" class="js-transfer-dropoff-lng" value="{{ $dropoffData['lng'] ?? '' }}">
            <input type="hidden" class="js-transfer-pickup-payload" value='@json($selectedPickupPayload)'>
            <input type="hidden" name="pickup" class="js-transfer-pickup-json" value='@json($selectedPickupPayload)'>
            <input type="hidden" name="dropoff" class="js-transfer-dropoff-json" value='@json($dropoffData)'>

            <div class="col-12 px-30 lg:py-20 lg:px-0">
                <div class="transfer-map-wrapper js-transfer-map-wrapper mt-10 rounded-4 overflow-hidden" style="display: none;">
                    <div class="js-transfer-map" style="height: 260px;"></div>
                </div>
            </div>

            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-date item">
                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Date') }}</h4>
                    <div class="text-15 text-light-1 ls-2 lh-16">
                        <input type="date" name="transfer_date" class="form-control js-transfer-date" value="{{ $transferDate }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-date item">
                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Time') }}</h4>
                    <div class="text-15 text-light-1 ls-2 lh-16">
                        <input type="time" name="transfer_time" class="form-control js-transfer-time" value="{{ $transferTime }}">
                    </div>
                </div>
            </div>
            <input type="hidden" name="transfer_datetime" class="js-transfer-datetime" value="{{ $transferDatetime }}">
            <input type="hidden" class="js-transfer-pickup-payload" value='@json($selectedPickupPayload)'>
            @if(!empty($car_search_fields))
                @foreach($car_search_fields as $field)
                    <div class="col-lg-{{ $field['size'] ?? "6" }} align-self-center px-30 lg:py-20 lg:px-0">
                        @php $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "" @endphp
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Layout::common.search.fields.service_name')
                                @break
                            @case ('location')
                                @include('Layout::common.search.fields.location')
                                @break
                            @case ('date')
                                @include('Layout::common.search.fields.date')
                                @break
                            @case ('attr')
                                @include('Layout::common.search.fields.attr')
                                @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="button-item">
        <button class="mainSearch__submit button {{ $button_classes }}" type="submit">
            <i class="icon-search text-20 mr-10"></i>
            <span class="text-search">{{__("Search")}}</span>
        </button>
    </div>
</form>
@push('js')
    @once('transfer-form-script')
        <script>
            jQuery(function ($) {
                var timezoneOffset = '{{ \Carbon\Carbon::now('Asia/Tbilisi')->format('P') }}';
                var myLocationLabel = '{{ __('transfers.form.my_location_label') }}';
                var locationErrorMessage = '{{ __('transfers.form.location_error') }}';
                var geolocationUnsupported = '{{ __('transfers.form.location_unsupported') }}';
                var dropoffRequiredMessage = '{{ __('transfers.form.dropoff_coordinates_required') }}';
                var defaultMapLat = Number('{{ (float) setting_item('map_lat_default', 41.715133) }}') || 0;
                var defaultMapLng = Number('{{ (float) setting_item('map_lng_default', 44.827096) }}') || 0;

                function parseJsonValue(value) {
                    if (!value) {
                        return null;
                    }
                    try {
                        return JSON.parse(value);
                    } catch (e) {
                        return null;
                    }
                }

                function serialisePayload(payload) {
                    return payload ? JSON.stringify(payload) : '';
                }

                function initTransferForm($form) {
                    var pickupSelect = $form.find('.js-transfer-pickup');
                    var pickupJsonInput = $form.find('.js-transfer-pickup-json');
                    var pickupPayloadHolder = $form.find('.js-transfer-pickup-payload');
                    var dropoffJsonInput = $form.find('.js-transfer-dropoff-json');
                    var dropoffDisplay = $form.find('.js-transfer-dropoff-display');
                    var dropoffAddress = $form.find('.js-transfer-dropoff-address');
                    var dropoffName = $form.find('.js-transfer-dropoff-name');
                    var dropoffLat = $form.find('.js-transfer-dropoff-lat');
                    var dropoffLng = $form.find('.js-transfer-dropoff-lng');
                    var dateInput = $form.find('.js-transfer-date');
                    var timeInput = $form.find('.js-transfer-time');
                    var datetimeInput = $form.find('.js-transfer-datetime');
                    var geocodeInProgress = false;
                    var mapWrapper = $form.find('.js-transfer-map-wrapper');
                    var mapCanvas = mapWrapper.find('.js-transfer-map');
                    var mapInstance = null;
                    var pickupMarker = null;
                    var dropoffMarker = null;

                    if (mapWrapper.length) {
                        mapWrapper.hide();
                    }

                    function ensureMapInstance() {
                        if (!mapCanvas.length) {
                            return null;
                        }
                        if (typeof google === 'undefined' || !google.maps) {
                            return null;
                        }
                        if (!mapInstance) {
                            mapInstance = new google.maps.Map(mapCanvas[0], {
                                zoom: 12,
                                center: {
                                    lat: defaultMapLat || 0,
                                    lng: defaultMapLng || 0
                                },
                                streetViewControl: false,
                                mapTypeControl: false,
                            });
                        }

                        return mapInstance;
                    }

                    function updateMapMarkers() {
                        if (!mapWrapper.length) {
                            return;
                        }

                        var pickupPayload = parseJsonValue(pickupJsonInput.val());
                        var dropoffPayload = parseJsonValue(dropoffJsonInput.val());
                        var hasPickup = pickupPayload && pickupPayload.lat && pickupPayload.lng;
                        var hasDropoff = dropoffPayload && dropoffPayload.lat && dropoffPayload.lng;

                        if (!hasPickup && !hasDropoff) {
                            mapWrapper.hide();
                            if (pickupMarker) {
                                pickupMarker.setMap(null);
                                pickupMarker = null;
                            }
                            if (dropoffMarker) {
                                dropoffMarker.setMap(null);
                                dropoffMarker = null;
                            }
                            return;
                        }

                        if (typeof google === 'undefined' || !google.maps) {
                            return;
                        }

                        mapWrapper.show();
                        var map = ensureMapInstance();
                        if (!map) {
                            return;
                        }

                        google.maps.event.trigger(map, 'resize');

                        var bounds = new google.maps.LatLngBounds();

                        if (hasPickup) {
                            var pickupLatLng = new google.maps.LatLng(parseFloat(pickupPayload.lat), parseFloat(pickupPayload.lng));
                            if (!pickupMarker) {
                                pickupMarker = new google.maps.Marker({
                                    map: map,
                                    position: pickupLatLng,
                                    label: {text: 'A', fontWeight: 'bold'},
                                });
                            } else {
                                pickupMarker.setPosition(pickupLatLng);
                                pickupMarker.setMap(map);
                            }
                            bounds.extend(pickupLatLng);
                        } else if (pickupMarker) {
                            pickupMarker.setMap(null);
                            pickupMarker = null;
                        }

                        if (hasDropoff) {
                            var dropoffLatLng = new google.maps.LatLng(parseFloat(dropoffPayload.lat), parseFloat(dropoffPayload.lng));
                            if (!dropoffMarker) {
                                dropoffMarker = new google.maps.Marker({
                                    map: map,
                                    position: dropoffLatLng,
                                    label: {text: 'B', fontWeight: 'bold'},
                                });
                            } else {
                                dropoffMarker.setPosition(dropoffLatLng);
                                dropoffMarker.setMap(map);
                            }
                            bounds.extend(dropoffLatLng);
                        } else if (dropoffMarker) {
                            dropoffMarker.setMap(null);
                            dropoffMarker = null;
                        }

                        if (hasPickup && hasDropoff) {
                            map.fitBounds(bounds);
                        } else if (hasPickup && pickupMarker) {
                            map.setCenter(pickupMarker.getPosition());
                            map.setZoom(13);
                        } else if (hasDropoff && dropoffMarker) {
                            map.setCenter(dropoffMarker.getPosition());
                            map.setZoom(13);
                        }
                    }

                    function emitTransferUpdate() {
                        var context = {
                            pickup: parseJsonValue(pickupJsonInput.val()),
                            dropoff: parseJsonValue(dropoffJsonInput.val())
                        };
                        $form.trigger('transfer:context-changed', context);
                    }

                    function setPickupPayload(payload, options) {
                        options = options || {};
                        pickupJsonInput.val(serialisePayload(payload));
                        pickupPayloadHolder.val(serialisePayload(payload));
                        if (!options.silent) {
                            if (payload && payload.source === 'mylocation') {
                                pickupSelect.val('__mylocation__');
                            } else if (payload && payload.id) {
                                pickupSelect.val(payload.id);
                            } else {
                                pickupSelect.val('');
                            }
                        }
                        updateMapMarkers();
                        emitTransferUpdate();
                    }

                    function setDropoffPayload(payload) {
                        dropoffJsonInput.val(serialisePayload(payload));
                        dropoffAddress.val(payload && (payload.address || payload.name) ? (payload.address || payload.name) : '');
                        dropoffName.val(payload && payload.name ? payload.name : '');
                        dropoffLat.val(payload && payload.lat ? payload.lat : '');
                        dropoffLng.val(payload && payload.lng ? payload.lng : '');
                        if (payload && (payload.address || payload.name)) {
                            dropoffDisplay.val(payload.address || payload.name);
                        }
                        updateMapMarkers();
                        emitTransferUpdate();
                    }

                    function updateDatetimeValue() {
                        if (!datetimeInput.length) {
                            return;
                        }
                        var date = dateInput.val();
                        var time = timeInput.val();
                        if (date && time) {
                            datetimeInput.val(date + 'T' + time + ':00' + timezoneOffset);
                        } else {
                            datetimeInput.val('');
                        }
                    }

                    dateInput.on('change', updateDatetimeValue);
                    timeInput.on('change', updateDatetimeValue);
                    updateDatetimeValue();

                    function requestGeolocation() {
                        if (!navigator.geolocation) {
                            alert(geolocationUnsupported);
                            setPickupPayload(null);
                            pickupSelect.val('');
                            return;
                        }
                        navigator.geolocation.getCurrentPosition(function (position) {
                            setPickupPayload({
                                id: null,
                                name: myLocationLabel,
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                                source: 'mylocation'
                            }, {silent: true});
                        }, function () {
                            alert(locationErrorMessage);
                            pickupSelect.val('');
                            setPickupPayload(null, {silent: true});
                        }, {
                            enableHighAccuracy: true,
                            maximumAge: 60000,
                        });
                    }

                    pickupSelect.on('change', function () {
                        var selected = $(this).find('option:selected');
                        var value = $(this).val();
                        if (!value) {
                            setPickupPayload(null, {silent: true});
                            return;
                        }
                        if (value === '__mylocation__') {
                            requestGeolocation();
                            return;
                        }
                        var payload = selected.data('payload') || null;
                        if (payload) {
                            payload.source = 'backend';
                            setPickupPayload(payload, {silent: true});
                        }
                    });

                    function ensurePickupSelection() {
                        var payload = parseJsonValue(pickupJsonInput.val());
                        if (payload) {
                            if (payload.source === 'mylocation') {
                                pickupSelect.val('__mylocation__');
                            } else if (payload.id) {
                                pickupSelect.val(payload.id);
                            }
                        }
                    }

                    ensurePickupSelection();
                    emitTransferUpdate();
                    updateMapMarkers();

                    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                        var autocomplete = new google.maps.places.Autocomplete(dropoffDisplay[0], {
                            fields: ['formatted_address', 'geometry', 'name']
                        });
                        autocomplete.addListener('place_changed', function () {
                            var place = autocomplete.getPlace();
                            if (!place || !place.geometry || !place.geometry.location) {
                                return;
                            }
                            setDropoffPayload({
                                address: place.formatted_address || place.name || '',
                                name: place.name || place.formatted_address || '',
                                lat: place.geometry.location.lat(),
                                lng: place.geometry.location.lng()
                            });
                        });
                    }

                    if ($form.is('form')) {
                        $form.on('submit', function (event) {
                            if (geocodeInProgress) {
                                event.preventDefault();
                                return false;
                            }

                            updateDatetimeValue();

                            var dropoffPayload = parseJsonValue(dropoffJsonInput.val());
                            if (!dropoffPayload || !dropoffPayload.lat || !dropoffPayload.lng) {
                                if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                                    event.preventDefault();
                                    geocodeInProgress = true;
                                    var geocoder = new google.maps.Geocoder();
                                    geocoder.geocode({address: dropoffDisplay.val()}, function (results, status) {
                                        geocodeInProgress = false;
                                        if (status === 'OK' && results[0] && results[0].geometry && results[0].geometry.location) {
                                            var result = results[0];
                                            setDropoffPayload({
                                                address: result.formatted_address || dropoffDisplay.val(),
                                                name: result.address_components && result.address_components[0] ? result.address_components[0].short_name : dropoffDisplay.val(),
                                                lat: result.geometry.location.lat(),
                                                lng: result.geometry.location.lng()
                                            });
                                            updateDatetimeValue();
                                            $form.trigger('submit');
                                        } else {
                                            alert(dropoffRequiredMessage);
                                        }
                                    });
                                    return false;
                                }
                                alert(dropoffRequiredMessage);
                                event.preventDefault();
                                return false;
                            }

                            var pickupPayload = parseJsonValue(pickupJsonInput.val());
                            if (!pickupPayload) {
                                var selectedOption = pickupSelect.find('option:selected');
                                var selectedValue = pickupSelect.val();
                                if (selectedValue === '__mylocation__') {
                                    alert(locationErrorMessage);
                                    event.preventDefault();
                                    return false;
                                }
                                if (selectedOption.length && selectedOption.data('payload')) {
                                    var payload = selectedOption.data('payload');
                                    payload.source = 'backend';
                                    setPickupPayload(payload, {silent: true});
                                }
                            }

                            return true;
                        });
                    }

                    var initialDropoff = parseJsonValue(dropoffJsonInput.val());
                    if (initialDropoff) {
                        setDropoffPayload(initialDropoff);
                    }
                }

                $('.js-transfer-form').each(function () {
                    initTransferForm($(this));
                });
            });
        </script>
    @endonce
@endpush
