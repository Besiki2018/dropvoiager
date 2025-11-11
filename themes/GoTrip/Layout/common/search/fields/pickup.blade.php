@php
    $pickupAddress = request()->input('pickup_address');
    $pickupName = request()->input('pickup_name');
    $pickupLat = request()->input('pickup_lat');
    $pickupLng = request()->input('pickup_lng');
    $pickupDisplay = request()->input('pickup_display', $pickupName ?: $pickupAddress);
    $pickupPayload = request()->input('pickup_payload');
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
            <input type="hidden" name="pickup_payload" class="js-transfer-pickup-payload" value="{{ $pickupPayload }}">
        </div>
    </div>
</div>
