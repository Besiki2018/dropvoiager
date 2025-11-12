@php
    $dropoffAddress = request()->input('dropoff_address');
    $dropoffName = request()->input('dropoff_name');
    $dropoffLat = request()->input('dropoff_lat');
    $dropoffLng = request()->input('dropoff_lng');
    $dropoffDisplay = request()->input('dropoff_display', $dropoffName ?: $dropoffAddress);
    $dropoffPlaceId = request()->input('dropoff_place_id');
    $dropoffJson = request()->input('dropoff_json');
    if ($dropoffJson) {
        try {
            $parsedDropoff = json_decode($dropoffJson, true);
            if (is_array($parsedDropoff)) {
                $dropoffAddress = $dropoffAddress ?: ($parsedDropoff['address'] ?? '');
                $dropoffName = $dropoffName ?: ($parsedDropoff['name'] ?? $dropoffAddress);
                if (empty($dropoffPlaceId) && !empty($parsedDropoff['place_id'])) {
                    $dropoffPlaceId = $parsedDropoff['place_id'];
                }
                if (empty($dropoffLat) && !empty($parsedDropoff['lat'])) {
                    $dropoffLat = $parsedDropoff['lat'];
                }
                if (empty($dropoffLng) && !empty($parsedDropoff['lng'])) {
                    $dropoffLng = $parsedDropoff['lng'];
                }
                if (empty($dropoffDisplay)) {
                    $dropoffDisplay = $parsedDropoff['display_name'] ?? $dropoffName ?? $dropoffAddress;
                }
            }
        } catch (\Exception $exception) {
        }
    }
    if (empty($dropoffDisplay)) {
        $dropoffDisplay = $dropoffName ?: $dropoffAddress;
    }
@endphp
<div class="searchMenu-loc item">
    <div>
        <h4 class="text-15 fw-500 ls-2 lh-16">{{ $field['title'] }}</h4>
        <div class="text-15 text-light-1 ls-2 lh-16">
            <input type="text"
                   name="dropoff_display"
                   class="js-transfer-dropoff-display"
                   value="{{ $dropoffDisplay }}"
                   placeholder="{{ __('Enter drop-off location') }}"
                   autocomplete="off">
            <input type="hidden" name="dropoff_address" class="js-transfer-dropoff-address" value="{{ $dropoffAddress }}">
            <input type="hidden" name="dropoff_name" class="js-transfer-dropoff-name" value="{{ $dropoffName }}">
            <input type="hidden" name="dropoff_lat" class="js-transfer-dropoff-lat" value="{{ $dropoffLat }}">
            <input type="hidden" name="dropoff_lng" class="js-transfer-dropoff-lng" value="{{ $dropoffLng }}">
            <input type="hidden" name="dropoff_place_id" class="js-transfer-dropoff-place-id" value="{{ $dropoffPlaceId }}">
            <input type="hidden" name="dropoff_json" class="js-transfer-dropoff-json" value="{{ $dropoffJson }}">
        </div>
    </div>
</div>
