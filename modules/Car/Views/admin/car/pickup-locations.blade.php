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
            var tableBody = wrapper.find('.js-pickup-table tbody');
            var mapElementId = 'pickup_locations_map';
            var markersData = [];
            try {
                markersData = JSON.parse(wrapper.find('#' + mapElementId).attr('data-markers') || '[]');
            } catch (e) {
                markersData = [];
            }
            var nextIndex = tableBody.find('tr').length;
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
                wrapper.find('.js-pickup-form-coords').text(latLng[0].toFixed(6) + ', ' + latLng[1].toFixed(6));
            }

            function updateRowCoordinates(row, latLng) {
                row.find('.js-pickup-lat').val(latLng[0]);
                row.find('.js-pickup-lng').val(latLng[1]);
                row.find('.js-pickup-coordinate-display').text(latLng[0].toFixed(6) + ', ' + latLng[1].toFixed(6));
            }

            function resetForm() {
                wrapper.find('.js-pickup-form-name').val('');
                wrapper.find('.js-pickup-form-lat').val('');
                wrapper.find('.js-pickup-form-lng').val('');
                wrapper.find('.js-pickup-form-coords').text('{{ __('transfers.admin.pickups.form.coordinates_hint') }}');
                wrapper.find('.js-pickup-form-active').prop('checked', true);
            }

            wrapper.on('click', '.js-pickup-add', function () {
                var name = wrapper.find('.js-pickup-form-name').val().trim();
                var lat = wrapper.find('.js-pickup-form-lat').val();
                var lng = wrapper.find('.js-pickup-form-lng').val();
                var isActive = wrapper.find('.js-pickup-form-active').is(':checked');

                if (!name || !lat || !lng) {
                    alert('{{ __('transfers.admin.pickups.form.validation_error') }}');
                    return;
                }

                var rowHtml = '\n<tr data-index="' + nextIndex + '">\n' +
                    '    <td>\n' +
                    '        <input type="hidden" name="pickup_locations[' + nextIndex + '][id]" value="">\n' +
                    '        <input type="hidden" name="pickup_locations[' + nextIndex + '][lat]" class="js-pickup-lat" value="' + lat + '">\n' +
                    '        <input type="hidden" name="pickup_locations[' + nextIndex + '][lng]" class="js-pickup-lng" value="' + lng + '">\n' +
                    '        <input type="hidden" name="pickup_locations[' + nextIndex + '][is_active]" class="js-pickup-active" value="' + (isActive ? 1 : 0) + '">\n' +
                    '        <input type="text" name="pickup_locations[' + nextIndex + '][name]" class="form-control js-pickup-name" value="' + name.replace(/"/g, '&quot;') + '">\n' +
                    '    </td>\n' +
                    '    <td class="text-center">\n' +
                    '        <div class="small text-muted js-pickup-coordinate-display">' + parseFloat(lat).toFixed(6) + ', ' + parseFloat(lng).toFixed(6) + '</div>\n' +
                    '    </td>\n' +
                    '    <td class="text-center">\n' +
                    '        <span class="badge js-pickup-status ' + (isActive ? 'badge-success' : 'badge-secondary') + '">' + (isActive ? '{{ __('transfers.admin.pickups.table.status_active') }}' : '{{ __('transfers.admin.pickups.table.status_inactive') }}') + '</span>\n' +
                    '    </td>\n' +
                    '    <td class="text-center">\n' +
                    '        <button type="button" class="btn btn-sm btn-outline-primary js-pickup-set-coords" data-index="' + nextIndex + '">{{ __('transfers.admin.pickups.actions.set_coordinates') }}</button>\n' +
                    '        <button type="button" class="btn btn-sm btn-outline-secondary js-pickup-toggle" data-index="' + nextIndex + '">{{ __('transfers.admin.pickups.actions.toggle_status') }}</button>\n' +
                    '        <button type="button" class="btn btn-sm btn-outline-danger js-pickup-remove" data-index="' + nextIndex + '">{{ __('transfers.admin.pickups.actions.remove') }}</button>\n' +
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
                if (!confirm('{{ __('transfers.admin.pickups.actions.confirm_remove') }}')) {
                    return;
                }
                $(this).closest('tr').remove();
            });

            wrapper.on('click', '.js-pickup-set-coords', function () {
                var row = $(this).closest('tr');
                activeCoordinateTarget = row;
                wrapper.find('.js-pickup-table tr').removeClass('table-active');
                row.addClass('table-active');
                alert('{{ __('transfers.admin.pickups.actions.click_map_to_set') }}');
            });

            wrapper.on('click', '.js-pickup-toggle', function () {
                var row = $(this).closest('tr');
                var activeInput = row.find('.js-pickup-active');
                var statusBadge = row.find('.js-pickup-status');
                var current = parseInt(activeInput.val(), 10) === 1;
                var nextState = !current;
                activeInput.val(nextState ? 1 : 0);
                statusBadge.toggleClass('badge-success', nextState).toggleClass('badge-secondary', !nextState);
                statusBadge.text(nextState ? '{{ __('transfers.admin.pickups.table.status_active') }}' : '{{ __('transfers.admin.pickups.table.status_inactive') }}');
            });
        });
    </script>
@endpush
