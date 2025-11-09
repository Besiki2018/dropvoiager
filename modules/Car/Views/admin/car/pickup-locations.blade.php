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
            <div class="col-md-3">
                <label class="form-label">{{ __('transfers.admin.pickups.form.name') }}</label>
                <input type="text" class="form-control js-pickup-form-name" placeholder="{{ __('transfers.admin.pickups.form.name_placeholder') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('transfers.admin.pickups.form.address') }}</label>
                <input type="text" class="form-control js-pickup-form-address" placeholder="{{ __('transfers.admin.pickups.form.address_placeholder') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('transfers.admin.pickups.form.base_price') }}</label>
                <input type="number" step="0.01" class="form-control js-pickup-form-base-price" placeholder="0.00">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('transfers.admin.pickups.form.coefficient') }}</label>
                <input type="number" step="0.01" class="form-control js-pickup-form-coefficient" value="1">
            </div>
            <div class="col-md-2">
                <div class="fw-bold">{{ __('transfers.admin.pickups.form.coordinates') }}</div>
                <div class="small text-muted js-pickup-form-coords">{{ __('transfers.admin.pickups.form.coordinates_hint') }}</div>
                <input type="hidden" class="js-pickup-form-lat">
                <input type="hidden" class="js-pickup-form-lng">
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-primary js-pickup-add">{{ __('transfers.admin.pickups.form.add_button') }}</button>
                <button type="button" class="btn btn-outline-secondary js-pickup-reset">{{ __('transfers.admin.pickups.form.reset_button') }}</button>
            </div>
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-bordered align-middle js-pickup-table">
                <thead>
                    <tr>
                        <th>{{ __('transfers.admin.pickups.table.name') }}</th>
                        <th>{{ __('transfers.admin.pickups.table.address') }}</th>
                        <th class="text-center" style="width: 120px;">{{ __('transfers.admin.pickups.table.base_price') }}</th>
                        <th class="text-center" style="width: 120px;">{{ __('transfers.admin.pickups.table.coefficient') }}</th>
                        <th class="text-center" style="width: 180px;">{{ __('transfers.admin.pickups.table.coordinates') }}</th>
                        <th style="width: 160px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formPickupLocations as $index => $location)
                        <tr data-index="{{ $index }}">
                            <td>
                                <input type="hidden" name="pickup_locations[{{ $index }}][id]" value="{{ $location['id'] ?? '' }}">
                                <input type="hidden" name="pickup_locations[{{ $index }}][lat]" class="form-control js-pickup-lat" value="{{ $location['lat'] ?? '' }}">
                                <input type="hidden" name="pickup_locations[{{ $index }}][lng]" class="form-control js-pickup-lng" value="{{ $location['lng'] ?? '' }}">
                                <input type="text" name="pickup_locations[{{ $index }}][name]" class="form-control js-pickup-name" value="{{ $location['name'] ?? '' }}">
                            </td>
                            <td>
                                <input type="text" name="pickup_locations[{{ $index }}][address]" class="form-control js-pickup-address" value="{{ $location['address'] ?? '' }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="pickup_locations[{{ $index }}][base_price]" class="form-control js-pickup-base-price" value="{{ $location['base_price'] ?? '' }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="pickup_locations[{{ $index }}][price_coefficient]" class="form-control js-pickup-coefficient" value="{{ $location['price_coefficient'] ?? 1 }}">
                            </td>
                            <td>
                                <div class="small text-muted js-pickup-coordinate-display">{{ ($location['lat'] ?? '') && ($location['lng'] ?? '') ? ($location['lat'] . ', ' . $location['lng']) : __('transfers.admin.pickups.table.no_coordinates') }}</div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary js-pickup-set-coords" data-index="{{ $index }}">{{ __('transfers.admin.pickups.actions.set_coordinates') }}</button>
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
                            wrapper.find('.js-pickup-form-lat').val(latLng[0]);
                            wrapper.find('.js-pickup-form-lng').val(latLng[1]);
                            wrapper.find('.js-pickup-form-coords').text(latLng[0].toFixed(6) + ', ' + latLng[1].toFixed(6));
                            engineMap.addMarker(latLng, {text: wrapper.find('.js-pickup-form-name').val() || ''});
                        }
                    });
                    if (bookingCore.map_provider === 'gmap') {
                        engineMap.searchBox(wrapper.find('.js-pickup-form-address'), function (latLng) {
                            wrapper.find('.js-pickup-form-lat').val(latLng[0]);
                            wrapper.find('.js-pickup-form-lng').val(latLng[1]);
                            wrapper.find('.js-pickup-form-coords').text(latLng[0].toFixed(6) + ', ' + latLng[1].toFixed(6));
                            engineMap.addMarker(latLng, {text: wrapper.find('.js-pickup-form-name').val() || ''});
                        });
                    }
                }
            });

            function updateRowCoordinates(row, latLng) {
                row.find('.js-pickup-lat').val(latLng[0]);
                row.find('.js-pickup-lng').val(latLng[1]);
                row.find('.js-pickup-coordinate-display').text(latLng[0].toFixed(6) + ', ' + latLng[1].toFixed(6));
            }

            function resetForm() {
                wrapper.find('.js-pickup-form-name').val('');
                wrapper.find('.js-pickup-form-address').val('');
                wrapper.find('.js-pickup-form-base-price').val('');
                wrapper.find('.js-pickup-form-coefficient').val('1');
                wrapper.find('.js-pickup-form-lat').val('');
                wrapper.find('.js-pickup-form-lng').val('');
                wrapper.find('.js-pickup-form-coords').text('{{ __('transfers.admin.pickups.form.coordinates_hint') }}');
            }

            wrapper.on('click', '.js-pickup-add', function () {
                var name = wrapper.find('.js-pickup-form-name').val().trim();
                var address = wrapper.find('.js-pickup-form-address').val().trim();
                var basePrice = wrapper.find('.js-pickup-form-base-price').val();
                var coefficient = wrapper.find('.js-pickup-form-coefficient').val();
                var lat = wrapper.find('.js-pickup-form-lat').val();
                var lng = wrapper.find('.js-pickup-form-lng').val();

                if (!name || !lat || !lng) {
                    alert('{{ __('transfers.admin.pickups.form.validation_error') }}');
                    return;
                }

                var rowHtml = `
                    <tr data-index="${nextIndex}">
                        <td>
                            <input type="hidden" name="pickup_locations[${nextIndex}][id]" value="">
                            <input type="hidden" name="pickup_locations[${nextIndex}][lat]" class="form-control js-pickup-lat" value="${lat}">
                            <input type="hidden" name="pickup_locations[${nextIndex}][lng]" class="form-control js-pickup-lng" value="${lng}">
                            <input type="text" name="pickup_locations[${nextIndex}][name]" class="form-control js-pickup-name" value="${name}">
                        </td>
                        <td>
                            <input type="text" name="pickup_locations[${nextIndex}][address]" class="form-control js-pickup-address" value="${address}">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="pickup_locations[${nextIndex}][base_price]" class="form-control js-pickup-base-price" value="${basePrice}">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="pickup_locations[${nextIndex}][price_coefficient]" class="form-control js-pickup-coefficient" value="${coefficient || 1}">
                        </td>
                        <td>
                            <div class="small text-muted js-pickup-coordinate-display">${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}</div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary js-pickup-set-coords" data-index="${nextIndex}">{{ __('transfers.admin.pickups.actions.set_coordinates') }}</button>
                            <button type="button" class="btn btn-sm btn-outline-danger js-pickup-remove" data-index="${nextIndex}">{{ __('transfers.admin.pickups.actions.remove') }}</button>
                        </td>
                    </tr>`;
                tableBody.append(rowHtml);
                nextIndex++;
                resetForm();
            });

            wrapper.on('click', '.js-pickup-reset', function () {
                resetForm();
                activeCoordinateTarget = null;
                wrapper.find('.js-pickup-table tr').removeClass('table-active');
            });

            wrapper.on('click', '.js-pickup-remove', function () {
                var row = $(this).closest('tr');
                if (confirm('{{ __('transfers.admin.pickups.actions.confirm_remove') }}')) {
                    row.remove();
                }
            });

            wrapper.on('click', '.js-pickup-set-coords', function () {
                var row = $(this).closest('tr');
                activeCoordinateTarget = row;
                wrapper.find('.js-pickup-table tr').removeClass('table-active');
                row.addClass('table-active');
                alert('{{ __('transfers.admin.pickups.actions.click_map_to_set') }}');
            });
        });
    </script>
@endpush
