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
                                    @if($pickupLocations->isEmpty()) disabled @endif>
                                <option value="">{{ __('transfers.form.select_pickup_option') }}</option>
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-loc item">
                    <div>
                        <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.to_label') }}</h4>
                        <div class="text-15 text-light-1 ls-2 lh-16">
                            <input type="text" class="form-control js-transfer-dropoff-display" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}" placeholder="{{ __('transfers.form.to_placeholder') }}" minlength="3" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="dropoff[address]" class="js-transfer-dropoff-address" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}">
            <input type="hidden" name="dropoff[name]" class="js-transfer-dropoff-name" value="{{ $dropoffData['name'] ?? $dropoffData['address'] ?? '' }}">
            <input type="hidden" name="dropoff[lat]" class="js-transfer-dropoff-lat" value="{{ $dropoffData['lat'] ?? '' }}">
            <input type="hidden" name="dropoff[lng]" class="js-transfer-dropoff-lng" value="{{ $dropoffData['lng'] ?? '' }}">
            <input type="hidden" name="dropoff[place_id]" class="js-transfer-dropoff-place-id" value="{{ $dropoffData['place_id'] ?? '' }}">
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
                var dropoffRequiredMessage = '{{ __('transfers.form.dropoff_coordinates_required') }}';

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
                    var dropoffPlaceId = $form.find('.js-transfer-dropoff-place-id').first();
                    var dateInput = $form.find('.js-transfer-date').first();
                    var timeInput = $form.find('.js-transfer-time').first();
                    var datetimeInput = $form.find('.js-transfer-datetime').first();

                    var fetchUrl = pickupSelect.data('fetch-url');
                    var defaultOptionLabel = pickupSelect.data('default-label') || pickupSelect.find('option').first().text() || '';
                    var pickupFetchLoaded = false;
                    var suppressDropoffInput = false;

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
                            if (payload && payload.id) {
                                pickupSelect.val(String(payload.id));
                            } else {
                                pickupSelect.val('');
                            }
                        }
                        emitTransferUpdate();
                    }

                    function setDropoffDisplayValue(value) {
                        if (!dropoffDisplay.length) {
                            return;
                        }
                        suppressDropoffInput = true;
                        dropoffDisplay.val(value || '');
                        suppressDropoffInput = false;
                    }

                    function setDropoffPayload(payload, options) {
                        options = options || {};
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
                        if (dropoffPlaceId.length) {
                            dropoffPlaceId.val(payload && payload.place_id ? payload.place_id : '');
                        }
                        if (!options.preserveDisplay) {
                            var displayValue = payload && (payload.address || payload.name) ? (payload.address || payload.name) : '';
                            setDropoffDisplayValue(displayValue);
                        }
                        dropoffDisplay.each(function () {
                            this.setCustomValidity('');
                        });
                        emitTransferUpdate();
                    }

                    function clearDropoffPayload() {
                        setDropoffPayload(null, {preserveDisplay: true});
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
                        var selectedOption = $(this).find('option:selected');
                        var payload = getOptionPayload(selectedOption);
                        if (payload) {
                            payload.source = 'backend';
                            setPickupPayload(payload, {silent: true});
                        }
                    });

                    function ensurePickupSelection() {
                        var payload = parseJsonValue(pickupJsonInput.val());
                        if (payload && payload.id) {
                            pickupSelect.val(String(payload.id));
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
                            pickupSelect.prop('disabled', false);
                        });
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
                            fields: ['formatted_address', 'geometry', 'name', 'place_id'],
                            componentRestrictions: {country: ['ge']}
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
                                lng: place.geometry.location.lng(),
                                place_id: place.place_id || ''
                            });
                        });
                    }

                    if (dropoffDisplay.length) {
                        dropoffDisplay.on('input', function () {
                            if (suppressDropoffInput) {
                                return;
                            }
                            clearDropoffPayload();
                        });
                    }

                    if ($form.is('form')) {
                        $form.on('submit', function (event) {
                            updateDatetimeValue();

                            var dropoffPayload = parseJsonValue(dropoffJsonInput.val());
                            if (!dropoffPayload || !dropoffPayload.lat || !dropoffPayload.lng || !dropoffPayload.place_id) {
                                if (dropoffDisplay.length) {
                                    dropoffDisplay[0].setCustomValidity(dropoffRequiredMessage);
                                    dropoffDisplay[0].reportValidity();
                                }
                                event.preventDefault();
                                return false;
                            }

                            if (dropoffDisplay.length) {
                                dropoffDisplay[0].setCustomValidity('');
                            }
                            return true;
                        });
                    }

                    setupDropoffAutocomplete();
                    fetchPickupLocations();
                    ensurePickupSelection();

                    var initialDropoff = parseJsonValue(dropoffJsonInput.val());
                    if (initialDropoff) {
                        setDropoffPayload(initialDropoff, {preserveDisplay: false});
                    }

                    emitTransferUpdate();
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
