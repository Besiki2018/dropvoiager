(function ($) {
    'use strict';

    if (!$ || typeof $ !== 'function') {
        return;
    }

    var loaderState = {
        ready: false,
        loading: false,
        callbacks: []
    };

    var geocoder = null;
    var bookingContext = null;
    var detailMapState = {
        engine: null,
        map: null,
        defaultCenter: null,
        pickupMarker: null,
        dropoffMarker: null,
        directionsRenderer: null,
        directionsService: null,
        markerIcon: null,
        pickupIcon: null,
        dropoffIcon: null
    };

    var ICONS = {
        pickup: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
        dropoff: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
    };

    function runLoaderCallbacks(success) {
        var queue = loaderState.callbacks.splice(0, loaderState.callbacks.length);
        for (var i = 0; i < queue.length; i++) {
            var cb = queue[i];
            if (typeof cb === 'function') {
                try {
                    cb(success);
                } catch (error) {
                }
            }
        }
    }

    function ensureGoogle(callback) {
        var ready = loaderState.ready && window.google && window.google.maps && window.google.maps.places;
        if (ready) {
            if (typeof callback === 'function') {
                callback(true);
            }
            return;
        }
        if (typeof callback === 'function') {
            loaderState.callbacks.push(callback);
        }
        if (loaderState.loading) {
            return;
        }
        if (window.google && window.google.maps && window.google.maps.places) {
            loaderState.ready = true;
            runLoaderCallbacks(true);
            return;
        }
        loaderState.loading = true;
        var params = ['libraries=places,geometry'];
        if (typeof bookingCore !== 'undefined' && bookingCore.map_gmap_key) {
            params.unshift('key=' + bookingCore.map_gmap_key);
        }
        var script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?' + params.join('&');
        script.async = true;
        script.defer = true;
        script.onload = function () {
            loaderState.loading = false;
            loaderState.ready = true;
            runLoaderCallbacks(true);
        };
        script.onerror = function () {
            loaderState.loading = false;
            runLoaderCallbacks(false);
        };
        document.head.appendChild(script);
    }

    function withGoogle(callback) {
        ensureGoogle(function (success) {
            if (success && typeof callback === 'function') {
                callback();
            }
        });
    }

    function ensureGeocoder(callback) {
        withGoogle(function () {
            if (!geocoder) {
                try {
                    geocoder = new google.maps.Geocoder();
                } catch (error) {
                    geocoder = null;
                }
            }
            if (typeof callback === 'function') {
                callback(geocoder);
            }
        });
    }

    function parseCoordinate(value) {
        if (value === null || typeof value === 'undefined' || value === '') {
            return null;
        }
        var parsed = parseFloat(value);
        if (isNaN(parsed)) {
            return null;
        }
        return parsed;
    }

    function defaultMapCenter() {
        var lat = 0;
        var lng = 0;
        if (typeof bookingCore !== 'undefined' && bookingCore.map_options) {
            lat = parseCoordinate(bookingCore.map_options.map_lat_default);
            lng = parseCoordinate(bookingCore.map_options.map_lng_default);
        }
        if (lat === null) {
            lat = 0;
        }
        if (lng === null) {
            lng = 0;
        }
        return { lat: lat, lng: lng };
    }

    function getContext($element) {
        var $context = $element.closest('[data-transfer-form]');
        if (!$context.length) {
            $context = $element.closest('form');
        }
        return $context;
    }

    function getContextState($context) {
        var state = $context.data('transferFormState');
        if (!state) {
            state = {
                pickup: {
                    address: '',
                    name: '',
                    lat: null,
                    lng: null,
                    placeId: '',
                    display: '',
                    payload: ''
                },
                dropoff: {
                    address: '',
                    name: '',
                    lat: null,
                    lng: null,
                    placeId: '',
                    display: '',
                    payload: ''
                },
                userPickup: {
                    address: '',
                    name: '',
                    lat: null,
                    lng: null,
                    placeId: '',
                    display: '',
                    payload: ''
                },
                route: {
                    distanceKm: null,
                    distanceText: '',
                    durationText: ''
                }
            };
            $context.data('transferFormState', state);
        }
        return state;
    }

    function buildPayload(data) {
        if (!data) {
            return '';
        }
        var payload = {
            address: data.address || '',
            name: data.name || data.address || '',
            display_name: data.display || data.name || data.address || '',
            lat: data.lat !== null && typeof data.lat !== 'undefined' ? data.lat : null,
            lng: data.lng !== null && typeof data.lng !== 'undefined' ? data.lng : null,
            place_id: data.placeId || ''
        };
        try {
            return JSON.stringify(payload);
        } catch (error) {
            return '';
        }
    }

    function formatDistanceText(km) {
        if (typeof km !== 'number' || !isFinite(km)) {
            return '';
        }
        return km.toFixed(2) + ' km';
    }

    function computeDistanceKm(pickup, dropoff) {
        if (!pickup || !dropoff) {
            return null;
        }
        if (typeof pickup.lat !== 'number' || typeof pickup.lng !== 'number' || typeof dropoff.lat !== 'number' || typeof dropoff.lng !== 'number') {
            return null;
        }
        try {
            if (window.google && google.maps && google.maps.geometry && google.maps.geometry.spherical && google.maps.LatLng) {
                var from = new google.maps.LatLng(pickup.lat, pickup.lng);
                var to = new google.maps.LatLng(dropoff.lat, dropoff.lng);
                var meters = google.maps.geometry.spherical.computeDistanceBetween(from, to);
                if (isFinite(meters)) {
                    return meters / 1000;
                }
            }
        } catch (error) {}
        var toRad = function (value) {
            return value * Math.PI / 180;
        };
        var lat1 = toRad(pickup.lat);
        var lng1 = toRad(pickup.lng);
        var lat2 = toRad(dropoff.lat);
        var lng2 = toRad(dropoff.lng);
        var latDelta = lat2 - lat1;
        var lngDelta = lng2 - lng1;
        var a = Math.pow(Math.sin(latDelta / 2), 2) + Math.cos(lat1) * Math.cos(lat2) * Math.pow(Math.sin(lngDelta / 2), 2);
        var c = 2 * Math.asin(Math.sqrt(a));
        var earthRadiusKm = 6371;
        var distance = earthRadiusKm * c;
        return isFinite(distance) ? distance : null;
    }

    function updateRouteState($context, updates) {
        if (!$context || !$context.length) {
            return;
        }
        var state = getContextState($context);
        state.route = state.route || { distanceKm: null, distanceText: '', durationText: '' };
        updates = updates || {};
        if (Object.prototype.hasOwnProperty.call(updates, 'distanceKm')) {
            state.route.distanceKm = typeof updates.distanceKm === 'number' && isFinite(updates.distanceKm) ? updates.distanceKm : null;
        }
        if (Object.prototype.hasOwnProperty.call(updates, 'distanceText')) {
            state.route.distanceText = updates.distanceText || '';
        }
        if (Object.prototype.hasOwnProperty.call(updates, 'durationText')) {
            state.route.durationText = updates.durationText || '';
        }
        return state.route;
    }

    function cloneLocationForEvent(location) {
        if (!location) {
            return null;
        }
        return {
            address: location.address || '',
            name: location.name || '',
            display_name: location.display || location.name || location.address || '',
            lat: typeof location.lat === 'number' ? location.lat : null,
            lng: typeof location.lng === 'number' ? location.lng : null,
            place_id: location.placeId || location.place_id || ''
        };
    }

    function buildContextPayload(state) {
        state = state || {};
        return {
            pickup: cloneLocationForEvent(state.pickup),
            dropoff: cloneLocationForEvent(state.dropoff),
            userPickup: cloneLocationForEvent(state.userPickup),
            route: {
                distance_km: state.route && typeof state.route.distanceKm === 'number' ? state.route.distanceKm : null,
                distance_text: state.route && state.route.distanceText ? state.route.distanceText : '',
                duration_text: state.route && state.route.durationText ? state.route.durationText : ''
            }
        };
    }

    function emitContextChanged($context) {
        if (!$context || !$context.length) {
            return;
        }
        var state = getContextState($context);
        var payload = buildContextPayload(state);
        $context.trigger('transfer:context-changed', [payload]);
    }

    function readInitialValues($context) {
        var state = getContextState($context);
        var pickup = state.pickup;
        pickup.address = $context.find('.js-transfer-pickup-address').val() || '';
        pickup.name = $context.find('.js-transfer-pickup-name').val() || pickup.address;
        pickup.lat = parseCoordinate($context.find('.js-transfer-pickup-lat').val());
        pickup.lng = parseCoordinate($context.find('.js-transfer-pickup-lng').val());
        pickup.placeId = $context.find('.js-transfer-pickup-place-id').val() || '';
        pickup.display = $context.find('.js-transfer-pickup-display').val() || pickup.name || pickup.address;
        pickup.payload = $context.find('.js-transfer-pickup-payload').val() || buildPayload(pickup);

        var dropoff = state.dropoff;
        dropoff.address = $context.find('.js-transfer-dropoff-address').val() || '';
        dropoff.name = $context.find('.js-transfer-dropoff-name').val() || dropoff.address;
        dropoff.lat = parseCoordinate($context.find('.js-transfer-dropoff-lat').val());
        dropoff.lng = parseCoordinate($context.find('.js-transfer-dropoff-lng').val());
        dropoff.placeId = $context.find('.js-transfer-dropoff-place-id').val() || '';
        dropoff.display = $context.find('.js-transfer-dropoff-display').val() || dropoff.name || dropoff.address;
        dropoff.payload = $context.find('.js-transfer-dropoff-json').val() || buildPayload(dropoff);

        var userPickup = state.userPickup;
        userPickup.address = $context.find('.js-transfer-user-pickup-address').val() || '';
        userPickup.name = $context.find('.js-transfer-user-pickup-formatted').val() || userPickup.address;
        userPickup.lat = parseCoordinate($context.find('.js-transfer-user-pickup-lat').val());
        userPickup.lng = parseCoordinate($context.find('.js-transfer-user-pickup-lng').val());
        userPickup.placeId = $context.find('.js-transfer-user-pickup-place-id').val() || '';
        userPickup.display = userPickup.name || userPickup.address;
        userPickup.payload = $context.find('.js-transfer-user-pickup-json').val() || buildPayload({
            address: userPickup.address,
            name: userPickup.name,
            display: userPickup.display,
            lat: userPickup.lat,
            lng: userPickup.lng,
            placeId: userPickup.placeId
        });
    }

    function setInputValues($context, type, data, options) {
        options = options || {};
        var state = getContextState($context);
        var target = type === 'dropoff' ? state.dropoff : state.pickup;
        target.address = data.address || '';
        target.name = data.name || target.address;
        target.lat = typeof data.lat === 'number' ? data.lat : parseCoordinate(data.lat);
        target.lng = typeof data.lng === 'number' ? data.lng : parseCoordinate(data.lng);
        target.placeId = data.placeId || '';
        target.display = data.display || target.name || target.address;
        target.payload = data.payload || buildPayload(target);

        var prefix = '.js-transfer-' + type;
        var $display = $context.find(prefix + '-display');
        if (options.updateDisplay && $display.length) {
            $display.val(target.display || '');
        }
        $context.find(prefix + '-address').val(target.address || '');
        $context.find(prefix + '-name').val(target.name || '');
        $context.find(prefix + '-lat').val(target.lat !== null && typeof target.lat !== 'undefined' ? target.lat : '');
        $context.find(prefix + '-lng').val(target.lng !== null && typeof target.lng !== 'undefined' ? target.lng : '');
        $context.find(prefix + '-place-id').val(target.placeId || '');
        if (type === 'pickup') {
            $context.find(prefix + '-payload').val(target.payload || '');
        } else {
            $context.find(prefix + '-json').val(target.payload || '');
        }

        state = getContextState($context);
        var shouldCompute = state && state.pickup && state.dropoff && typeof state.pickup.lat === 'number' && typeof state.pickup.lng === 'number' && typeof state.dropoff.lat === 'number' && typeof state.dropoff.lng === 'number';
        if (shouldCompute) {
            var fallbackKm = computeDistanceKm(state.pickup, state.dropoff);
            updateRouteState($context, {
                distanceKm: fallbackKm,
                distanceText: formatDistanceText(fallbackKm)
            });
        } else {
            updateRouteState($context, {
                distanceKm: null,
                distanceText: '',
                durationText: ''
            });
        }

        refreshContextMaps($context);
        refreshDetailMap();
        emitContextChanged($context);
    }

    function clearType($context, type) {
        setInputValues($context, type, {
            address: '',
            name: '',
            lat: null,
            lng: null,
            placeId: '',
            display: '',
            payload: ''
        }, { updateDisplay: true });
    }

    function geocodeLatLng(latLng, callback) {
        ensureGeocoder(function (instance) {
            if (!instance || !latLng) {
                if (typeof callback === 'function') {
                    callback(null);
                }
                return;
            }
            instance.geocode({ location: latLng }, function (results, status) {
                if (status === 'OK' && results && results.length) {
                    callback(results[0]);
                } else {
                    callback(null);
                }
            });
        });
    }

    function handleMarkerPosition($context, type, latLng) {
        if (!latLng) {
            return;
        }
        geocodeLatLng(latLng, function (result) {
            var lat = latLng.lat();
            var lng = latLng.lng();
            var data = {
                lat: lat,
                lng: lng
            };
            if (result) {
                data.address = result.formatted_address || '';
                data.name = result.name || data.address;
                data.placeId = result.place_id || '';
                data.display = data.address || data.name;
            } else {
                var fallback = lat.toFixed(5) + ', ' + lng.toFixed(5);
                data.address = fallback;
                data.name = fallback;
                data.placeId = '';
                data.display = fallback;
            }
            data.payload = buildPayload({
                address: data.address,
                name: data.name,
                display: data.display,
                lat: data.lat,
                lng: data.lng,
                placeId: data.placeId
            });
            setInputValues($context, type, data, { updateDisplay: true });
        });
    }

    function createMapInstance($context, $container) {
        if (!$container || !$container.length) {
            return null;
        }
        var type = ($container.data('transferMap') || 'route').toString().toLowerCase();
        var lat = parseCoordinate($container.data('defaultLat'));
        var lng = parseCoordinate($container.data('defaultLng'));
        var center = defaultMapCenter();
        if (lat !== null) {
            center.lat = lat;
        }
        if (lng !== null) {
            center.lng = lng;
        }
        var map = new google.maps.Map($container.get(0), {
            center: { lat: center.lat, lng: center.lng },
            zoom: 13,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });
        var instance = {
            type: type,
            map: map,
            defaultCenter: new google.maps.LatLng(center.lat, center.lng),
            context: $context
        };
        if (type === 'pickup' || type === 'dropoff') {
            var marker = new google.maps.Marker({
                map: map,
                draggable: true,
                visible: false,
                icon: type === 'dropoff' ? ICONS.dropoff : ICONS.pickup
            });
            marker.addListener('dragend', function (event) {
                handleMarkerPosition($context, type, event && event.latLng ? event.latLng : null);
            });
            map.addListener('click', function (event) {
                if (event && event.latLng) {
                    marker.setPosition(event.latLng);
                    marker.setVisible(true);
                    handleMarkerPosition($context, type, event.latLng);
                }
            });
            instance.marker = marker;
        } else {
            instance.pickupMarker = new google.maps.Marker({
                map: map,
                draggable: false,
                visible: false,
                icon: ICONS.pickup
            });
            instance.dropoffMarker = new google.maps.Marker({
                map: map,
                draggable: false,
                visible: false,
                icon: ICONS.dropoff
            });
            instance.directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: true,
                map: map,
                polylineOptions: {
                    strokeColor: '#0d6efd',
                    strokeOpacity: 0.9,
                    strokeWeight: 5
                }
            });
            instance.directionsService = new google.maps.DirectionsService();
        }
        $container.data('transferMapInstance', instance);
        google.maps.event.trigger(map, 'resize');
        return instance;
    }

    function updateSingleMarkerMap(instance, data) {
        if (!instance || !instance.map || !instance.marker) {
            return;
        }
        var marker = instance.marker;
        var map = instance.map;
        if (data && typeof data.lat === 'number' && typeof data.lng === 'number') {
            var position = new google.maps.LatLng(data.lat, data.lng);
            marker.setPosition(position);
            marker.setVisible(true);
            map.setCenter(position);
            if (map.getZoom() < 14) {
                map.setZoom(14);
            }
        } else {
            marker.setVisible(false);
            if (instance.defaultCenter) {
                map.setCenter(instance.defaultCenter);
            }
        }
    }

    function updateRouteMap(instance, pickup, dropoff) {
        if (!instance || !instance.map) {
            return;
        }
        var $context = instance.context || null;
        var hasPickup = pickup && typeof pickup.lat === 'number' && typeof pickup.lng === 'number';
        var hasDropoff = dropoff && typeof dropoff.lat === 'number' && typeof dropoff.lng === 'number';
        var bounds = new google.maps.LatLngBounds();
        if (hasPickup) {
            var pickupPosition = new google.maps.LatLng(pickup.lat, pickup.lng);
            instance.pickupMarker.setPosition(pickupPosition);
            instance.pickupMarker.setVisible(true);
            bounds.extend(pickupPosition);
        } else {
            instance.pickupMarker.setVisible(false);
        }
        if (hasDropoff) {
            var dropoffPosition = new google.maps.LatLng(dropoff.lat, dropoff.lng);
            instance.dropoffMarker.setPosition(dropoffPosition);
            instance.dropoffMarker.setVisible(true);
            bounds.extend(dropoffPosition);
        } else {
            instance.dropoffMarker.setVisible(false);
        }
        if (hasPickup && hasDropoff) {
            instance.directionsRenderer.setMap(instance.map);
            var requestId = Date.now();
            instance.lastRouteRequestId = requestId;
            instance.directionsService.route({
                origin: { lat: pickup.lat, lng: pickup.lng },
                destination: { lat: dropoff.lat, lng: dropoff.lng },
                travelMode: google.maps.TravelMode.DRIVING
            }, function (response, status) {
                if (instance.lastRouteRequestId !== requestId) {
                    return;
                }
                if (status === 'OK' && response && response.routes && response.routes.length) {
                    instance.directionsRenderer.setDirections(response);
                    var leg = response.routes[0].legs && response.routes[0].legs.length ? response.routes[0].legs[0] : null;
                    var distanceKm = null;
                    var distanceText = '';
                    var durationText = '';
                    if (leg) {
                        if (leg.distance && typeof leg.distance.value !== 'undefined') {
                            distanceKm = leg.distance.value / 1000;
                        }
                        if (leg.distance && leg.distance.text) {
                            distanceText = leg.distance.text;
                        }
                        if (leg.duration && leg.duration.text) {
                            durationText = leg.duration.text;
                        }
                    }
                    if (distanceKm === null) {
                        distanceKm = computeDistanceKm(pickup, dropoff);
                    }
                    if (!distanceText) {
                        distanceText = formatDistanceText(distanceKm);
                    }
                    updateRouteState($context, {
                        distanceKm: distanceKm,
                        distanceText: distanceText,
                        durationText: durationText
                    });
                } else {
                    instance.directionsRenderer.setDirections({ routes: [] });
                    var fallbackKm = computeDistanceKm(pickup, dropoff);
                    updateRouteState($context, {
                        distanceKm: fallbackKm,
                        distanceText: formatDistanceText(fallbackKm),
                        durationText: ''
                    });
                }
                if ($context) {
                    emitContextChanged($context);
                }
            });
            instance.map.fitBounds(bounds);
        } else {
            instance.directionsRenderer.setDirections({ routes: [] });
            if (instance.defaultCenter) {
                instance.map.setCenter(instance.defaultCenter);
                instance.map.setZoom(13);
            }
            if ($context) {
                updateRouteState($context, {
                    distanceKm: null,
                    distanceText: '',
                    durationText: ''
                });
                emitContextChanged($context);
            }
        }
    }

    function refreshContextMaps($context) {
        withGoogle(function () {
            var state = getContextState($context);
            $context.find('[data-transfer-map]').each(function () {
                var $container = $(this);
                var instance = $container.data('transferMapInstance');
                if (!instance) {
                    instance = createMapInstance($context, $container);
                }
                if (!instance) {
                    return;
                }
                if (instance.type === 'pickup') {
                    updateSingleMarkerMap(instance, state.pickup);
                } else if (instance.type === 'dropoff') {
                    updateSingleMarkerMap(instance, state.dropoff);
                } else {
                    updateRouteMap(instance, state.pickup, state.dropoff);
                }
            });
        });
    }

    function attachAutocomplete($input, type) {
        if (!$input.length || $input.data('transferAutocomplete')) {
            return;
        }
        withGoogle(function () {
            if ($input.data('transferAutocomplete')) {
                return;
            }
            var autocomplete = new google.maps.places.Autocomplete($input.get(0), {
                fields: ['formatted_address', 'geometry', 'name', 'place_id']
            });
            $input.data('transferAutocomplete', autocomplete);
            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace() || {};
                var lat = null;
                var lng = null;
                if (place.geometry && place.geometry.location) {
                    lat = place.geometry.location.lat();
                    lng = place.geometry.location.lng();
                }
                var address = place.formatted_address || $input.val();
                var name = place.name || address;
                var placeId = place.place_id || '';
                var payload = buildPayload({
                    address: address,
                    name: name,
                    display: address || name,
                    lat: lat,
                    lng: lng,
                    placeId: placeId
                });
                var $context = getContext($input);
                setInputValues($context, type, {
                    address: address,
                    name: name,
                    lat: lat,
                    lng: lng,
                    placeId: placeId,
                    display: address || name,
                    payload: payload
                }, { updateDisplay: true });
                if (type === 'pickup') {
                    clearType($context, 'dropoff');
                }
            });
        });
    }

    function refreshDetailMap() {
        if (!detailMapState.map) {
            return;
        }
        var state = null;
        if (bookingContext && bookingContext.length) {
            state = getContextState(bookingContext);
        }
        if (!state) {
            detailMapState.pickupMarker.setVisible(false);
            detailMapState.dropoffMarker.setVisible(false);
            detailMapState.directionsRenderer.setDirections({ routes: [] });
            if (detailMapState.defaultCenter) {
                detailMapState.map.setCenter(detailMapState.defaultCenter);
            }
            return;
        }
        var pickup = state.pickup;
        var dropoff = state.dropoff;
        var hasPickup = pickup && typeof pickup.lat === 'number' && typeof pickup.lng === 'number';
        var hasDropoff = dropoff && typeof dropoff.lat === 'number' && typeof dropoff.lng === 'number';
        var bounds = new google.maps.LatLngBounds();
        if (hasPickup) {
            var pickupPosition = new google.maps.LatLng(pickup.lat, pickup.lng);
            detailMapState.pickupMarker.setPosition(pickupPosition);
            detailMapState.pickupMarker.setVisible(true);
            bounds.extend(pickupPosition);
        } else {
            detailMapState.pickupMarker.setVisible(false);
        }
        if (hasDropoff) {
            var dropoffPosition = new google.maps.LatLng(dropoff.lat, dropoff.lng);
            detailMapState.dropoffMarker.setPosition(dropoffPosition);
            detailMapState.dropoffMarker.setVisible(true);
            bounds.extend(dropoffPosition);
        } else {
            detailMapState.dropoffMarker.setVisible(false);
        }
        if (hasPickup && hasDropoff) {
            detailMapState.directionsService.route({
                origin: { lat: pickup.lat, lng: pickup.lng },
                destination: { lat: dropoff.lat, lng: dropoff.lng },
                travelMode: google.maps.TravelMode.DRIVING
            }, function (response, status) {
                if (status === 'OK') {
                    detailMapState.directionsRenderer.setDirections(response);
                    detailMapState.map.fitBounds(bounds);
                }
            });
        } else {
            detailMapState.directionsRenderer.setDirections({ routes: [] });
            if (detailMapState.defaultCenter) {
                detailMapState.map.setCenter(detailMapState.defaultCenter);
            }
        }
    }

    function initContext($context) {
        if (!$context.length || $context.data('transferFormInitialised')) {
            return;
        }
        $context.data('transferFormInitialised', true);
        if (!bookingContext && $context.data('transferForm') && $context.data('transferForm').toString() === 'car-booking') {
            bookingContext = $context;
        }
        readInitialValues($context);
        refreshContextMaps($context);
        emitContextChanged($context);
        $context.find('.js-transfer-pickup-display').each(function () {
            attachAutocomplete($(this), 'pickup');
        });
        $context.find('.js-transfer-dropoff-display').each(function () {
            attachAutocomplete($(this), 'dropoff');
        });
    }

    function initAll($scope) {
        var $root = $scope && $scope.length ? $scope : $(document);
        $root.each(function () {
            var $node = $(this);
            if ($node.is('[data-transfer-form]')) {
                initContext($node);
            }
            $node.find('[data-transfer-form]').each(function () {
                initContext($(this));
            });
        });
    }

    $(document).on('focus.transferPickup', '.js-transfer-pickup-display', function () {
        attachAutocomplete($(this), 'pickup');
    });

    $(document).on('focus.transferDropoff', '.js-transfer-dropoff-display', function () {
        attachAutocomplete($(this), 'dropoff');
    });

    $(document).on('input.transferPickup', '.js-transfer-pickup-display', function () {
        if (!this.value) {
            var $context = getContext($(this));
            clearType($context, 'pickup');
            clearType($context, 'dropoff');
        }
    });

    $(document).on('input.transferDropoff', '.js-transfer-dropoff-display', function () {
        if (!this.value) {
            var $context = getContext($(this));
            clearType($context, 'dropoff');
        }
    });

    var observer = null;
    function observeDom() {
        if (observer || !window.MutationObserver) {
            return;
        }
        observer = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var mutation = mutations[i];
                if (!mutation.addedNodes || !mutation.addedNodes.length) {
                    continue;
                }
                $(mutation.addedNodes).each(function () {
                    var $node = $(this);
                    if ($node.is('[data-transfer-form]')) {
                        initContext($node);
                    }
                    $node.find('[data-transfer-form]').each(function () {
                        initContext($(this));
                    });
                });
            }
        });
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    function registerDetailMap(engineMap, options) {
        options = options || {};
        withGoogle(function () {
            detailMapState.engine = engineMap;
            detailMapState.map = engineMap && engineMap.map ? engineMap.map : null;
            if (!detailMapState.map) {
                return;
            }
            var defaultCenter = options.defaultCenter || null;
            if (Array.isArray(defaultCenter) && defaultCenter.length >= 2) {
                detailMapState.defaultCenter = new google.maps.LatLng(parseCoordinate(defaultCenter[0]) || 0, parseCoordinate(defaultCenter[1]) || 0);
            } else if (defaultCenter && typeof defaultCenter.lat === 'number' && typeof defaultCenter.lng === 'number') {
                detailMapState.defaultCenter = new google.maps.LatLng(defaultCenter.lat, defaultCenter.lng);
            } else {
                var center = detailMapState.map.getCenter();
                detailMapState.defaultCenter = center ? center : null;
            }
            detailMapState.markerIcon = options.markerIcon || null;
            detailMapState.pickupIcon = options.pickupIcon || ICONS.pickup;
            detailMapState.dropoffIcon = options.dropoffIcon || ICONS.dropoff;
            detailMapState.pickupMarker = new google.maps.Marker({
                map: detailMapState.map,
                draggable: false,
                visible: false,
                icon: detailMapState.pickupIcon
            });
            detailMapState.dropoffMarker = new google.maps.Marker({
                map: detailMapState.map,
                draggable: false,
                visible: false,
                icon: detailMapState.dropoffIcon
            });
            detailMapState.directionsService = new google.maps.DirectionsService();
            detailMapState.directionsRenderer = new google.maps.DirectionsRenderer({
                map: detailMapState.map,
                suppressMarkers: true,
                polylineOptions: {
                    strokeColor: '#0d6efd',
                    strokeOpacity: 0.9,
                    strokeWeight: 5
                }
            });
            refreshDetailMap();
        });
    }

    observeDom();

    $(function () {
        ensureGoogle();
        initAll($(document));
    });

    window.BravoTransferForm = window.BravoTransferForm || {};
    window.BravoTransferForm.ensureGooglePlaces = ensureGoogle;
    window.BravoTransferForm.initAll = initAll;
    window.BravoTransferForm.registerDetailMap = registerDetailMap;
})(window.jQuery);
