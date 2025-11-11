<script>
    (function (window, $) {
        var timezoneOffset = '{{ \Carbon\Carbon::now('Asia/Tbilisi')->format('P') }}';
        var dropoffRequiredMessage = '{{ __('transfers.form.dropoff_coordinates_required') }}';

        var googlePlacesDetailsServiceInstance = null;
        var googlePlacesAutocompleteInstance = null;
        var googlePlacesServiceNode = null;
        var googlePlacesSessionToken = null;
        var googleMapsGeocoderInstance = null;
        var googleMapsScriptLoading = false;

        function getSiteLocale() {
            if (window.bookingCore) {
                if (bookingCore.locale) {
                    return String(bookingCore.locale);
                }
                if (bookingCore.locale_default) {
                    return String(bookingCore.locale_default);
                }
            }
            return '';
        }

        function getPlacesLanguage() {
            var locale = getSiteLocale();
            if (!locale) {
                return '';
            }
            return locale.replace('_', '-');
        }

        function ensureGoogleMapsScript() {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                return;
            }
            if (googleMapsScriptLoading) {
                return;
            }
            var apiKey = window.bookingCore && bookingCore.map_gmap_key ? bookingCore.map_gmap_key : '';
            var params = ['libraries=places'];
            var language = getPlacesLanguage();
            if (language) {
                params.push('language=' + encodeURIComponent(language));
            }
            if (apiKey) {
                params.unshift('key=' + encodeURIComponent(apiKey));
            }
            var script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?' + params.join('&');
            script.async = true;
            script.defer = true;
            script.onload = function () {
                googleMapsScriptLoading = false;
                resetPlacesSessionToken();
            };
            script.onerror = function () {
                googleMapsScriptLoading = false;
            };
            googleMapsScriptLoading = true;
            document.head.appendChild(script);
        }

        function parseJsonValue(value, options) {
            if (!value) {
                return null;
            }
            options = options || {};
            try {
                return JSON.parse(value);
            } catch (e) {
                if (typeof options.onError === 'function') {
                    options.onError(e);
                }
                return null;
            }
        }

        function serialisePayload(payload) {
            return payload ? JSON.stringify(payload) : '';
        }

        function schedule(fn, delay) {
            return window.setTimeout(fn, delay || 0);
        }

        function getPlacesDetailsService() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                return null;
            }
            if (!googlePlacesServiceNode) {
                googlePlacesServiceNode = document.createElement('div');
            }
            if (!googlePlacesDetailsServiceInstance) {
                googlePlacesDetailsServiceInstance = new google.maps.places.PlacesService(googlePlacesServiceNode);
            }
            return googlePlacesDetailsServiceInstance;
        }

        function getAutocompleteService() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                return null;
            }
            if (!googlePlacesAutocompleteInstance) {
                googlePlacesAutocompleteInstance = new google.maps.places.AutocompleteService();
            }
            return googlePlacesAutocompleteInstance;
        }

        function getGeocoder() {
            if (typeof google === 'undefined' || !google.maps) {
                return null;
            }
            if (!googleMapsGeocoderInstance) {
                googleMapsGeocoderInstance = new google.maps.Geocoder();
            }
            return googleMapsGeocoderInstance;
        }

        function ensurePlacesSessionToken() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places || typeof google.maps.places.AutocompleteSessionToken !== 'function') {
                return null;
            }
            if (!googlePlacesSessionToken) {
                googlePlacesSessionToken = new google.maps.places.AutocompleteSessionToken();
            }
            return googlePlacesSessionToken;
        }

        function resetPlacesSessionToken() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places || typeof google.maps.places.AutocompleteSessionToken !== 'function') {
                googlePlacesSessionToken = null;
                return;
            }
            googlePlacesSessionToken = new google.maps.places.AutocompleteSessionToken();
        }

        function sanitiseDropoffPayload(payload) {
            if (!payload || typeof payload !== 'object') {
                return null;
            }
            var address = payload.address ? String(payload.address).trim() : '';
            var name = payload.name ? String(payload.name).trim() : '';
            var placeId = payload.place_id ? String(payload.place_id).trim() : '';
            var lat = parseFloat(payload.lat);
            var lng = parseFloat(payload.lng);

            var hasLat = !isNaN(lat);
            var hasLng = !isNaN(lng);

            return {
                address: address || name || '',
                name: name || address || '',
                place_id: placeId,
                lat: hasLat ? parseFloat(lat.toFixed(6)) : null,
                lng: hasLng ? parseFloat(lng.toFixed(6)) : null
            };
        }

        function hasValidDropoffPayload(payload) {
            if (!payload || typeof payload !== 'object') {
                return false;
            }
            var placeId = payload.place_id ? String(payload.place_id).trim() : '';
            var lat = parseFloat(payload.lat);
            var lng = parseFloat(payload.lng);
            return !!placeId && !isNaN(lat) && !isNaN(lng);
        }

        function buildDropoffPayloadFromPlace(place, fallbackLabel) {
            if (!place) {
                return null;
            }
            var geometry = place.geometry || {};
            var location = geometry.location || null;
            var lat = null;
            var lng = null;
            if (location && typeof location.lat === 'function' && typeof location.lng === 'function') {
                lat = location.lat();
                lng = location.lng();
            }
            return sanitiseDropoffPayload({
                address: place.formatted_address || place.name || fallbackLabel || '',
                name: place.name || place.formatted_address || fallbackLabel || '',
                lat: lat,
                lng: lng,
                place_id: place.place_id || ''
            });
        }

        function initTransferForm($form) {
            if (!$form.length || $form.data('transfer-init')) {
                return;
            }
            $form.data('transfer-init', true);

            ensureGoogleMapsScript();

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
            var dateDisplay = $form.find('.js-transfer-date-display').first();
            var dateFieldWrapper = $form.find('.js-transfer-date-field').first();
            var dateError = $form.find('.js-transfer-date-error').first();
            var timeInput = $form.find('.js-transfer-time').first();
            var datetimeInput = $form.find('.js-transfer-datetime').first();
            var suppressManualDateCheck = false;
            var dropoffResolveToken = null;
            var lastResolvedDropoffValue = '';
            var dropoffInputDebounceTimer = null;

            var fetchUrl = pickupSelect.data('fetch-url');
            var defaultOptionLabel = pickupSelect.data('default-label') || pickupSelect.find('option').first().text() || '';
            var pickupFetchLoaded = false;
            var suppressDropoffInput = false;
            var suppressDateSync = false;
            var restoreErrorMessage = $form.data('restore-error') || '';
            var restoreErrorReported = false;

            function emitFormError(message) {
                $form.trigger('transfer:form-error', message || '');
            }

            function handleJsonParseError() {
                if (restoreErrorReported || !restoreErrorMessage) {
                    return;
                }
                restoreErrorReported = true;
                emitFormError(restoreErrorMessage);
            }

            function setDateError(message) {
                if (dateError.length) {
                    dateError.toggleClass('d-none', !message);
                    dateError.text(message || '');
                }
                $form.trigger('transfer:date-error', message || '');
            }

            function emitTransferUpdate() {
                var context = {
                    pickup: parseJsonValue(pickupJsonInput.val(), {onError: handleJsonParseError}),
                    dropoff: parseJsonValue(dropoffJsonInput.val(), {onError: handleJsonParseError})
                };
                $form.trigger('transfer:context-changed', context);
            }

            function getDateDisplayFormat() {
                var defaultFormat = (window.bookingCore && bookingCore.date_format) ? bookingCore.date_format : 'YYYY-MM-DD';
                if (!dateDisplay.length) {
                    return defaultFormat;
                }
                var attrFormat = dateDisplay.data('display-format');
                return attrFormat || defaultFormat;
            }

            function setDateValue(value, options) {
                options = options || {};
                var isoValue = value || '';
                if (dateInput.length && !options.skipInput) {
                    suppressDateSync = true;
                    dateInput.val(isoValue);
                    if (!options.silent) {
                        dateInput.trigger('change');
                        dateInput.trigger('input');
                    }
                    suppressDateSync = false;
                }
                if (dateDisplay.length) {
                    var displayValue = '';
                    if (isoValue && typeof moment !== 'undefined') {
                        var parsed = moment(isoValue, 'YYYY-MM-DD', true);
                        if (parsed.isValid()) {
                            displayValue = parsed.format(getDateDisplayFormat());
                        } else {
                            displayValue = isoValue;
                        }
                    }
                    suppressManualDateCheck = true;
                    dateDisplay.val(displayValue);
                    suppressManualDateCheck = false;
                }
                setDateError('');
                if (!options.silent) {
                    $form.trigger('transfer:date-changed', isoValue);
                }
            }

            if (dateInput.length) {
                dateInput.on('change', function () {
                    if (suppressDateSync) {
                        return;
                    }
                    setDateValue($(this).val(), {silent: true, skipInput: true});
                });
            }

            $form.on('transfer:update-date', function (event, isoDate) {
                setDateValue(isoDate, {silent: true, skipInput: true});
            });

            function setupDatePicker() {
                if (!dateDisplay.length || dateDisplay.data('drp-bound')) {
                    return;
                }
                if (typeof jQuery === 'undefined' || typeof $.fn.daterangepicker === 'undefined') {
                    schedule(setupDatePicker, 400);
                    return;
                }
                var rtl = !!(window.bookingCore && bookingCore.rtl);
                var options = {
                    singleDatePicker: true,
                    autoApply: true,
                    sameDate: true,
                    showCalendar: true,
                    disabledPast: true,
                    enableLoading: true,
                    showEventTooltip: true,
                    classNotAvailable: ['disabled', 'off'],
                    disableHightLight: true,
                    opens: rtl ? 'right' : 'left',
                    locale: {
                        direction: rtl ? 'rtl' : 'ltr'
                    },
                    isInvalidDate: function (date) {
                        var events = dateDisplay.data('transferEvents');
                        if (!Array.isArray(events)) {
                            return false;
                        }
                        for (var i = 0; i < events.length; i++) {
                            var item = events[i];
                            if (item && item.start === date.format('YYYY-MM-DD')) {
                                if (typeof item.active !== 'undefined' && !item.active) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    }
                };
                if (typeof daterangepickerLocale === 'object') {
                    options.locale = $.extend(true, {}, daterangepickerLocale, options.locale);
                }
                if (typeof moment !== 'undefined') {
                    options.minDate = moment();
                }
                dateDisplay.daterangepicker(options).on('apply.daterangepicker', function (ev, picker) {
                    var isoDate = picker.startDate.format('YYYY-MM-DD');
                    setDateValue(isoDate);
                }).on('show.daterangepicker', function () {
                    var drp = dateDisplay.data('daterangepicker');
                    if (drp) {
                        drp.updateCalendars();
                    }
                    if (dateFieldWrapper.length) {
                        dateFieldWrapper.attr('aria-expanded', 'true');
                    }
                }).on('hide.daterangepicker', function () {
                    if (dateFieldWrapper.length) {
                        dateFieldWrapper.attr('aria-expanded', 'false');
                    }
                });
                dateDisplay.data('drp-bound', true);
                dateDisplay.on('click', function () {
                    $(this).trigger('focus');
                });
                dateDisplay.on('focus', function () {
                    var drp = dateDisplay.data('daterangepicker');
                    if (drp && typeof drp.show === 'function' && !drp.isShowing) {
                        drp.show();
                    }
                });
                if (dateFieldWrapper.length) {
                    if (!dateFieldWrapper.attr('tabindex')) {
                        dateFieldWrapper.attr('tabindex', '0');
                    }
                    dateFieldWrapper.attr('role', 'button');
                    dateFieldWrapper.attr('aria-haspopup', 'dialog');
                    dateFieldWrapper.attr('aria-expanded', dateDisplay.val() ? 'true' : 'false');
                    dateFieldWrapper.on('click', function (event) {
                        if (event.target !== dateDisplay[0]) {
                            event.preventDefault();
                            dateDisplay.trigger('focus');
                            dateDisplay.trigger('click');
                        }
                    });
                    dateFieldWrapper.on('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar') {
                            event.preventDefault();
                            dateDisplay.trigger('focus');
                            dateDisplay.trigger('click');
                        }
                    });
                }
                var initialValue = dateInput.length ? dateInput.val() : '';
                if (initialValue) {
                    setDateValue(initialValue, {silent: true, skipInput: true});
                    if (typeof moment !== 'undefined') {
                        var parsed = moment(initialValue, 'YYYY-MM-DD', true);
                        if (parsed.isValid()) {
                            var drpInstance = dateDisplay.data('daterangepicker');
                            if (drpInstance) {
                                drpInstance.setStartDate(parsed);
                                drpInstance.setEndDate(parsed);
                            }
                        }
                    }
                } else {
                    setDateValue('', {silent: true, skipInput: true});
                }
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
                if (dropoffInputDebounceTimer) {
                    window.clearTimeout(dropoffInputDebounceTimer);
                    dropoffInputDebounceTimer = null;
                }
                var sanitised = sanitiseDropoffPayload(payload);
                if (!sanitised) {
                    if (dropoffJsonInput.length) {
                        dropoffJsonInput.val('');
                    }
                    if (dropoffAddress.length) {
                        dropoffAddress.val('');
                    }
                    if (dropoffName.length) {
                        dropoffName.val('');
                    }
                    if (dropoffLat.length) {
                        dropoffLat.val('');
                    }
                    if (dropoffLng.length) {
                        dropoffLng.val('');
                    }
                    if (dropoffPlaceId.length) {
                        dropoffPlaceId.val('');
                    }
                    if (!options.preserveDisplay) {
                        setDropoffDisplayValue('');
                    }
                    dropoffDisplay.each(function () {
                        this.setCustomValidity('');
                    });
                    lastResolvedDropoffValue = '';
                    emitTransferUpdate();
                    return;
                }
                var serialised = serialisePayload(sanitised);
                if (dropoffJsonInput.length) {
                    dropoffJsonInput.val(serialised);
                }
                if (dropoffAddress.length) {
                    dropoffAddress.val(sanitised.address || sanitised.name || '');
                }
                if (dropoffName.length) {
                    dropoffName.val(sanitised.name || '');
                }
                if (dropoffLat.length) {
                    dropoffLat.val(hasValidDropoffPayload(sanitised) ? sanitised.lat : '');
                }
                if (dropoffLng.length) {
                    dropoffLng.val(hasValidDropoffPayload(sanitised) ? sanitised.lng : '');
                }
                if (dropoffPlaceId.length) {
                    dropoffPlaceId.val(sanitised.place_id || '');
                }
                if (!options.preserveDisplay) {
                    var displayValue = sanitised.address || sanitised.name || '';
                    setDropoffDisplayValue(displayValue);
                }
                dropoffDisplay.each(function () {
                    this.setCustomValidity('');
                });
                lastResolvedDropoffValue = (sanitised.address || sanitised.name || '').toLowerCase();
                emitTransferUpdate();
                if (hasValidDropoffPayload(sanitised)) {
                    resetPlacesSessionToken();
                }
            }

            function clearDropoffPayload() {
                lastResolvedDropoffValue = '';
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
                var payload = parseJsonValue(pickupJsonInput.val(), {onError: handleJsonParseError});
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
            setupDatePicker();
            if (dateInput.length) {
                setDateValue(dateInput.val(), {silent: true, skipInput: true});
            }
            updateDatetimeValue();

            function handleManualDateInput() {
                if (!dateDisplay.length || suppressManualDateCheck) {
                    return;
                }
                if (dateDisplay.prop('readonly')) {
                    return;
                }
                var rawValue = dateDisplay.val();
                if (!rawValue) {
                    setDateValue('', {silent: true});
                    setDateError('');
                    return;
                }
                if (typeof moment === 'undefined') {
                    setDateError('');
                    return;
                }
                var parsed = moment(rawValue, getDateDisplayFormat(), true);
                if (parsed.isValid()) {
                    setDateError('');
                    setDateValue(parsed.format('YYYY-MM-DD'));
                    updateDatetimeValue();
                } else {
                    var invalidMessage = dateDisplay.data('invalid-message') || '{{ __('transfers.form.date_invalid') }}';
                    setDateError(invalidMessage);
                    setDateValue('', {silent: true});
                    updateDatetimeValue();
                }
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
                    fields: ['formatted_address', 'geometry', 'name', 'place_id', 'address_components'],
                    types: ['geocode']
                });
                autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place || !place.geometry || !place.geometry.location) {
                        ensureDropoffFromInput({force: true});
                        return;
                    }
                    var payload = buildDropoffPayloadFromPlace(place);
                    if (payload) {
                        setDropoffPayload(payload);
                    }
                });
            }

            if (dropoffDisplay.length) {
                dropoffDisplay.on('input', function () {
                    if (suppressDropoffInput) {
                        return;
                    }
                    clearDropoffPayload();
                    lastResolvedDropoffValue = '';
                    if (dropoffInputDebounceTimer) {
                        window.clearTimeout(dropoffInputDebounceTimer);
                    }
                    dropoffInputDebounceTimer = schedule(function () {
                        ensureDropoffFromInput({debounced: true});
                    }, 300);
                });
                dropoffDisplay.on('focus', function () {
                    this.setCustomValidity('');
                });
                dropoffDisplay.on('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        ensureDropoffFromInput({force: true});
                    }
                });
                dropoffDisplay.on('blur', function () {
                    ensureDropoffFromInput();
                });
            }

            if ($form.is('form')) {
                $form.on('submit', function (event) {
                    updateDatetimeValue();

                    var dropoffPayload = parseJsonValue(dropoffJsonInput.val(), {onError: handleJsonParseError});
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

            var initialDropoff = parseJsonValue(dropoffJsonInput.val(), {onError: handleJsonParseError});
            if (initialDropoff) {
                setDropoffPayload(initialDropoff, {preserveDisplay: false});
            }
            if (!hasValidDropoffPayload(initialDropoff) && dropoffDisplay.length && dropoffDisplay.val()) {
                schedule(function () {
                    ensureDropoffFromInput({force: true, silent: true});
                }, 800);
            }

            if (dateDisplay.length) {
                dateDisplay.on('input change blur', handleManualDateInput);
            }

            emitTransferUpdate();

            function ensureDropoffFromInput(options) {
                options = options || {};
                var textValue = dropoffDisplay.length ? $.trim(dropoffDisplay.val()) : '';
                if (!textValue || textValue.length < 3) {
                    return;
                }
                var currentPayload = parseJsonValue(dropoffJsonInput.val(), {onError: handleJsonParseError});
                if (!options.force && hasValidDropoffPayload(currentPayload)) {
                    var currentLabel = (currentPayload.address || currentPayload.name || '').toLowerCase();
                    if (currentLabel === textValue.toLowerCase()) {
                        return;
                    }
                }
                if (!options.force && textValue.toLowerCase() === lastResolvedDropoffValue) {
                    return;
                }
                if (dropoffResolveToken && typeof dropoffResolveToken.cancelled !== 'undefined') {
                    dropoffResolveToken.cancelled = true;
                }
                var autocompleteService = getAutocompleteService();
                var detailsService = getPlacesDetailsService();
                if (!autocompleteService || !detailsService) {
                    if (!options.silent) {
                        ensureGoogleMapsScript();
                        schedule(function () {
                            ensureDropoffFromInput($.extend({}, options, {silent: true}));
                        }, 600);
                    }
                    return;
                }
                var requestToken = {cancelled: false};
                requestToken.query = textValue;
                dropoffResolveToken = requestToken;
                var sessionToken = ensurePlacesSessionToken();
                var language = getPlacesLanguage();
                var predictionRequest = {
                    input: textValue,
                    types: ['geocode']
                };
                if (sessionToken) {
                    predictionRequest.sessionToken = sessionToken;
                }
                if (language) {
                    predictionRequest.language = language;
                }
                autocompleteService.getPlacePredictions(predictionRequest, function (predictions, status) {
                    if (requestToken.cancelled) {
                        if (dropoffResolveToken === requestToken) {
                            dropoffResolveToken = null;
                        }
                        return;
                    }
                    if (status === google.maps.places.PlacesServiceStatus.OK && predictions && predictions.length) {
                        resolvePlacePrediction(predictions[0], requestToken, options, sessionToken, false);
                        return;
                    }
                    attemptTextSearchFallback(requestToken, options, sessionToken, false);
                });
            }

            function resolvePlacePrediction(prediction, requestToken, options, sessionToken, fromFallback) {
                var detailsService = getPlacesDetailsService();
                if (!detailsService || !prediction || !prediction.place_id) {
                    attemptTextSearchFallback(requestToken, options, sessionToken, true);
                    return;
                }
                var detailsRequest = {
                    placeId: prediction.place_id,
                    fields: ['formatted_address', 'geometry', 'name', 'place_id', 'address_components']
                };
                if (sessionToken) {
                    detailsRequest.sessionToken = sessionToken;
                }
                detailsService.getDetails(detailsRequest, function (place, detailStatus) {
                    if (requestToken.cancelled) {
                        if (dropoffResolveToken === requestToken) {
                            dropoffResolveToken = null;
                        }
                        return;
                    }
                    if (detailStatus === google.maps.places.PlacesServiceStatus.OK && place && place.geometry && place.geometry.location) {
                        var resolvedPayload = buildDropoffPayloadFromPlace(place, requestToken.query);
                        handleDropoffSuccess(requestToken, resolvedPayload, options);
                        return;
                    }
                    if (fromFallback) {
                        attemptGeocodeFallback(requestToken, options);
                    } else {
                        attemptTextSearchFallback(requestToken, options, sessionToken, true);
                    }
                });
            }

            function attemptTextSearchFallback(requestToken, options, sessionToken, hasRetried) {
                var detailsService = getPlacesDetailsService();
                if (!detailsService || typeof detailsService.textSearch !== 'function') {
                    attemptGeocodeFallback(requestToken, options);
                    return;
                }
                var language = getPlacesLanguage();
                var textSearchRequest = {query: requestToken.query};
                if (language) {
                    textSearchRequest.language = language;
                }
                detailsService.textSearch(textSearchRequest, function (results, status) {
                    if (requestToken.cancelled) {
                        if (dropoffResolveToken === requestToken) {
                            dropoffResolveToken = null;
                        }
                        return;
                    }
                    if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length) {
                        var best = results[0];
                        if (best.place_id && !hasRetried) {
                            resolvePlacePrediction(best, requestToken, options, sessionToken, true);
                            return;
                        }
                        if (best.geometry && best.geometry.location) {
                            var location = best.geometry.location;
                            var lat = typeof location.lat === 'function' ? location.lat() : location.lat;
                            var lng = typeof location.lng === 'function' ? location.lng() : location.lng;
                            var payload = sanitiseDropoffPayload({
                                address: best.formatted_address || best.name || requestToken.query,
                                name: best.name || best.formatted_address || requestToken.query,
                                lat: lat,
                                lng: lng,
                                place_id: best.place_id || ''
                            });
                            handleDropoffSuccess(requestToken, payload, options);
                            return;
                        }
                    }
                    attemptGeocodeFallback(requestToken, options);
                });
            }

            function attemptGeocodeFallback(requestToken, options) {
                var geocoder = getGeocoder();
                if (!geocoder) {
                    handleDropoffFailure(requestToken, 'geocoder_unavailable', options);
                    return;
                }
                geocoder.geocode({address: requestToken.query}, function (results, status) {
                    if (requestToken.cancelled) {
                        if (dropoffResolveToken === requestToken) {
                            dropoffResolveToken = null;
                        }
                        return;
                    }
                    if (status === 'OK' && results && results.length) {
                        var first = results[0];
                        if (first && first.geometry && first.geometry.location) {
                            var location = first.geometry.location;
                            var lat = typeof location.lat === 'function' ? location.lat() : location.lat;
                            var lng = typeof location.lng === 'function' ? location.lng() : location.lng;
                            var payload = sanitiseDropoffPayload({
                                address: first.formatted_address || requestToken.query,
                                name: first.formatted_address || requestToken.query,
                                lat: lat,
                                lng: lng,
                                place_id: first.place_id || ''
                            });
                            handleDropoffSuccess(requestToken, payload, options);
                            return;
                        }
                    }
                    handleDropoffFailure(requestToken, 'geocode_failed', options);
                });
            }

            function handleDropoffSuccess(requestToken, payload, options) {
                if (requestToken.cancelled) {
                    return;
                }
                if (dropoffResolveToken === requestToken) {
                    dropoffResolveToken = null;
                }
                if (payload && hasValidDropoffPayload(payload)) {
                    setDropoffPayload(payload);
                    if (typeof options.onSuccess === 'function') {
                        options.onSuccess(payload);
                    }
                    return;
                }
                handleDropoffFailure(requestToken, 'invalid_payload', options);
            }

            function handleDropoffFailure(requestToken, reason, options) {
                if (dropoffResolveToken === requestToken) {
                    dropoffResolveToken = null;
                }
                resetPlacesSessionToken();
                if (typeof options.onError === 'function') {
                    options.onError(reason);
                    return;
                }
                if (!options.silent && dropoffDisplay.length) {
                    dropoffDisplay[0].setCustomValidity(dropoffRequiredMessage);
                    dropoffDisplay[0].reportValidity();
                }
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
