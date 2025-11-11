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

        var transferRouteManager = (function () {
            var state = {
                pickup: null,
                dropoff: null,
                userPickup: null,
                mapEngine: null,
                map: null,
                directionsService: null,
                directionsRenderer: null,
                lastRequestId: 0
            };

            function normaliseLocation(location) {
                if (!location || typeof location !== 'object') {
                    return null;
                }
                var lat = parseFloat(location.lat);
                var lng = parseFloat(location.lng);
                if (isNaN(lat) || isNaN(lng)) {
                    return null;
                }
                var label = '';
                if (location.display_name) {
                    label = String(location.display_name);
                } else if (location.name) {
                    label = String(location.name);
                } else if (location.address) {
                    label = String(location.address);
                }
                return {
                    lat: lat,
                    lng: lng,
                    label: label
                };
            }

            function ensureDirections() {
                if (typeof google === 'undefined' || !google.maps) {
                    return null;
                }
                if (!state.directionsService) {
                    state.directionsService = new google.maps.DirectionsService();
                }
                if (!state.directionsRenderer && state.map) {
                    state.directionsRenderer = new google.maps.DirectionsRenderer({
                        map: state.map,
                        suppressMarkers: false,
                        polylineOptions: {
                            strokeColor: '#0066ff',
                            strokeOpacity: 0.8,
                            strokeWeight: 4
                        }
                    });
                } else if (state.directionsRenderer && state.map && typeof state.directionsRenderer.setMap === 'function') {
                    state.directionsRenderer.setMap(state.map);
                }
                return state.directionsService;
            }

            function clearRoute() {
                if (state.directionsRenderer && typeof state.directionsRenderer.setDirections === 'function') {
                    state.directionsRenderer.setDirections({routes: []});
                }
            }

            function requestRoute() {
                ensureGoogleMapsScript();
                if (!state.map) {
                    clearRoute();
                    return;
                }
                var pickup = state.pickup;
                var dropoff = state.dropoff;
                var userPickup = state.userPickup;
                if ((!pickup && !userPickup) || !dropoff) {
                    clearRoute();
                    return;
                }
                if (typeof google === 'undefined' || !google.maps) {
                    return;
                }
                var service = ensureDirections();
                if (!service) {
                    return;
                }
                state.lastRequestId += 1;
                var requestId = state.lastRequestId;
                var routeOrigin = pickup || userPickup;
                var requestOptions = {
                    origin: {lat: routeOrigin.lat, lng: routeOrigin.lng},
                    destination: {lat: dropoff.lat, lng: dropoff.lng},
                    travelMode: google.maps.TravelMode.DRIVING
                };
                if (pickup && userPickup) {
                    requestOptions.origin = {lat: pickup.lat, lng: pickup.lng};
                    requestOptions.waypoints = [{
                        location: new google.maps.LatLng(userPickup.lat, userPickup.lng),
                        stopover: true
                    }];
                }
                service.route(requestOptions, function (response, status) {
                    if (requestId !== state.lastRequestId) {
                        return;
                    }
                    if (status === google.maps.DirectionsStatus.OK && response) {
                        ensureDirections();
                        if (state.directionsRenderer && typeof state.directionsRenderer.setDirections === 'function') {
                            state.directionsRenderer.setDirections(response);
                        }
                    } else {
                        clearRoute();
                    }
                });
            }

            return {
                setContext: function (pickup, dropoff, userPickup) {
                    state.pickup = normaliseLocation(pickup);
                    state.dropoff = normaliseLocation(dropoff);
                    state.userPickup = normaliseLocation(userPickup);
                    requestRoute();
                },
                attachToMap: function (engine) {
                    if (!engine) {
                        return;
                    }
                    state.mapEngine = engine;
                    var incomingMap = null;
                    if (engine.map) {
                        incomingMap = engine.map;
                    } else if (engine.mapObject) {
                        incomingMap = engine.mapObject;
                    }
                    if (incomingMap && incomingMap !== state.map) {
                        if (state.directionsRenderer && typeof state.directionsRenderer.setMap === 'function') {
                            state.directionsRenderer.setMap(null);
                        }
                        state.directionsRenderer = null;
                    }
                    if (incomingMap) {
                        state.map = incomingMap;
                    }
                    ensureDirections();
                    requestRoute();
                },
                refresh: function () {
                    ensureDirections();
                    requestRoute();
                }
            };
        })();

        window.BravoTransferRoute = transferRouteManager;

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
                if (window.BravoTransferRoute && typeof window.BravoTransferRoute.refresh === 'function') {
                    window.BravoTransferRoute.refresh();
                }
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

        function sanitiseUserPickupPayload(payload) {
            if (!payload || typeof payload !== 'object') {
                return null;
            }
            var formatted = payload.formatted_address ? String(payload.formatted_address).trim() : '';
            var address = payload.address ? String(payload.address).trim() : '';
            var name = payload.name ? String(payload.name).trim() : '';
            var placeId = payload.place_id ? String(payload.place_id).trim() : '';
            var lat = parseFloat(payload.lat);
            var lng = parseFloat(payload.lng);

            var hasLat = !isNaN(lat);
            var hasLng = !isNaN(lng);

            return {
                formatted_address: formatted || address || name || '',
                address: address || formatted || name || '',
                name: name || formatted || address || '',
                place_id: placeId,
                lat: hasLat ? parseFloat(lat.toFixed(6)) : null,
                lng: hasLng ? parseFloat(lng.toFixed(6)) : null
            };
        }

        function hasValidUserPickupPayload(payload) {
            if (!payload || typeof payload !== 'object') {
                return false;
            }
            var placeId = payload.place_id ? String(payload.place_id).trim() : '';
            var lat = parseFloat(payload.lat);
            var lng = parseFloat(payload.lng);
            return !!placeId && !isNaN(lat) && !isNaN(lng);
        }

        function buildUserPickupPayloadFromPlace(place, fallbackLabel) {
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
            return sanitiseUserPickupPayload({
                formatted_address: place.formatted_address || place.name || fallbackLabel || '',
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
            var dropoffMapContainer = $form.find('.js-transfer-dropoff-map').first();
            var dropoffMap = null;
            var dropoffMarker = null;
            var userPickupJsonInput = $form.find('.js-transfer-user-pickup-json').first();
            var userPickupDisplay = $form.find('.js-transfer-user-pickup-display').first();
            var userPickupFormatted = $form.find('.js-transfer-user-pickup-formatted').first();
            var userPickupAddress = $form.find('.js-transfer-user-pickup-address').first();
            var userPickupLatInput = $form.find('.js-transfer-user-pickup-lat').first();
            var userPickupLngInput = $form.find('.js-transfer-user-pickup-lng').first();
            var userPickupPlaceIdInput = $form.find('.js-transfer-user-pickup-place-id').first();
            var userPickupMapContainer = $form.find('.js-transfer-user-pickup-map').first();
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
            var userPickupResolveToken = null;
            var userPickupMap = null;
            var userPickupMarker = null;

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
                    dropoff: parseJsonValue(dropoffJsonInput.val(), {onError: handleJsonParseError}),
                    userPickup: parseJsonValue(userPickupJsonInput.val(), {onError: handleJsonParseError})
                };
                $form.trigger('transfer:context-changed', context);
                if (window.BravoTransferRoute && typeof window.BravoTransferRoute.setContext === 'function') {
                    window.BravoTransferRoute.setContext(context.pickup, context.dropoff, context.userPickup);
                }
            }

            function getUserPickupPayload() {
                return parseJsonValue(userPickupJsonInput.val(), {onError: handleJsonParseError});
            }

            function updateUserPickupMarker(payload, options) {
                options = options || {};
                ensureUserPickupMap();
                if (!userPickupMap || !userPickupMarker) {
                    return;
                }
                if (payload && typeof payload.lat === 'number' && typeof payload.lng === 'number') {
                    var position = new google.maps.LatLng(payload.lat, payload.lng);
                    userPickupMarker.setPosition(position);
                    userPickupMarker.setVisible(true);
                    if (!options.preserveCenter) {
                        userPickupMap.panTo(position);
                    }
                } else {
                    userPickupMarker.setVisible(false);
                }
            }

            function setUserPickupPayload(payload, options) {
                options = options || {};
                var sanitized = payload ? sanitiseUserPickupPayload(payload) : null;
                if (!sanitized) {
                    clearUserPickupPayload(options);
                    return;
                }
                if (userPickupJsonInput.length) {
                    userPickupJsonInput.val(serialisePayload(sanitized));
                }
                if (userPickupFormatted.length) {
                    userPickupFormatted.val(sanitized.formatted_address || sanitized.address || '');
                }
                if (userPickupAddress.length) {
                    userPickupAddress.val(sanitized.address || sanitized.formatted_address || '');
                }
                if (userPickupLatInput.length) {
                    userPickupLatInput.val(sanitized.lat !== null ? sanitized.lat : '');
                }
                if (userPickupLngInput.length) {
                    userPickupLngInput.val(sanitized.lng !== null ? sanitized.lng : '');
                }
                if (userPickupPlaceIdInput.length) {
                    userPickupPlaceIdInput.val(sanitized.place_id || '');
                }
                if (userPickupDisplay.length && !options.preserveDisplay) {
                    userPickupDisplay.val(sanitized.formatted_address || sanitized.address || sanitized.name || '');
                }
                if (!options.skipMarker) {
                    updateUserPickupMarker(sanitized, {preserveCenter: options.preserveCenter});
                }
                if (!options.silent) {
                    emitTransferUpdate();
                    $form.trigger('transfer:user-pickup-updated', sanitized);
                }
            }

            function clearUserPickupPayload(options) {
                options = options || {};
                if (userPickupJsonInput.length) {
                    userPickupJsonInput.val('');
                }
                if (userPickupFormatted.length) {
                    userPickupFormatted.val('');
                }
                if (userPickupAddress.length) {
                    userPickupAddress.val('');
                }
                if (userPickupLatInput.length) {
                    userPickupLatInput.val('');
                }
                if (userPickupLngInput.length) {
                    userPickupLngInput.val('');
                }
                if (userPickupPlaceIdInput.length) {
                    userPickupPlaceIdInput.val('');
                }
                if (userPickupDisplay.length && !options.preserveDisplay) {
                    userPickupDisplay.val('');
                }
                if (!options.skipMarker) {
                    updateUserPickupMarker(null);
                }
                if (!options.silent) {
                    emitTransferUpdate();
                    $form.trigger('transfer:user-pickup-cleared');
                }
            }

            function resolveUserPickupByLatLng(latLng) {
                if (!latLng || typeof latLng.lat !== 'function' || typeof latLng.lng !== 'function') {
                    return;
                }
                var geocoder = getGeocoder();
                if (!geocoder) {
                    return;
                }
                geocoder.geocode({location: latLng}, function (results, status) {
                    if (status === 'OK' && results && results.length) {
                        var first = results[0];
                        var payload = sanitiseUserPickupPayload({
                            formatted_address: first.formatted_address || '',
                            address: first.formatted_address || '',
                            name: first.name || first.formatted_address || '',
                            place_id: first.place_id || '',
                            lat: latLng.lat(),
                            lng: latLng.lng()
                        });
                        setUserPickupPayload(payload, {skipMarker: true});
                    }
                });
            }

            function ensureUserPickupMap() {
                if (!userPickupMapContainer.length) {
                    return;
                }
                if (userPickupMap) {
                    return;
                }
                if (typeof google === 'undefined' || !google.maps) {
                    schedule(ensureUserPickupMap, 400);
                    return;
                }
                var initialPayload = sanitiseUserPickupPayload(getUserPickupPayload());
                var center = null;
                if (initialPayload && hasValidUserPickupPayload(initialPayload)) {
                    center = {lat: initialPayload.lat, lng: initialPayload.lng};
                } else {
                    var pickupPayload = parseJsonValue(pickupJsonInput.val(), {onError: handleJsonParseError});
                    var pickupLatVal = pickupPayload && pickupPayload.lat ? parseFloat(pickupPayload.lat) : null;
                    var pickupLngVal = pickupPayload && pickupPayload.lng ? parseFloat(pickupPayload.lng) : null;
                    if (!isNaN(pickupLatVal) && !isNaN(pickupLngVal)) {
                        center = {lat: pickupLatVal, lng: pickupLngVal};
                    } else {
                        var dropLatVal = dropoffLat.length ? parseFloat(dropoffLat.val()) : null;
                        var dropLngVal = dropoffLng.length ? parseFloat(dropoffLng.val()) : null;
                        if (!isNaN(dropLatVal) && !isNaN(dropLngVal)) {
                            center = {lat: dropLatVal, lng: dropLngVal};
                        }
                    }
                }
                if (!center) {
                    center = {lat: 41.7151, lng: 44.8271};
                }
                userPickupMap = new google.maps.Map(userPickupMapContainer[0], {
                    center: center,
                    zoom: 12
                });
                userPickupMarker = new google.maps.Marker({
                    map: userPickupMap,
                    draggable: true,
                    visible: false
                });
                google.maps.event.addListener(userPickupMap, 'click', function (event) {
                    if (!event || !event.latLng) {
                        return;
                    }
                    var latLng = event.latLng;
                    var payload = sanitiseUserPickupPayload({
                        formatted_address: userPickupFormatted.val() || userPickupAddress.val(),
                        address: userPickupAddress.val(),
                        place_id: '',
                        lat: latLng.lat(),
                        lng: latLng.lng()
                    });
                    setUserPickupPayload(payload, {preserveDisplay: true, skipMarker: true, silent: true});
                    updateUserPickupMarker(payload);
                    resolveUserPickupByLatLng(latLng);
                    emitTransferUpdate();
                });
                google.maps.event.addListener(userPickupMarker, 'dragend', function (event) {
                    if (!event || !event.latLng) {
                        return;
                    }
                    var latLng = event.latLng;
                    var payload = sanitiseUserPickupPayload({
                        formatted_address: userPickupFormatted.val() || userPickupAddress.val(),
                        address: userPickupAddress.val(),
                        place_id: userPickupPlaceIdInput.val(),
                        lat: latLng.lat(),
                        lng: latLng.lng()
                    });
                    setUserPickupPayload(payload, {preserveDisplay: true, skipMarker: true, silent: true});
                    updateUserPickupMarker(payload, {preserveCenter: true});
                    resolveUserPickupByLatLng(latLng);
                    emitTransferUpdate();
                });
                updateUserPickupMarker(initialPayload, {preserveCenter: true});
            }

            function geocodeUserPickupAddress(query, options) {
                options = options || {};
                if (!query) {
                    clearUserPickupPayload(options);
                    return;
                }
                if (!userPickupMapContainer.length) {
                    setUserPickupPayload({
                        formatted_address: query,
                        address: query,
                        name: query,
                        place_id: '',
                        lat: null,
                        lng: null
                    }, {silent: options.silent, preserveDisplay: true, skipMarker: true});
                    return;
                }
                var geocoder = getGeocoder();
                if (!geocoder) {
                    ensureGoogleMapsScript();
                    return;
                }
                if (userPickupResolveToken && typeof userPickupResolveToken.cancelled !== 'undefined') {
                    userPickupResolveToken.cancelled = true;
                }
                var requestToken = {cancelled: false};
                userPickupResolveToken = requestToken;
                geocoder.geocode({address: query}, function (results, status) {
                    if (requestToken.cancelled) {
                        return;
                    }
                    userPickupResolveToken = null;
                    if (status === 'OK' && results && results.length) {
                        var best = results[0];
                        var location = best.geometry && best.geometry.location;
                        var payload = sanitiseUserPickupPayload({
                            formatted_address: best.formatted_address || query,
                            address: best.formatted_address || query,
                            name: best.name || best.formatted_address || query,
                            place_id: best.place_id || '',
                            lat: location && typeof location.lat === 'function' ? location.lat() : (location ? location.lat : null),
                            lng: location && typeof location.lng === 'function' ? location.lng() : (location ? location.lng : null)
                        });
                        setUserPickupPayload(payload);
                        return;
                    }
                    if (!options.silent && userPickupDisplay.length && userPickupDisplay[0].setCustomValidity) {
                        userPickupDisplay[0].setCustomValidity('{{ __('transfers.form.pickup_coordinates_required') }}');
                        userPickupDisplay[0].reportValidity();
                    }
                });
            }

            function setupUserPickupAutocomplete() {
                if (!userPickupDisplay.length || userPickupDisplay.data('autocomplete-bound')) {
                    return;
                }
                if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                    schedule(setupUserPickupAutocomplete, 400);
                    return;
                }
                userPickupDisplay.data('autocomplete-bound', true);
                var autocomplete = new google.maps.places.Autocomplete(userPickupDisplay[0], {
                    fields: ['formatted_address', 'geometry', 'name', 'place_id', 'address_components'],
                    types: ['geocode']
                });
                autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place || !place.geometry || !place.geometry.location) {
                        var fallbackQuery = $.trim(userPickupDisplay.val());
                        if (fallbackQuery) {
                            geocodeUserPickupAddress(fallbackQuery, {silent: true});
                        }
                        return;
                    }
                    var payload = buildUserPickupPayloadFromPlace(place, userPickupDisplay.val());
                    if (payload) {
                        setUserPickupPayload(payload);
                    }
                });
            }

            function setupUserPickupInputHandlers() {
                if (!userPickupDisplay.length) {
                    return;
                }
                userPickupDisplay.on('focus', function () {
                    if (this.setCustomValidity) {
                        this.setCustomValidity('');
                    }
                });
                userPickupDisplay.on('input', function () {
                    if (userPickupMapContainer.length) {
                        return;
                    }
                    var text = $.trim(userPickupDisplay.val());
                    setUserPickupPayload({
                        formatted_address: text,
                        address: text,
                        name: text,
                        place_id: userPickupPlaceIdInput.length ? userPickupPlaceIdInput.val() : '',
                        lat: userPickupLatInput.length ? userPickupLatInput.val() : null,
                        lng: userPickupLngInput.length ? userPickupLngInput.val() : null
                    }, {skipMarker: true, preserveDisplay: true});
                });
                userPickupDisplay.on('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        var text = $.trim(userPickupDisplay.val());
                        if (text && text.length >= 3) {
                            geocodeUserPickupAddress(text);
                        }
                    }
                });
                if (!userPickupMapContainer.length) {
                    return;
                }
                userPickupDisplay.on('blur', function () {
                    var text = $.trim(userPickupDisplay.val());
                    if (!text) {
                        clearUserPickupPayload();
                        return;
                    }
                    geocodeUserPickupAddress(text);
                });
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

            function getDropoffPayload() {
                return parseJsonValue(dropoffJsonInput.val(), {onError: handleJsonParseError});
            }

            function setDropoffPayload(payload, options) {
                options = options || {};
                if (dropoffInputDebounceTimer) {
                    window.clearTimeout(dropoffInputDebounceTimer);
                    dropoffInputDebounceTimer = null;
                }
                var sanitised = sanitiseDropoffPayload(payload);
                ensureDropoffMap();
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
                    updateDropoffMarker(null);
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
                updateDropoffMarker(sanitised, options);
                dropoffDisplay.each(function () {
                    this.setCustomValidity('');
                });
                lastResolvedDropoffValue = (sanitised.address || sanitised.name || '').toLowerCase();
                updateDropoffMarker(sanitised, options);
                emitTransferUpdate();
                if (hasValidDropoffPayload(sanitised)) {
                    resetPlacesSessionToken();
                }
            }

            function clearDropoffPayload() {
                lastResolvedDropoffValue = '';
                setDropoffPayload(null, {preserveDisplay: true});
                updateDropoffMarker(null);
            }

            function updateDropoffMarker(payload, options) {
                if (!dropoffMapContainer.length) {
                    return;
                }
                options = options || {};
                ensureDropoffMap();
                if (!dropoffMap || !dropoffMarker || typeof google === 'undefined' || !google.maps) {
                    return;
                }
                var lat = payload && payload.lat !== undefined ? parseFloat(payload.lat) : NaN;
                var lng = payload && payload.lng !== undefined ? parseFloat(payload.lng) : NaN;
                var hasCoords = !isNaN(lat) && !isNaN(lng);
                if (!hasCoords) {
                    dropoffMarker.setVisible(false);
                    return;
                }
                var position = new google.maps.LatLng(lat, lng);
                dropoffMarker.setPosition(position);
                dropoffMarker.setVisible(true);
                if (!options.preserveCenter) {
                    dropoffMap.setCenter(position);
                }
            }

            function resolveDropoffLatLng(latLng) {
                ensureGoogleMapsScript();
                var geocoder = getGeocoder();
                if (!geocoder || !latLng) {
                    return;
                }
                if (dropoffResolveToken && typeof dropoffResolveToken.cancelled !== 'undefined') {
                    dropoffResolveToken.cancelled = true;
                }
                var requestToken = {cancelled: false};
                dropoffResolveToken = requestToken;
                geocoder.geocode({location: latLng}, function (results, status) {
                    if (requestToken.cancelled) {
                        return;
                    }
                    dropoffResolveToken = null;
                    if (status === 'OK' && results && results.length) {
                        var best = results[0];
                        var payload = sanitiseDropoffPayload({
                            address: best.formatted_address || '',
                            name: best.name || best.formatted_address || '',
                            lat: typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat,
                            lng: typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng,
                            place_id: best.place_id || ''
                        });
                        if (payload) {
                            setDropoffPayload(payload);
                            return;
                        }
                    }
                    var fallbackPayload = sanitiseDropoffPayload({
                        address: dropoffDisplay.length ? $.trim(dropoffDisplay.val()) : '',
                        name: dropoffDisplay.length ? $.trim(dropoffDisplay.val()) : '',
                        lat: typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat,
                        lng: typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng,
                        place_id: ''
                    });
                    setDropoffPayload(fallbackPayload, {preserveDisplay: true});
                });
            }

            function ensureDropoffMap() {
                if (!dropoffMapContainer.length) {
                    return;
                }
                if (dropoffMap) {
                    return;
                }
                if (typeof google === 'undefined' || !google.maps) {
                    schedule(ensureDropoffMap, 400);
                    return;
                }
                var initialPayload = sanitiseDropoffPayload(getDropoffPayload());
                var center = null;
                var zoom = 11;
                if (initialPayload && hasValidDropoffPayload(initialPayload)) {
                    center = {lat: parseFloat(initialPayload.lat), lng: parseFloat(initialPayload.lng)};
                    zoom = 13;
                }
                if (!center) {
                    var pickupPayload = parseJsonValue(pickupJsonInput.val(), {onError: handleJsonParseError});
                    var pickupLat = pickupPayload && pickupPayload.lat ? parseFloat(pickupPayload.lat) : null;
                    var pickupLng = pickupPayload && pickupPayload.lng ? parseFloat(pickupPayload.lng) : null;
                    if (!isNaN(pickupLat) && !isNaN(pickupLng)) {
                        center = {lat: pickupLat, lng: pickupLng};
                        if (pickupPayload && pickupPayload.map_zoom) {
                            var pickupZoom = parseInt(pickupPayload.map_zoom, 10);
                            if (!isNaN(pickupZoom) && pickupZoom > 0) {
                                zoom = pickupZoom;
                            }
                        }
                    }
                }
                if (!center) {
                    center = {lat: 41.7151, lng: 44.8271};
                }
                dropoffMap = new google.maps.Map(dropoffMapContainer[0], {
                    center: center,
                    zoom: zoom
                });
                dropoffMarker = new google.maps.Marker({
                    map: dropoffMap,
                    draggable: true,
                    visible: false
                });
                google.maps.event.addListener(dropoffMap, 'click', function (event) {
                    if (!event || !event.latLng) {
                        return;
                    }
                    updateDropoffMarker({lat: event.latLng.lat(), lng: event.latLng.lng()}, {preserveCenter: true});
                    resolveDropoffLatLng(event.latLng);
                });
                google.maps.event.addListener(dropoffMarker, 'dragend', function (event) {
                    if (!event || !event.latLng) {
                        return;
                    }
                    resolveDropoffLatLng(event.latLng);
                });
                updateDropoffMarker(initialPayload, {preserveCenter: true});
                if (window.BravoTransferRoute && typeof window.BravoTransferRoute.attachToMap === 'function') {
                    window.BravoTransferRoute.attachToMap({map: dropoffMap});
                }
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

            function updateDropoffMarker(payload, options) {
                options = options || {};
                if (!dropoffMap) {
                    return;
                }
                var hasCoords = payload && typeof payload === 'object' && !isNaN(parseFloat(payload.lat)) && !isNaN(parseFloat(payload.lng));
                if (!dropoffMarker) {
                    dropoffMarker = new google.maps.Marker({
                        map: dropoffMap,
                        draggable: true,
                        visible: false
                    });
                    google.maps.event.addListener(dropoffMarker, 'dragend', function (event) {
                        if (!event || !event.latLng) {
                            return;
                        }
                        var dragPayload = sanitiseDropoffPayload({
                            lat: event.latLng.lat(),
                            lng: event.latLng.lng(),
                            place_id: dropoffPlaceId.length ? dropoffPlaceId.val() : '',
                            address: dropoffAddress.length ? dropoffAddress.val() : '',
                            name: dropoffName.length ? dropoffName.val() : ''
                        });
                        setDropoffPayload(dragPayload, {preserveDisplay: true});
                        resolveDropoffByLatLng(event.latLng);
                    });
                }
                if (!hasCoords) {
                    dropoffMarker.setVisible(false);
                    return;
                }
                var position = new google.maps.LatLng(parseFloat(payload.lat), parseFloat(payload.lng));
                dropoffMarker.setPosition(position);
                dropoffMarker.setVisible(true);
                if (!options.preserveCenter) {
                    dropoffMap.setCenter(position);
                }
            }

            function resolveDropoffByLatLng(latLng) {
                var geocoder = getGeocoder();
                if (!geocoder || !latLng) {
                    return;
                }
                geocoder.geocode({location: latLng}, function (results, status) {
                    if (status === 'OK' && results && results.length) {
                        var first = results[0];
                        var payload = sanitiseDropoffPayload({
                            address: first.formatted_address || dropoffDisplay.val(),
                            name: first.name || first.formatted_address || dropoffDisplay.val(),
                            place_id: first.place_id || '',
                            lat: latLng.lat(),
                            lng: latLng.lng()
                        });
                        setDropoffPayload(payload, {preserveDisplay: false});
                    }
                });
            }

            function ensureDropoffMap() {
                if (!dropoffMapContainer.length) {
                    return;
                }
                if (dropoffMap) {
                    return;
                }
                if (typeof google === 'undefined' || !google.maps) {
                    schedule(ensureDropoffMap, 400);
                    return;
                }
                var initialPayload = sanitiseDropoffPayload(getDropoffPayload());
                var center = null;
                if (initialPayload && !isNaN(parseFloat(initialPayload.lat)) && !isNaN(parseFloat(initialPayload.lng))) {
                    center = {lat: parseFloat(initialPayload.lat), lng: parseFloat(initialPayload.lng)};
                } else if (!isNaN(parseFloat(dropoffLat.val())) && !isNaN(parseFloat(dropoffLng.val()))) {
                    center = {lat: parseFloat(dropoffLat.val()), lng: parseFloat(dropoffLng.val())};
                } else {
                    var parsedPickup = parseJsonValue(pickupJsonInput.val(), {onError: handleJsonParseError}) || {};
                    var parsedPickupLat = parseFloat(parsedPickup.lat);
                    var parsedPickupLng = parseFloat(parsedPickup.lng);
                    if (!isNaN(parsedPickupLat) && !isNaN(parsedPickupLng)) {
                        center = {lat: parsedPickupLat, lng: parsedPickupLng};
                    }
                }
                if (!center) {
                    center = {lat: 41.7151, lng: 44.8271};
                }
                dropoffMap = new google.maps.Map(dropoffMapContainer[0], {
                    center: center,
                    zoom: 12
                });
                google.maps.event.addListener(dropoffMap, 'click', function (event) {
                    if (!event || !event.latLng) {
                        return;
                    }
                    var payload = sanitiseDropoffPayload({
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng(),
                        place_id: '',
                        address: dropoffAddress.length ? dropoffAddress.val() : dropoffDisplay.val(),
                        name: dropoffName.length ? dropoffName.val() : dropoffDisplay.val()
                    });
                    setDropoffPayload(payload, {preserveDisplay: true});
                    resolveDropoffByLatLng(event.latLng);
                });
                updateDropoffMarker(initialPayload, {preserveCenter: true});
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
                        var label = location.label || location.display_name || location.name || location.address || '';
                        if (!label && typeof location.id !== 'undefined') {
                            label = 'Location #' + location.id;
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
            ensureDropoffMap();
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

            var initialUserPickup = getUserPickupPayload();
            if (initialUserPickup) {
                setUserPickupPayload(initialUserPickup, {silent: true, preserveDisplay: true, skipMarker: true, preserveCenter: true});
            } else if (userPickupDisplay.length && userPickupDisplay.val()) {
                var latSeed = userPickupLatInput.length ? parseFloat(userPickupLatInput.val()) : null;
                var lngSeed = userPickupLngInput.length ? parseFloat(userPickupLngInput.val()) : null;
                if (!isNaN(latSeed) && !isNaN(lngSeed)) {
                    setUserPickupPayload({
                        formatted_address: userPickupDisplay.val(),
                        address: userPickupDisplay.val(),
                        name: userPickupDisplay.val(),
                        place_id: userPickupPlaceIdInput.length ? userPickupPlaceIdInput.val() : '',
                        lat: latSeed,
                        lng: lngSeed
                    }, {silent: true, preserveDisplay: true, skipMarker: true, preserveCenter: true});
                }
                if (userPickupMapContainer.length) {
                    schedule(function () {
                        geocodeUserPickupAddress(userPickupDisplay.val(), {silent: true});
                    }, 600);
                }
            }

            setupUserPickupAutocomplete();
            setupUserPickupInputHandlers();
            ensureUserPickupMap();

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
