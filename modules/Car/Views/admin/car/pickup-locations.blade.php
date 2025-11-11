@php
    $formPickupLocations = collect(old('pickup_locations', isset($row) ? $row->pickupLocations->map(function ($location) {
        return array_merge($location->toFrontendArray(), ['id' => $location->id]);
    })->toArray() : []));
@endphp
<div class="panel js-pickup-locations-wrapper">
    <div class="panel-title"><strong>{{ __('transfers.admin.pickups.title') }}</strong></div>
    <div class="panel-body">
        <p class="text-muted">{{ __('transfers.admin.pickups.instructions') }}</p>
        <div class="mb-3">
            <div id="pickup_locations_map" class="bravo-form-group" style="height: 320px;" data-markers='@json($formPickupLocations)'></div>
        </div>
        <div class="row g-3 align-items-end js-pickup-form">
            <div class="col-md-4">
                <label class="form-label">{{ __('transfers.admin.pickups.form.name') }}</label>
                <input type="text" class="form-control js-pickup-form-name" placeholder="{{ __('transfers.admin.pickups.form.name_placeholder') }}">
                <input type="hidden" class="js-pickup-form-address">
                <input type="hidden" class="js-pickup-form-place">
            </div>
            <div class="col-md-4">
                <div class="fw-bold">{{ __('transfers.admin.pickups.form.coordinates') }}</div>
                <div class="small text-muted js-pickup-form-coords">{{ __('transfers.admin.pickups.form.coordinates_hint') }}</div>
                <input type="hidden" class="js-pickup-form-lat">
                <input type="hidden" class="js-pickup-form-lng">
            </div>
            <div class="col-md-2">
                <div class="form-check mt-4">
                    <input class="form-check-input js-pickup-form-active" type="checkbox" checked id="pickup-form-active">
                    <label class="form-check-label" for="pickup-form-active">{{ __('transfers.admin.pickups.form.active_label') }}</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary js-pickup-add">{{ __('transfers.admin.pickups.form.add_button') }}</button>
                <button type="button" class="btn btn-outline-secondary js-pickup-reset">{{ __('transfers.admin.pickups.form.reset_button') }}</button>
            </div>
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-bordered align-middle js-pickup-table">
                <thead>
                    <tr>
                        <th>{{ __('transfers.admin.pickups.table.name') }}</th>
                        <th class="text-center" style="width: 220px;">{{ __('transfers.admin.pickups.table.coordinates') }}</th>
                        <th class="text-center" style="width: 140px;">{{ __('transfers.admin.pickups.table.status') }}</th>
                        <th style="width: 180px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formPickupLocations as $index => $location)
                        <tr data-index="{{ $index }}">
                            <td>
                                <input type="hidden" name="pickup_locations[{{ $index }}][id]" value="{{ $location['id'] ?? '' }}">
                                <input type="hidden" name="pickup_locations[{{ $index }}][lat]" class="js-pickup-lat" value="{{ $location['lat'] ?? '' }}">
                                <input type="hidden" name="pickup_locations[{{ $index }}][lng]" class="js-pickup-lng" value="{{ $location['lng'] ?? '' }}">
                                <input type="hidden" name="pickup_locations[{{ $index }}][address]" class="js-pickup-address" value="{{ $location['address'] ?? '' }}">
                                <input type="hidden" name="pickup_locations[{{ $index }}][place_id]" class="js-pickup-place" value="{{ $location['place_id'] ?? '' }}">
                                <input type="hidden" name="pickup_locations[{{ $index }}][is_active]" class="js-pickup-active" value="{{ ($location['is_active'] ?? true) ? 1 : 0 }}">
                                <input type="text" name="pickup_locations[{{ $index }}][name]" class="form-control js-pickup-name" value="{{ $location['name'] ?? '' }}">
                            </td>
                            <td class="text-center">
                                <div class="small text-muted js-pickup-coordinate-display">{{ ($location['lat'] ?? '') && ($location['lng'] ?? '') ? ($location['lat'] . ', ' . $location['lng']) : __('transfers.admin.pickups.table.no_coordinates') }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge js-pickup-status {{ ($location['is_active'] ?? true) ? 'badge-success' : 'badge-secondary' }}">{{ ($location['is_active'] ?? true) ? __('transfers.admin.pickups.table.status_active') : __('transfers.admin.pickups.table.status_inactive') }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary js-pickup-set-coords" data-index="{{ $index }}">{{ __('transfers.admin.pickups.actions.set_coordinates') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary js-pickup-toggle" data-index="{{ $index }}">{{ __('transfers.admin.pickups.actions.toggle_status') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-danger js-pickup-remove" data-index="{{ $index }}">{{ __('transfers.admin.pickups.actions.remove') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
    <script>
        jQuery(function ($) {
            var wrapper = $('.js-pickup-locations-wrapper');
            if (!wrapper.length) {
                return;
            }

            var coordinateHint = @json(__('transfers.admin.pickups.form.coordinates_hint'));
            var validationError = @json(__('transfers.admin.pickups.form.validation_error'));
            var confirmRemoveText = @json(__('transfers.admin.pickups.actions.confirm_remove'));
            var clickMapToSet = @json(__('transfers.admin.pickups.actions.click_map_to_set'));
            var statusActiveLabel = @json(__('transfers.admin.pickups.table.status_active'));
            var statusInactiveLabel = @json(__('transfers.admin.pickups.table.status_inactive'));
            var noCoordinatesLabel = @json(__('transfers.admin.pickups.table.no_coordinates'));
            var setCoordinatesLabel = @json(__('transfers.admin.pickups.actions.set_coordinates'));
            var toggleStatusLabel = @json(__('transfers.admin.pickups.actions.toggle_status'));
            var removeLabel = @json(__('transfers.admin.pickups.actions.remove'));

            var mapProvider = (window.bookingCore && bookingCore.map_provider) || '';

            if (mapProvider === 'gmap' && typeof google !== 'undefined' && google.maps) {
                initGoogleMapEngine();
                return;
            }

            initFallbackEngine();

            function computeNextIndex($tbody) {
                var maxIndex = -1;
                $tbody.find('tr').each(function () {
                    var value = parseInt($(this).data('index'), 10);
                    if (!isNaN(value) && value > maxIndex) {
                        maxIndex = value;
                    }
                });
                return maxIndex + 1;
            }

            function initGoogleMapEngine() {
                var tableBody = wrapper.find('.js-pickup-table tbody');
                var mapElement = document.getElementById('pickup_locations_map');
                if (!mapElement) {
                    return;
                }

                var defaultCenter = {
                    lat: Number(@json($row->map_lat ?? setting_item('map_lat_default', 41.715133))) || 0,
                    lng: Number(@json($row->map_lng ?? setting_item('map_lng_default', 44.827096))) || 0
                };
                var defaultZoom = Number(@json($row->map_zoom ?? 8)) || 8;

                var formNameInput = wrapper.find('.js-pickup-form-name');
                var formLatInput = wrapper.find('.js-pickup-form-lat');
                var formLngInput = wrapper.find('.js-pickup-form-lng');
                var formAddressInput = wrapper.find('.js-pickup-form-address');
                var formPlaceInput = wrapper.find('.js-pickup-form-place');
                var formCoordsDisplay = wrapper.find('.js-pickup-form-coords');
                var formActiveCheckbox = wrapper.find('.js-pickup-form-active');
                var formMarker = null;
                var activeRow = null;
                var activeRowMarker = null;
                var geocoder = new google.maps.Geocoder();

                var map = new google.maps.Map(mapElement, {
                    center: defaultCenter,
                    zoom: defaultZoom,
                    mapTypeControl: false,
                    streetViewControl: false
                });

                var nextIndex = computeNextIndex(tableBody);

                function parseNumber(value) {
                    var parsed = parseFloat(value);
                    return isNaN(parsed) ? null : parsed;
                }

                function formatCoordinate(value) {
                    return typeof value === 'number' && !isNaN(value) ? value.toFixed(6) : '';
                }

                function reverseGeocode(lat, lng, callback) {
                    if (!geocoder) {
                        return;
                    }
                    geocoder.geocode({location: {lat: lat, lng: lng}}, function (results, status) {
                        if (status === 'OK' && results && results.length) {
                            callback(results[0].formatted_address || results[0].name || '');
                        } else {
                            callback('');
                        }
                    });
                }

                function ensureFormMarker() {
                    if (!formMarker) {
                        formMarker = new google.maps.Marker({
                            map: map,
                            draggable: true
                        });
                        formMarker.addListener('dragend', function (event) {
                            updateFormCoordinates(event.latLng.lat(), event.latLng.lng(), true);
                        });
                    }
                    return formMarker;
                }

                function clearActiveRow() {
                    activeRow = null;
                    activeRowMarker = null;
                    wrapper.find('.js-pickup-table tr').removeClass('table-active');
                    tableBody.find('tr').each(function () {
                        var marker = $(this).data('mapMarker');
                        if (marker) {
                            marker.setDraggable(false);
                        }
                    });
                }

                function focusFormMarker() {
                    clearActiveRow();
                    var lat = parseNumber(formLatInput.val());
                    var lng = parseNumber(formLngInput.val());
                    if (lat === null || lng === null) {
                        if (formMarker) {
                            formMarker.setMap(null);
                        }
                        return;
                    }
                    var marker = ensureFormMarker();
                    marker.setPosition({lat: lat, lng: lng});
                    marker.setDraggable(true);
                    marker.setMap(map);
                    map.panTo(marker.getPosition());
                    if (map.getZoom() < 13) {
                        map.setZoom(13);
                    }
                }

                function ensureRowMarker(row) {
                    var marker = row.data('mapMarker');
                    var lat = parseNumber(row.find('.js-pickup-lat').val());
                    var lng = parseNumber(row.find('.js-pickup-lng').val());
                    var isActive = parseInt(row.find('.js-pickup-active').val(), 10) === 1;
                    if (!marker) {
                        marker = new google.maps.Marker({
                            map: null,
                            draggable: false,
                            title: row.find('.js-pickup-name').val() || ''
                        });
                        marker.addListener('dragend', function (event) {
                            if (activeRow && activeRow[0] === row[0]) {
                                updateRowCoordinates(row, event.latLng.lat(), event.latLng.lng(), true);
                            }
                        });
                        row.data('mapMarker', marker);
                    }
                    if (lat !== null && lng !== null) {
                        marker.setPosition({lat: lat, lng: lng});
                        marker.setTitle(row.find('.js-pickup-name').val() || '');
                        marker.setMap(isActive ? map : null);
                    } else {
                        marker.setMap(null);
                    }
                    marker.setDraggable(activeRow && activeRow[0] === row[0]);
                    return marker;
                }

                function updateRowCoordinates(row, lat, lng, shouldReverse) {
                    var latInput = row.find('.js-pickup-lat');
                    var lngInput = row.find('.js-pickup-lng');
                    var addressInput = row.find('.js-pickup-address');
                    var placeInput = row.find('.js-pickup-place');
                    var display = row.find('.js-pickup-coordinate-display');
                    if (lat === null || lng === null) {
                        latInput.val('');
                        lngInput.val('');
                        addressInput.val('');
                        placeInput.val('');
                        display.text(noCoordinatesLabel);
                        var marker = row.data('mapMarker');
                        if (marker) {
                            marker.setMap(null);
                        }
                        return;
                    }
                    latInput.val(lat);
                    lngInput.val(lng);
                    if (!shouldReverse && !addressInput.val()) {
                        addressInput.val(row.find('.js-pickup-name').val());
                    }
                    display.text(formatCoordinate(lat) + ', ' + formatCoordinate(lng));
                    var marker = ensureRowMarker(row);
                    if (shouldReverse) {
                        reverseGeocode(lat, lng, function (address) {
                            if (address) {
                                row.find('.js-pickup-name').val(address);
                                addressInput.val(address);
                            } else {
                                addressInput.val('');
                            }
                            placeInput.val('');
                            if (marker) {
                                marker.setTitle(row.find('.js-pickup-name').val() || '');
                            }
                        });
                    }
                }

                function updateFormCoordinates(lat, lng, shouldReverse) {
                    if (lat === null || lng === null) {
                        formLatInput.val('');
                        formLngInput.val('');
                        formAddressInput.val('');
                        formPlaceInput.val('');
                        formCoordsDisplay.text(coordinateHint);
                        if (formMarker) {
                            formMarker.setMap(null);
                        }
                        return;
                    }
                    formLatInput.val(lat);
                    formLngInput.val(lng);
                     if (!shouldReverse) {
                        formPlaceInput.val(formPlaceInput.val() || '');
                     }
                    formAddressInput.val(shouldReverse ? '' : formAddressInput.val());
                    formCoordsDisplay.text(formatCoordinate(lat) + ', ' + formatCoordinate(lng));
                    focusFormMarker();
                    if (shouldReverse) {
                        reverseGeocode(lat, lng, function (address) {
                            if (address) {
                                formNameInput.val(address);
                                formAddressInput.val(address);
                            } else {
                                formAddressInput.val('');
                            }
                            formPlaceInput.val('');
                        });
                    } else if (!formAddressInput.val()) {
                        formAddressInput.val(formNameInput.val());
                    }
                }

                function focusRowMarker(row) {
                    activeRow = row;
                    wrapper.find('.js-pickup-table tr').removeClass('table-active');
                    row.addClass('table-active');
                    activeRowMarker = ensureRowMarker(row);
                    tableBody.find('tr').each(function () {
                        var marker = $(this).data('mapMarker');
                        if (marker && marker !== activeRowMarker) {
                            marker.setDraggable(false);
                        }
                    });
                    if (activeRowMarker) {
                        activeRowMarker.setDraggable(true);
                        if (activeRowMarker.getMap() !== map) {
                            activeRowMarker.setMap(map);
                        }
                        var position = activeRowMarker.getPosition();
                        if (position) {
                            map.panTo(position);
                            if (map.getZoom() < 13) {
                                map.setZoom(13);
                            }
                        }
                    }
                }

                function resetForm() {
                    formNameInput.val('');
                    formLatInput.val('');
                    formLngInput.val('');
                    formAddressInput.val('');
                    formPlaceInput.val('');
                    formCoordsDisplay.text(coordinateHint);
                    formActiveCheckbox.prop('checked', true);
                    if (formMarker) {
                        formMarker.setMap(null);
                    }
                }

                function appendRow(name, lat, lng, isActive, address, placeId) {
                    var index = nextIndex++;
                    var row = $('<tr/>', {'data-index': index});
                    var nameCell = $('<td/>');
                    nameCell.append($('<input>', {type: 'hidden', name: 'pickup_locations[' + index + '][id]', value: ''}));
                    nameCell.append($('<input>', {type: 'hidden', 'class': 'js-pickup-lat', name: 'pickup_locations[' + index + '][lat]', value: lat}));
                    nameCell.append($('<input>', {type: 'hidden', 'class': 'js-pickup-lng', name: 'pickup_locations[' + index + '][lng]', value: lng}));
                    nameCell.append($('<input>', {type: 'hidden', 'class': 'js-pickup-address', name: 'pickup_locations[' + index + '][address]', value: address || ''}));
                    nameCell.append($('<input>', {type: 'hidden', 'class': 'js-pickup-place', name: 'pickup_locations[' + index + '][place_id]', value: placeId || ''}));
                    nameCell.append($('<input>', {type: 'hidden', 'class': 'js-pickup-active', name: 'pickup_locations[' + index + '][is_active]', value: isActive ? 1 : 0}));
                    nameCell.append($('<input>', {type: 'text', 'class': 'form-control js-pickup-name', name: 'pickup_locations[' + index + '][name]', value: name}));
                    row.append(nameCell);

                    var coordsCell = $('<td/>', {'class': 'text-center'});
                    coordsCell.append($('<div/>', {'class': 'small text-muted js-pickup-coordinate-display'}).text(noCoordinatesLabel));
                    row.append(coordsCell);

                    var statusCell = $('<td/>', {'class': 'text-center'});
                    var badge = $('<span/>', {'class': 'badge js-pickup-status'}).text(isActive ? statusActiveLabel : statusInactiveLabel);
                    badge.toggleClass('badge-success', isActive).toggleClass('badge-secondary', !isActive);
                    statusCell.append(badge);
                    row.append(statusCell);

                    var actionsCell = $('<td/>', {'class': 'text-center'});
                    actionsCell.append(
                        $('<button/>', {type: 'button', 'class': 'btn btn-sm btn-outline-primary js-pickup-set-coords', text: setCoordinatesLabel}).attr('data-index', index)
                    ).append(' ')
                        .append(
                            $('<button/>', {type: 'button', 'class': 'btn btn-sm btn-outline-secondary js-pickup-toggle', text: toggleStatusLabel}).attr('data-index', index)
                        ).append(' ')
                        .append(
                            $('<button/>', {type: 'button', 'class': 'btn btn-sm btn-outline-danger js-pickup-remove', text: removeLabel}).attr('data-index', index)
                        );
                    row.append(actionsCell);

                    tableBody.append(row);
                    updateRowCoordinates(row, lat, lng, false);
                    if (!isActive) {
                        row.find('.js-pickup-status').removeClass('badge-success').addClass('badge-secondary').text(statusInactiveLabel);
                        var marker = row.data('mapMarker');
                        if (marker) {
                            marker.setMap(null);
                        }
                    }
                    focusRowMarker(row);
                }

                map.addListener('click', function (event) {
                    var lat = event.latLng.lat();
                    var lng = event.latLng.lng();
                    if (activeRow) {
                        updateRowCoordinates(activeRow, lat, lng, true);
                        focusRowMarker(activeRow);
                    } else {
                        updateFormCoordinates(lat, lng, true);
                        focusFormMarker();
                    }
                });

                if (typeof google.maps.places !== 'undefined' && formNameInput.length) {
                    var autocomplete = new google.maps.places.Autocomplete(formNameInput[0], {
                        fields: ['formatted_address', 'geometry', 'name']
                    });
                    autocomplete.addListener('place_changed', function () {
                        var place = autocomplete.getPlace();
                        if (!place || !place.geometry || !place.geometry.location) {
                            return;
                        }
                        var lat = place.geometry.location.lat();
                        var lng = place.geometry.location.lng();
                        formNameInput.val(place.formatted_address || place.name || '');
                        formAddressInput.val(place.formatted_address || place.name || '');
                        formPlaceInput.val(place.place_id || '');
                        updateFormCoordinates(lat, lng, false);
                        focusFormMarker();
                    });
                }

                var bounds = new google.maps.LatLngBounds();
                var hasBounds = false;
                tableBody.find('tr').each(function () {
                    var row = $(this);
                    ensureRowMarker(row);
                    var lat = parseNumber(row.find('.js-pickup-lat').val());
                    var lng = parseNumber(row.find('.js-pickup-lng').val());
                    var isActive = parseInt(row.find('.js-pickup-active').val(), 10) === 1;
                    if (lat !== null && lng !== null && isActive) {
                        bounds.extend(new google.maps.LatLng(lat, lng));
                        hasBounds = true;
                    }
                });
                if (hasBounds) {
                    map.fitBounds(bounds);
                } else {
                    map.setCenter(defaultCenter);
                    map.setZoom(defaultZoom);
                }

                wrapper.on('click', '.js-pickup-add', function () {
                    var name = formNameInput.val().trim();
                    var lat = parseNumber(formLatInput.val());
                    var lng = parseNumber(formLngInput.val());
                    var isActive = formActiveCheckbox.is(':checked');

                    if (!name || lat === null || lng === null) {
                        alert(validationError);
                        return;
                    }

                    var address = (formAddressInput.val() || '').trim();
                    var placeId = (formPlaceInput.val() || '').trim();
                    appendRow(name, lat, lng, isActive, address, placeId);
                    resetForm();
                });

                wrapper.on('click', '.js-pickup-reset', function () {
                    resetForm();
                    clearActiveRow();
                });

                wrapper.on('click', '.js-pickup-remove', function () {
                    if (!confirm(confirmRemoveText)) {
                        return;
                    }
                    var row = $(this).closest('tr');
                    var marker = row.data('mapMarker');
                    if (marker) {
                        marker.setMap(null);
                    }
                    if (activeRow && activeRow[0] === row[0]) {
                        clearActiveRow();
                    }
                    row.remove();
                });

                wrapper.on('click', '.js-pickup-set-coords', function () {
                    var row = $(this).closest('tr');
                    if (!row.find('.js-pickup-lat').val() || !row.find('.js-pickup-lng').val()) {
                        updateRowCoordinates(row, defaultCenter.lat, defaultCenter.lng, false);
                    }
                    focusRowMarker(row);
                    mapElement.scrollIntoView({behavior: 'smooth', block: 'center'});
                });

                wrapper.on('click', '.js-pickup-toggle', function () {
                    var row = $(this).closest('tr');
                    var activeInput = row.find('.js-pickup-active');
                    var statusBadge = row.find('.js-pickup-status');
                    var current = parseInt(activeInput.val(), 10) === 1;
                    var nextState = !current;
                    activeInput.val(nextState ? 1 : 0);
                    statusBadge.toggleClass('badge-success', nextState).toggleClass('badge-secondary', !nextState);
                    statusBadge.text(nextState ? statusActiveLabel : statusInactiveLabel);
                    ensureRowMarker(row);
                });

                wrapper.on('input', '.js-pickup-name', function () {
                    var row = $(this).closest('tr');
                    var marker = row.data('mapMarker');
                    if (marker) {
                        marker.setTitle($(this).val());
                    }
                });
            }

            function initFallbackEngine() {
                var tableBody = wrapper.find('.js-pickup-table tbody');
                var mapElementId = 'pickup_locations_map';
                var markersData = [];
                try {
                    markersData = JSON.parse(wrapper.find('#' + mapElementId).attr('data-markers') || '[]');
                } catch (e) {
                    markersData = [];
                }
                var nextIndex = computeNextIndex(tableBody);
                var activeCoordinateTarget = null;
                var pickupMap = new BravoMapEngine(mapElementId, {
                    fitBounds: true,
                    center:[{{ $row->map_lat ?? setting_item('map_lat_default',51.505 ) }}, {{ $row->map_lng ?? setting_item('map_lng_default',-0.09 ) }}],
                    zoom: {{ $row->map_zoom ?? 8 }},
                    ready: function (engineMap) {
                        markersData.forEach(function (marker) {
                            if (marker.lat && marker.lng) {
                                engineMap.addMarker([marker.lat, marker.lng], {text: marker.name || ''});
                            }
                        });
                        engineMap.on('click', function (latLng) {
                            if (activeCoordinateTarget) {
                                updateRowCoordinates(activeCoordinateTarget, latLng);
                                engineMap.addMarker(latLng, {text: activeCoordinateTarget.find('.js-pickup-name').val() || ''});
                                activeCoordinateTarget = null;
                                wrapper.find('.js-pickup-table tr').removeClass('table-active');
                            } else {
                                setFormCoordinates(latLng);
                                engineMap.addMarker(latLng, {text: wrapper.find('.js-pickup-form-name').val() || ''});
                            }
                        });
                    }
                });

                function setFormCoordinates(latLng) {
                    wrapper.find('.js-pickup-form-lat').val(latLng[0]);
                    wrapper.find('.js-pickup-form-lng').val(latLng[1]);
                    wrapper.find('.js-pickup-form-address').val('');
                    wrapper.find('.js-pickup-form-place').val('');
                    wrapper.find('.js-pickup-form-coords').text(latLng[0].toFixed(6) + ', ' + latLng[1].toFixed(6));
                }

                function updateRowCoordinates(row, latLng) {
                    row.find('.js-pickup-lat').val(latLng[0]);
                    row.find('.js-pickup-lng').val(latLng[1]);
                    if (!row.find('.js-pickup-address').val()) {
                        row.find('.js-pickup-address').val(row.find('.js-pickup-name').val());
                    }
                    row.find('.js-pickup-place').val('');
                    row.find('.js-pickup-coordinate-display').text(latLng[0].toFixed(6) + ', ' + latLng[1].toFixed(6));
                }

                function resetForm() {
                    wrapper.find('.js-pickup-form-name').val('');
                    wrapper.find('.js-pickup-form-lat').val('');
                    wrapper.find('.js-pickup-form-lng').val('');
                    wrapper.find('.js-pickup-form-address').val('');
                    wrapper.find('.js-pickup-form-place').val('');
                    wrapper.find('.js-pickup-form-coords').text(coordinateHint);
                    wrapper.find('.js-pickup-form-active').prop('checked', true);
                }

                wrapper.on('click', '.js-pickup-add', function () {
                    var name = wrapper.find('.js-pickup-form-name').val().trim();
                    var lat = wrapper.find('.js-pickup-form-lat').val();
                    var lng = wrapper.find('.js-pickup-form-lng').val();
                    var address = wrapper.find('.js-pickup-form-address').val().trim();
                    var placeId = wrapper.find('.js-pickup-form-place').val().trim();
                    var isActive = wrapper.find('.js-pickup-form-active').is(':checked');

                    if (!name || !lat || !lng) {
                        alert(validationError);
                        return;
                    }

                    var rowHtml = '\n<tr data-index="' + nextIndex + '">\n' +
                        '    <td>\n' +
                        '        <input type="hidden" name="pickup_locations[' + nextIndex + '][id]" value="">\n' +
                        '        <input type="hidden" name="pickup_locations[' + nextIndex + '][lat]" class="js-pickup-lat" value="' + lat + '">\n' +
                        '        <input type="hidden" name="pickup_locations[' + nextIndex + '][lng]" class="js-pickup-lng" value="' + lng + '">\n' +
                        '        <input type="hidden" name="pickup_locations[' + nextIndex + '][address]" class="js-pickup-address" value="' + (address ? address.replace(/"/g, '&quot;') : name.replace(/"/g, '&quot;')) + '">\n' +
                        '        <input type="hidden" name="pickup_locations[' + nextIndex + '][place_id]" class="js-pickup-place" value="' + placeId.replace(/"/g, '&quot;') + '">\n' +
                        '        <input type="hidden" name="pickup_locations[' + nextIndex + '][is_active]" class="js-pickup-active" value="' + (isActive ? 1 : 0) + '">\n' +
                        '        <input type="text" name="pickup_locations[' + nextIndex + '][name]" class="form-control js-pickup-name" value="' + name.replace(/"/g, '&quot;') + '">\n' +
                        '    </td>\n' +
                        '    <td class="text-center">\n' +
                        '        <div class="small text-muted js-pickup-coordinate-display">' + parseFloat(lat).toFixed(6) + ', ' + parseFloat(lng).toFixed(6) + '</div>\n' +
                        '    </td>\n' +
                        '    <td class="text-center">\n' +
                        '        <span class="badge js-pickup-status ' + (isActive ? 'badge-success' : 'badge-secondary') + '">' + (isActive ? statusActiveLabel : statusInactiveLabel) + '</span>\n' +
                        '    </td>\n' +
                        '    <td class="text-center">\n' +
                        '        <button type="button" class="btn btn-sm btn-outline-primary js-pickup-set-coords" data-index="' + nextIndex + '">' + setCoordinatesLabel + '</button>\n' +
                        '        <button type="button" class="btn btn-sm btn-outline-secondary js-pickup-toggle" data-index="' + nextIndex + '">' + toggleStatusLabel + '</button>\n' +
                        '        <button type="button" class="btn btn-sm btn-outline-danger js-pickup-remove" data-index="' + nextIndex + '">' + removeLabel + '</button>\n' +
                        '    </td>\n' +
                        '</tr>';

                    tableBody.append(rowHtml);
                    nextIndex++;
                    resetForm();
                });

                wrapper.on('click', '.js-pickup-reset', function () {
                    resetForm();
                });

                wrapper.on('click', '.js-pickup-remove', function () {
                    if (!confirm(confirmRemoveText)) {
                        return;
                    }
                    $(this).closest('tr').remove();
                });

                wrapper.on('click', '.js-pickup-set-coords', function () {
                    var row = $(this).closest('tr');
                    activeCoordinateTarget = row;
                    wrapper.find('.js-pickup-table tr').removeClass('table-active');
                    row.addClass('table-active');
                    alert(clickMapToSet);
                });

                wrapper.on('click', '.js-pickup-toggle', function () {
                    var row = $(this).closest('tr');
                    var activeInput = row.find('.js-pickup-active');
                    var statusBadge = row.find('.js-pickup-status');
                    var current = parseInt(activeInput.val(), 10) === 1;
                    var nextState = !current;
                    activeInput.val(nextState ? 1 : 0);
                    statusBadge.toggleClass('badge-success', nextState).toggleClass('badge-secondary', !nextState);
                    statusBadge.text(nextState ? statusActiveLabel : statusInactiveLabel);
                });
            }
        });
    </script>
@endpush
