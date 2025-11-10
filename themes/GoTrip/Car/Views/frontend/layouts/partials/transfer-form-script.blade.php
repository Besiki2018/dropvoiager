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
