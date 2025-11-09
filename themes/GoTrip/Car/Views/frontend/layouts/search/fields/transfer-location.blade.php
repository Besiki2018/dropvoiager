@php
    $pickup = request()->input('pickup', []);
    $dropoff = request()->input('dropoff', []);
@endphp
<div class="searchMenu-location item">
    <h4 class="text-15 fw-500 ls-2 lh-16">{{ $field['title'] ?? __('Locations') }}</h4>
    <div class="transfer-location__wrapper row x-gap-20 y-gap-20">
        <div class="col-12 col-md-6">
            <label class="text-13 text-light-1 lh-16 mb-5">{{ __('From (pickup location)') }}</label>
            <div class="position-relative">
                <input type="text"
                       name="pickup[address]"
                       value="{{ $pickup['address'] ?? '' }}"
                       class="form-control rounded-4 py-15 px-20 js-transfer-address"
                       placeholder="{{ __('Select pickup location') }}"
                       autocomplete="off"
                       data-role="address"
                       data-location="pickup">
                <button class="button -link text-13 text-dark-1 position-absolute top-50 end-0 translate-middle-y mr-10"
                        type="button"
                        data-action="clear"
                        aria-label="{{ __('Clear pickup location') }}">
                    {{ __('Clear') }}
                </button>
            </div>
            <input type="hidden" name="pickup[lat]" value="{{ $pickup['lat'] ?? '' }}" data-role="lat" data-location="pickup">
            <input type="hidden" name="pickup[lng]" value="{{ $pickup['lng'] ?? '' }}" data-role="lng" data-location="pickup">
        </div>
        <div class="col-12 col-md-6">
            <label class="text-13 text-light-1 lh-16 mb-5">{{ __('To (destination)') }}</label>
            <div class="position-relative">
                <input type="text"
                       name="dropoff[address]"
                       value="{{ $dropoff['address'] ?? '' }}"
                       class="form-control rounded-4 py-15 px-20 js-transfer-address"
                       placeholder="{{ __('Select dropoff location') }}"
                       autocomplete="off"
                       data-role="address"
                       data-location="dropoff">
                <button class="button -link text-13 text-dark-1 position-absolute top-50 end-0 translate-middle-y mr-10"
                        type="button"
                        data-action="clear"
                        aria-label="{{ __('Clear destination location') }}">
                    {{ __('Clear') }}
                </button>
            </div>
            <input type="hidden" name="dropoff[lat]" value="{{ $dropoff['lat'] ?? '' }}" data-role="lat" data-location="dropoff">
            <input type="hidden" name="dropoff[lng]" value="{{ $dropoff['lng'] ?? '' }}" data-role="lng" data-location="dropoff">
        </div>
    </div>
</div>

@once
    @push('js')
        <script src="{{ asset('module/car/js/transfer-search.js?_v='.config('app.asset_version')) }}"></script>
    @endpush
@endonce
