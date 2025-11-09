@php
    $pickup = request()->input('pickup', []);
    $dropoff = request()->input('dropoff', []);
@endphp
<div class="filter-item">
    <div class="form-group">
        <label class="text-13 text-light-1 lh-16 mb-5 d-block">{{ __('From (pickup)') }}</label>
        <input type="text"
               name="pickup[address]"
               value="{{ $pickup['address'] ?? '' }}"
               class="form-control py-10 px-15 js-transfer-address"
               placeholder="{{ __('Pickup location') }}"
               autocomplete="off"
               data-role="address"
               data-location="pickup">
        <input type="hidden" name="pickup[lat]" value="{{ $pickup['lat'] ?? '' }}" data-role="lat" data-location="pickup">
        <input type="hidden" name="pickup[lng]" value="{{ $pickup['lng'] ?? '' }}" data-role="lng" data-location="pickup">
    </div>
    <div class="form-group mt-10">
        <label class="text-13 text-light-1 lh-16 mb-5 d-block">{{ __('To (destination)') }}</label>
        <input type="text"
               name="dropoff[address]"
               value="{{ $dropoff['address'] ?? '' }}"
               class="form-control py-10 px-15 js-transfer-address"
               placeholder="{{ __('Destination location') }}"
               autocomplete="off"
               data-role="address"
               data-location="dropoff">
        <input type="hidden" name="dropoff[lat]" value="{{ $dropoff['lat'] ?? '' }}" data-role="lat" data-location="dropoff">
        <input type="hidden" name="dropoff[lng]" value="{{ $dropoff['lng'] ?? '' }}" data-role="lng" data-location="dropoff">
    </div>
</div>

@once
    @push('js')
        <script src="{{ asset('module/car/js/transfer-search.js?_v='.config('app.asset_version')) }}"></script>
    @endpush
@endonce
