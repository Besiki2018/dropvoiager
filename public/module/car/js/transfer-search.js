(function ($) {
    'use strict';

    function initAutocomplete($input) {
        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
            return;
        }
        var locationKey = $input.data('location');
        var $form = $input.closest('form');
        var $latInput = $form.find('input[data-role="lat"][data-location="' + locationKey + '"]');
        var $lngInput = $form.find('input[data-role="lng"][data-location="' + locationKey + '"]');

        var autocomplete = new google.maps.places.Autocomplete($input[0], {
            fields: ['formatted_address', 'geometry']
        });

        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place || !place.geometry) {
                return;
            }
            if (place.formatted_address) {
                $input.val(place.formatted_address);
            }
            if ($latInput.length) {
                $latInput.val(place.geometry.location.lat());
            }
            if ($lngInput.length) {
                $lngInput.val(place.geometry.location.lng());
            }
        });
    }

    function bindClearButtons($form) {
        $form.on('click', '[data-action="clear"]', function (event) {
            event.preventDefault();
            var $button = $(this);
            var $wrapper = $button.closest('.position-relative');
            var $input = $wrapper.find('.js-transfer-address');
            if (!$input.length) {
                return;
            }
            var locationKey = $input.data('location');
            $input.val('');
            $form.find('input[data-role="lat"][data-location="' + locationKey + '"]').val('');
            $form.find('input[data-role="lng"][data-location="' + locationKey + '"]').val('');
        });
    }

    function bindDatetime($container) {
        var $dateInput = $container.find('[data-role="transfer-date"]');
        var $timeInput = $container.find('[data-role="transfer-time"]');
        var $isoInput = $container.find('input[data-role="transfer-datetime"]');
        var $startInput = $container.find('input[name="start"]');
        var $endInput = $container.find('input[name="end"]');
        var $rangeInput = $container.find('input[name="date"]');

        function updateDatetime() {
            var dateValue = $dateInput.val();
            var timeValue = $timeInput.val() || '00:00';
            if (timeValue.length !== 5) {
                timeValue = timeValue.substring(0, 5);
            }

            if ($startInput.length) {
                $startInput.val(dateValue || '');
            }
            if ($endInput.length) {
                $endInput.val(dateValue || '');
            }
            if ($rangeInput.length) {
                $rangeInput.val(dateValue ? dateValue + ' - ' + dateValue : '');
            }

            if (!dateValue) {
                $isoInput.val('');
                return;
            }

            $isoInput.val(dateValue + 'T' + timeValue + ':00+04:00');
        }

        $dateInput.on('change', updateDatetime);
        $timeInput.on('change', updateDatetime);
        updateDatetime();
    }

    $(function () {
        var $forms = $('.bravo_form_search');
        if (!$forms.length) {
            return;
        }

        $forms.each(function () {
            var $form = $(this);
            bindClearButtons($form);

            $form.find('.js-transfer-datetime').each(function () {
                bindDatetime($(this));
            });

            $form.find('.js-transfer-address').each(function () {
                initAutocomplete($(this));
            });
        });
    });
})(jQuery);
