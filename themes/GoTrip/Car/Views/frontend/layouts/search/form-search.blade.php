@php $style = $style ?? 'default';
    $classes = ' form-search-all-service mainSearch bg-white px-10 py-10 lg:px-20 lg:pt-5 lg:pb-20 rounded-4 mt-30';
    $button_classes = " -dark-1 py-15 col-12 bg-blue-1 text-white w-100 rounded-4";
    if($style == 'sidebar'){
        $classes = ' form-search-sidebar';
        $button_classes = " -dark-1 py-15 col-12 bg-blue-1 h-60 text-white w-100 rounded-4";
    }
    if($style == 'normal'){
        $classes = ' px-10 py-10 lg:px-20 lg:pt-5 lg:pb-20 rounded-100 form-search-all-service mainSearch -w-900 bg-white';
        $button_classes = " -dark-1 py-15 h-60 col-12 rounded-100 bg-blue-1 text-white w-100";
    }
    if($style == 'normal2'){
        $classes = 'mainSearch bg-white pr-20 py-20 lg:px-20 lg:pt-5 lg:pb-20 rounded-4 shadow-1';
        $button_classes = " -dark-1 py-15 h-60 col-12 rounded-100 bg-blue-1 text-white w-100";
    }
    if($style == 'carousel_v2'){
        $classes = " w-100";
        $button_classes = " -dark-1 py-15 px-35 h-60 col-12 rounded-4 bg-yellow-1 text-dark-1";
    }
    if($style == 'map'){
        $classes = " w-100";
        $button_classes = " -dark-1 size-60 col-12 rounded-4 bg-blue-1 text-white";
    }
    if($style == 'car_carousel'){
        $classes = " mainSearch -col-5 -w-1070 mx-auto bg-white pr-20 py-20 lg:px-20 lg:pt-5 lg:pb-20 rounded-4 shadow-1";
        $button_classes = " -dark-1 py-15 px-35 h-60 col-12 rounded-4 bg-dark-1 text-white";
    }
@endphp

@php
    $pickupLocations = collect($pickup_locations ?? $pickupLocations ?? []);
    $selectedPickupLocation = $selected_pickup_location ?? null;
    $selectedPickupLocationId = $selected_pickup_location_id ?? ($selectedPickupLocation->id ?? request()->input('pickup_location_id'));
    if (!$selectedPickupLocation && $selectedPickupLocationId) {
        $selectedPickupLocation = $pickupLocations->firstWhere('id', (int) $selectedPickupLocationId);
    }
    $dropoffData = $selected_dropoff ?? request()->input('dropoff', []);
    $selectedPickupPayload = $selected_pickup_payload ?? ($selectedPickupLocation ? $selectedPickupLocation->toFrontendArray() : null);
    $userPickupInput = $selected_user_pickup ?? request()->input('user_pickup', []);
    if (is_string($userPickupInput)) {
        $decodedUserPickupInput = json_decode($userPickupInput, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $userPickupInput = $decodedUserPickupInput;
        }
    }
    if (!is_array($userPickupInput)) {
        $userPickupInput = [];
    }
    $userPickupPayload = $selected_user_pickup_payload ?? $userPickupInput;
    $transferDatetime = request()->input('transfer_datetime');
    $transferDate = '';
    $transferTime = '';
    if($transferDatetime){
        try {
            $transferCarbon = \Carbon\Carbon::parse($transferDatetime, 'Asia/Tbilisi')->setTimezone('Asia/Tbilisi');
            $transferDate = $transferCarbon->toDateString();
            $transferTime = $transferCarbon->format('H:i');
        } catch (\Exception $exception) {
            $transferDate = '';
            $transferTime = '';
        }
    }
@endphp

<form action="{{ route("car.search") }}" class="gotrip_form_search bravo_form_search bravo_form form-search-all-service form js-transfer-form {{$classes }}" method="get">
    @if( !empty(Request::query('_layout')) )
        <input type="hidden" name="_layout" value="{{Request::query('_layout')}}">
    @endif
    @php $search_style = setting_item('car_location_search_style','normal');
         $car_search_fields = setting_item_array('car_search_fields');
         $car_search_fields = array_values(array_filter($car_search_fields, function ($field) {
             return ($field['field'] ?? '') !== 'location' && ($field['field'] ?? '') !== 'date';
         }));
            $space_search_fields = array_values(\Illuminate\Support\Arr::sort($car_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));
    @endphp
    <div class="field-items">
        <div class="row w-100 m-0">
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-loc item">
                    <div>
                        <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.from_label') }}</h4>
                        <div class="text-15 text-light-1 ls-2 lh-16">
                            <select name="pickup_location_id"
                                    class="form-control js-transfer-pickup"
                                    data-fetch-url="{{ route('car.transfer_locations') }}"
                                    data-default-label="{{ __('transfers.form.select_pickup_option') }}"
                                    @if($pickupLocations->isEmpty()) disabled @endif>
                                <option value="">{{ __('transfers.form.select_pickup_option') }}</option>
                                @foreach($pickupLocations as $location)
                                    @php
                                        $payload = $location->toFrontendArray();
                                        $label = $payload['display_name'] ?? $location->display_name ?? $location->name ?? $location->address ?? '';
                                    @endphp
                                    <option value="{{ $location->id }}" data-source="backend" data-payload='@json($payload)' @if($location->id == $selectedPickupLocationId) selected @endif>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-loc item">
                    <div>
                        <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.exact_pickup_label') }}</h4>
                        <div class="text-15 text-light-1 ls-2 lh-16">
                            <div class="d-flex gap-10 flex-wrap">
                                <input type="text"
                                       class="form-control js-transfer-user-pickup-display flex-grow-1"
                                       name="user_pickup[address]"
                                       value="{{ $userPickupPayload['formatted_address'] ?? $userPickupPayload['address'] ?? '' }}"
                                       placeholder="{{ __('transfers.form.exact_pickup_placeholder') }}"
                                       autocomplete="off">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary js-transfer-user-pickup-locate"
                                        data-loading-text="{{ __('transfers.form.locating') }}">
                                    <i class="fa fa-location-arrow me-5"></i>{{ __('transfers.form.use_my_location') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-loc item">
                    <div>
                        <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.to_label') }}</h4>
                        <div class="text-15 text-light-1 ls-2 lh-16">
                            <input type="text" class="form-control js-transfer-dropoff-display" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}" placeholder="{{ __('transfers.form.to_placeholder') }}" minlength="3" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="dropoff[address]" class="js-transfer-dropoff-address" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}">
            <input type="hidden" name="dropoff[name]" class="js-transfer-dropoff-name" value="{{ $dropoffData['name'] ?? $dropoffData['address'] ?? '' }}">
            <input type="hidden" name="dropoff[lat]" class="js-transfer-dropoff-lat" value="{{ $dropoffData['lat'] ?? '' }}">
            <input type="hidden" name="dropoff[lng]" class="js-transfer-dropoff-lng" value="{{ $dropoffData['lng'] ?? '' }}">
            <input type="hidden" name="dropoff[place_id]" class="js-transfer-dropoff-place-id" value="{{ $dropoffData['place_id'] ?? '' }}">
            <input type="hidden" class="js-transfer-pickup-payload" value='@json($selectedPickupPayload)'>
            <input type="hidden" name="pickup" class="js-transfer-pickup-json" value='@json($selectedPickupPayload)'>
            <input type="hidden" name="dropoff" class="js-transfer-dropoff-json" value='@json($dropoffData)'>
            <input type="hidden" name="user_pickup[formatted_address]" class="js-transfer-user-pickup-formatted" value="{{ $userPickupPayload['formatted_address'] ?? $userPickupPayload['address'] ?? '' }}">
            <input type="hidden" name="user_pickup[place_id]" class="js-transfer-user-pickup-place-id" value="{{ $userPickupPayload['place_id'] ?? '' }}">
            <input type="hidden" name="user_pickup[lat]" class="js-transfer-user-pickup-lat" value="{{ $userPickupPayload['lat'] ?? '' }}">
            <input type="hidden" name="user_pickup[lng]" class="js-transfer-user-pickup-lng" value="{{ $userPickupPayload['lng'] ?? '' }}">
            <input type="hidden" class="js-transfer-user-pickup-json" value='@json($userPickupPayload)'>

            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-date item">
                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.date_label') }}</h4>
                    <div class="text-15 text-light-1 ls-2 lh-16">
                        @php
                            $transferDateDisplay = '';
                            if (!empty($transferDate)) {
                                try {
                                    $transferDateDisplay = display_date($transferDate);
                                } catch (\Exception $exception) {
                                    $transferDateDisplay = $transferDate;
                                }
                            }
                        @endphp
                        <input type="text"
                               class="form-control js-transfer-date-display"
                               data-display-format="{{ get_moment_date_format() }}"
                               value="{{ $transferDateDisplay }}"
                               placeholder="{{ __('transfers.form.date_label') }}"
                               readonly
                               autocomplete="off">
                        <input type="hidden" name="transfer_date" class="js-transfer-date" value="{{ $transferDate }}">
                    </div>
                </div>
            </div>
            <input type="hidden" name="transfer_time" class="js-transfer-time" value="{{ $transferTime }}">
            <input type="hidden" name="transfer_datetime" class="js-transfer-datetime" value="{{ $transferDatetime }}">
            @if(!empty($car_search_fields))
                @foreach($car_search_fields as $field)
                    <div class="col-lg-{{ $field['size'] ?? "6" }} align-self-center px-30 lg:py-20 lg:px-0">
                        @php $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "" @endphp
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Layout::common.search.fields.service_name')
                                @break
                            @case ('location')
                                @include('Layout::common.search.fields.location')
                                @break
                            @case ('date')
                                @include('Layout::common.search.fields.date')
                                @break
                            @case ('attr')
                                @include('Layout::common.search.fields.attr')
                                @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="button-item">
        <button class="mainSearch__submit button {{ $button_classes }}" type="submit">
            <i class="icon-search text-20 mr-10"></i>
            <span class="text-search">{{ __('transfers.form.search_button') }}</span>
        </button>
    </div>
</form>
@once
    @push('js')
        @include('Car::frontend.layouts.partials.transfer-form-script')
    @endpush
@endonce
