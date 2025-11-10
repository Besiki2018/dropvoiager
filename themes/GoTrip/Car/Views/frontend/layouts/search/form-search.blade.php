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
                            <select name="pickup_location_id"
                                    class="form-control js-transfer-pickup"
                                    data-fetch-url="{{ route('car.pickup_locations') }}"
                                    data-default-label="{{ __('transfers.form.select_pickup_option') }}"
                                    data-my-location-label="{{ __('transfers.form.use_my_location') }}"
                                    @if($pickupLocations->isEmpty()) disabled @endif>
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
                            <small class="text-danger d-block mt-2 js-pickup-empty-message" @if(!$pickupLocations->isEmpty()) style="display:none;" @endif>{{ __('transfers.form.no_pickups_available') }}</small>
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

            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-date item">
                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.date_label') }}</h4>
                    <div class="text-15 text-light-1 ls-2 lh-16">
                        <input type="date" name="transfer_date" class="form-control js-transfer-date" value="{{ $transferDate }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-date item">
                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.time_label') }}</h4>
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
            <span class="text-search">{{ __('transfers.form.search_button') }}</span>
        </button>
    </div>
</form>
@push('js')
    @once('transfer-form-script')
        <script>
            (function (window, $) {
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

                function schedule(fn, delay) {
                    return window.setTimeout(fn, delay || 0);
                }

                function initTransferForm($form) {
                    if (!$form.length || $form.data('transfer-init')) {
                        return;
                    }
                    $form.data('transfer-init', true);

                    var pickupSelect = $form.find('.js-transfer-pickup');
                    if (!pickupSelect.length) {
                        return;
                    }

                    var pickupJsonInput = $form.find('.js-transfer-pickup-json').first();
                    var pickupPayloadHolder = $form.find('.js-transfer-pickup-payload').first();
                    var dropoffJsonInput = $form.find('.js-transfer-dropoff-json').first();
                    var dropoffDisplay = $form.find('.js-transfer-dropoff-display').first();
                    var dropoffAddress = $form.find('.js-transfer-dropoff-address').first();
                    var dropoffName = $form.find('.js-transfer-dropoff-name').first();
                    var dropoffLat = $form.find('.js-transfer-dropoff-lat').first();
                    var dropoffLng = $form.find('.js-transfer-dropoff-lng').first();
                    var dateInput = $form.find('.js-transfer-date').first();
                    var timeInput = $form.find('.js-transfer-time').first();
                    var datetimeInput = $form.find('.js-transfer-datetime').first();
                    var mapWrapper = $form.find('.js-transfer-map-wrapper').first();
                    var mapCanvas = mapWrapper.find('.js-transfer-map').first();
                    var emptyMessage = $form.find('.js-pickup-empty-message').first();

                    var geocodeInProgress = false;
                    var mapState = {
                        map: null,
                        pickupMarker: null,
                        dropoffMarker: null,
                        retryTimer: null
                    };

                    var fetchUrl = pickupSelect.data('fetch-url');
                    var defaultOptionLabel = pickupSelect.data('default-label') || pickupSelect.find('option').first().text() || '';
                    var myLocationOptionLabel = pickupSelect.data('my-location-label') || pickupSelect.find('option[value="__mylocation__"]').text() || myLocationLabel;
                    var pickupFetchLoaded = false;

                    if (mapWrapper.length) {
                        mapWrapper.hide();
                    }

                    function setPickupEmptyState(isEmpty) {
                        if (!emptyMessage.length) {
                            return;
                        }
                        if (isEmpty) {
                            emptyMessage.show();
                        } else {
                            emptyMessage.hide();
                        }
                    }

                    function ensureMapInstance() {
                        if (!mapWrapper.length || !mapCanvas.length) {
                            return null;
                        }
                        if (mapState.map) {
                            return mapState.map;
                        }
                        if (typeof google === 'undefined' || !google.maps) {
                            if (!mapState.retryTimer) {
                                mapState.retryTimer = schedule(function () {
                                    mapState.retryTimer = null;
                                    updateMapMarkers();
                                }, 400);
                            }
                            return null;
                        }
                        mapState.map = new google.maps.Map(mapCanvas[0], {
                            zoom: 12,
                            center: {
                                lat: defaultMapLat || 0,
                                lng: defaultMapLng || 0
                            },
                            streetViewControl: false,
                            mapTypeControl: false,
                        });
                        return mapState.map;
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
                            if (mapState.pickupMarker) {
                                mapState.pickupMarker.setMap(null);
                                mapState.pickupMarker = null;
                            }
                            if (mapState.dropoffMarker) {
                                mapState.dropoffMarker.setMap(null);
                                mapState.dropoffMarker = null;
                            }
                            return;
                        }

                        if (typeof google === 'undefined' || !google.maps) {
                            updateMapMarkersLater();
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
                            if (!mapState.pickupMarker) {
                                mapState.pickupMarker = new google.maps.Marker({
                                    map: map,
                                    position: pickupLatLng,
                                    label: {text: 'A', fontWeight: 'bold'},
                                });
                            } else {
                                mapState.pickupMarker.setMap(map);
                                mapState.pickupMarker.setPosition(pickupLatLng);
                            }
                            bounds.extend(pickupLatLng);
                        } else if (mapState.pickupMarker) {
                            mapState.pickupMarker.setMap(null);
                            mapState.pickupMarker = null;
                        }

                        if (hasDropoff) {
                            var dropoffLatLng = new google.maps.LatLng(parseFloat(dropoffPayload.lat), parseFloat(dropoffPayload.lng));
                            if (!mapState.dropoffMarker) {
                                mapState.dropoffMarker = new google.maps.Marker({
                                    map: map,
                                    position: dropoffLatLng,
                                    label: {text: 'B', fontWeight: 'bold'},
                                });
                            } else {
                                mapState.dropoffMarker.setMap(map);
                                mapState.dropoffMarker.setPosition(dropoffLatLng);
                            }
                            bounds.extend(dropoffLatLng);
                        } else if (mapState.dropoffMarker) {
                            mapState.dropoffMarker.setMap(null);
                            mapState.dropoffMarker = null;
                        }

                        if (hasPickup && hasDropoff) {
                            map.fitBounds(bounds);
                        } else if (hasPickup && mapState.pickupMarker) {
                            map.setCenter(mapState.pickupMarker.getPosition());
                            map.setZoom(13);
                        } else if (hasDropoff && mapState.dropoffMarker) {
                            map.setCenter(mapState.dropoffMarker.getPosition());
                            map.setZoom(13);
                        }
                    }

                    function updateMapMarkersLater() {
                        if (mapState.retryTimer) {
                            return;
                        }
                        mapState.retryTimer = schedule(function () {
                            mapState.retryTimer = null;
                            updateMapMarkers();
                        }, 400);
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
                        var serialised = serialisePayload(payload);
                        if (pickupJsonInput.length) {
                            pickupJsonInput.val(serialised);
                        }
                        if (pickupPayloadHolder.length) {
                            pickupPayloadHolder.val(serialised);
                        }
                        if (!options.silent) {
                            if (payload && payload.source === 'mylocation') {
                                pickupSelect.val('__mylocation__');
                            } else if (payload && payload.id) {
                                pickupSelect.val(String(payload.id));
                            } else {
                                pickupSelect.val('');
                            }
                        }
                        updateMapMarkers();
                        emitTransferUpdate();
                    }

                    function setDropoffPayload(payload) {
                        var serialised = serialisePayload(payload);
                        if (dropoffJsonInput.length) {
                            dropoffJsonInput.val(serialised);
                        }
                        if (dropoffAddress.length) {
                            dropoffAddress.val(payload && (payload.address || payload.name) ? (payload.address || payload.name) : '');
                        }
                        if (dropoffName.length) {
                            dropoffName.val(payload && payload.name ? payload.name : '');
                        }
                        if (dropoffLat.length) {
                            dropoffLat.val(payload && payload.lat ? payload.lat : '');
                        }
                        if (dropoffLng.length) {
                            dropoffLng.val(payload && payload.lng ? payload.lng : '');
                        }
                        if (dropoffDisplay.length && payload && (payload.address || payload.name)) {
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

                    if (dateInput.length) {
                        dateInput.on('change', updateDatetimeValue);
                    }
                    if (timeInput.length) {
                        timeInput.on('change', updateDatetimeValue);
                    }
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

                    function getOptionPayload($option) {
                        if (!$option || !$option.length) {
                            return null;
                        }
                        var payload = $option.data('payload');
                        if (payload) {
                            return payload;
                        }
                        var attrPayload = $option.attr('data-payload');
                        if (attrPayload) {
                            try {
                                return JSON.parse(attrPayload);
                            } catch (e) {}
                        }
                        return null;
                    }

                    pickupSelect.on('change', function () {
                        var selectedValue = $(this).val();
                        if (!selectedValue) {
                            setPickupPayload(null, {silent: true});
                            return;
                        }
                        if (selectedValue === '__mylocation__') {
                            requestGeolocation();
                            return;
                        }
                        var selectedOption = $(this).find('option:selected');
                        var payload = getOptionPayload(selectedOption);
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
                                pickupSelect.val(String(payload.id));
                            }
                            setPickupPayload(payload, {silent: true});
                            return;
                        }

                        var selectedOption = pickupSelect.find('option:selected');
                        if (selectedOption.length) {
                            var optionPayload = getOptionPayload(selectedOption);
                            if (optionPayload) {
                                optionPayload.source = optionPayload.source || selectedOption.data('source') || 'backend';
                                setPickupPayload(optionPayload, {silent: true});
                            }
                        }
                    }

                    function buildOption(text, value, attributes, payload) {
                        var option = document.createElement('option');
                        option.value = value;
                        option.textContent = text;
                        if (attributes) {
                            Object.keys(attributes).forEach(function (key) {
                                option.setAttribute(key, attributes[key]);
                            });
                        }
                        if (payload) {
                            option.setAttribute('data-payload', JSON.stringify(payload));
                            $(option).data('payload', payload);
                        }
                        return option;
                    }

                    function renderPickupOptions(locations) {
                        var fragment = document.createDocumentFragment();
                        fragment.appendChild(buildOption(defaultOptionLabel, '', null));
                        fragment.appendChild(buildOption(myLocationOptionLabel, '__mylocation__', {'data-source': 'mylocation'}));

                        var hasLocations = Array.isArray(locations) && locations.length;
                        if (hasLocations) {
                            locations.forEach(function (location) {
                                if (!location) {
                                    return;
                                }
                                var label = location.label || location.name || '';
                                if (!location.label && location.car_title) {
                                    label = location.name + ' — ' + location.car_title;
                                }
                                fragment.appendChild(buildOption(label, String(location.id), {'data-source': 'backend'}, location));
                            });
                        }

                        var previousValue = pickupSelect.val();
                        pickupSelect.empty().append(fragment);

                        if (previousValue && pickupSelect.find('option[value="' + previousValue + '"]').length) {
                            pickupSelect.val(previousValue);
                        }

                        setPickupEmptyState(!hasLocations);
                        pickupSelect.prop('disabled', !hasLocations && !pickupSelect.find('option[value]').length);
                        ensurePickupSelection();
                    }

                    function fetchPickupLocations() {
                        if (!fetchUrl || pickupFetchLoaded) {
                            return;
                        }
                        pickupFetchLoaded = true;
                        pickupSelect.prop('disabled', true);
                        $.ajax({
                            url: fetchUrl,
                            method: 'GET',
                            dataType: 'json'
                        }).done(function (response) {
                            var data = response && response.data ? response.data : [];
                            renderPickupOptions(data);
                            pickupSelect.prop('disabled', false);
                        }).fail(function () {
                            setPickupEmptyState(true);
                            pickupSelect.prop('disabled', false);
                        });
                    }

                    function setupDropoffAutocomplete() {
                        if (!dropoffDisplay.length || dropoffDisplay.data('autocomplete-bound')) {
                            return;
                        }
                        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                            schedule(setupDropoffAutocomplete, 400);
                            return;
                        }
                        dropoffDisplay.data('autocomplete-bound', true);
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
                                var selectedValue = pickupSelect.val();
                                if (selectedValue === '__mylocation__') {
                                    alert(locationErrorMessage);
                                    event.preventDefault();
                                    return false;
                                }
                                var selectedOption = pickupSelect.find('option:selected');
                                if (selectedOption.length) {
                                    var payload = getOptionPayload(selectedOption);
                                    if (payload) {
                                        payload.source = 'backend';
                                        setPickupPayload(payload, {silent: true});
                                    }
                                }
                            }

                            return true;
                        });
                    }

                    setupDropoffAutocomplete();
                    fetchPickupLocations();
                    ensurePickupSelection();
                    emitTransferUpdate();
                    updateMapMarkers();

                    var initialDropoff = parseJsonValue(dropoffJsonInput.val());
                    if (initialDropoff) {
                        setDropoffPayload(initialDropoff);
                    }
                }

                function initAll(context) {
                    var $context = context ? $(context) : $(document);
                    $context.find('.js-transfer-form').each(function () {
                        initTransferForm($(this));
                    });
                }

                window.BravoTransferForm = window.BravoTransferForm || {};
                window.BravoTransferForm.initForm = initTransferForm;
                window.BravoTransferForm.initAll = initAll;

                $(function () {
                    initAll(document);
                });
            })(window, jQuery);
        </script>
    @endonce
@endpush
