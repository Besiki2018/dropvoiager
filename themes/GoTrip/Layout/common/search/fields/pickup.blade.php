@php
    $pickupAddress = request()->input('pickup_address');
    $pickupName = request()->input('pickup_name');
    $pickupLat = request()->input('pickup_lat');
    $pickupLng = request()->input('pickup_lng');
    $pickupDisplay = request()->input('pickup_display', $pickupName ?: $pickupAddress);
    $pickupPayload = request()->input('pickup_payload');
    $pickupPlaceId = request()->input('pickup_place_id');
    if ($pickupPayload) {
        try {
            $parsedPickup = json_decode($pickupPayload, true);
            if (is_array($parsedPickup)) {
                $pickupAddress = $pickupAddress ?: ($parsedPickup['address'] ?? '');
                $pickupName = $pickupName ?: ($parsedPickup['name'] ?? $pickupAddress);
                $pickupDisplay = $pickupDisplay ?: ($parsedPickup['display_name'] ?? $pickupName ?? $pickupAddress);
                if (empty($pickupPlaceId) && !empty($parsedPickup['place_id'])) {
                    $pickupPlaceId = $parsedPickup['place_id'];
                }
                if (empty($pickupLat) && !empty($parsedPickup['lat'])) {
                    $pickupLat = $parsedPickup['lat'];
                }
                if (empty($pickupLng) && !empty($parsedPickup['lng'])) {
                    $pickupLng = $parsedPickup['lng'];
                }
            }
        } catch (\Exception $exception) {
        }
    }
    if (empty($pickupDisplay)) {
        $pickupDisplay = $pickupName ?: $pickupAddress;
    }
@endphp
<div class="searchMenu-loc item">
    <div>
        <h4 class="text-15 fw-500 ls-2 lh-16">{{ $field['title'] }}</h4>
        <div class="text-15 text-light-1 ls-2 lh-16">
            <input type="text"
                   name="pickup_display"
                   class="js-transfer-pickup-display"
                   value="{{ $pickupDisplay }}"
                   placeholder="{{ __('Enter pickup location') }}"
                   autocomplete="off">
            <input type="hidden" name="pickup_address" class="js-transfer-pickup-address" value="{{ $pickupAddress }}">
            <input type="hidden" name="pickup_name" class="js-transfer-pickup-name" value="{{ $pickupName }}">
            <input type="hidden" name="pickup_lat" class="js-transfer-pickup-lat" value="{{ $pickupLat }}">
            <input type="hidden" name="pickup_lng" class="js-transfer-pickup-lng" value="{{ $pickupLng }}">
            <input type="hidden" name="pickup_place_id" class="js-transfer-pickup-place-id" value="{{ $pickupPlaceId }}">
            <input type="hidden" name="pickup_payload" class="js-transfer-pickup-payload" value="{{ $pickupPayload }}">
        </div>
    </div>
</div>
@once('transfer-form-script')
    @push('js')
        <script src="{{ asset('js/transfer-form.js?_ver='.config('app.asset_version')) }}"></script>
    @endpush
@endonce
