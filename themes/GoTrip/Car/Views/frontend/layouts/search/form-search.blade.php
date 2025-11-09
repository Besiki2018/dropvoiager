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
    $transferRoutes = collect($transfer_routes ?? $transferRoutes ?? []);
    $selectedRouteId = $selected_transfer_route_id ?? ($selected_transfer_route->id ?? request()->input('transfer_route_id'));
    $pickupData = request()->input('pickup', []);
    $dropoffData = request()->input('dropoff', []);
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

<form action="{{ route("car.search") }}" class="gotrip_form_search bravo_form_search bravo_form form-search-all-service form {{$classes }}" method="get">
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
                            <select name="transfer_route_id" class="form-control js-transfer-route" @if($transferRoutes->isEmpty()) disabled @endif>
                                <option value="">{{ __('transfers.form.select_route_option') }}</option>
                                @foreach($transferRoutes as $route)
                                    @php $routeData = $route->toFrontendArray(); @endphp
                                    <option value="{{ $route->id }}" data-route='@json($routeData)' @if($route->id == $selectedRouteId) selected @endif>{{ $route->pickup_name }}</option>
                                @endforeach
                            </select>
                            @if($transferRoutes->isEmpty())
                                <small class="text-danger d-block mt-2">{{ __('transfers.form.no_routes_available') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-loc item">
                    <div>
                        <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.to_label') }}</h4>
                        <div class="text-15 text-light-1 ls-2 lh-16">
                            <input type="text" class="form-control js-transfer-dropoff-display" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}" placeholder="{{ __('transfers.form.to_placeholder') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="pickup[address]" class="js-transfer-pickup-address" value="{{ $pickupData['address'] ?? $pickupData['name'] ?? '' }}">
            <input type="hidden" name="pickup[name]" class="js-transfer-pickup-name" value="{{ $pickupData['name'] ?? $pickupData['address'] ?? '' }}">
            <input type="hidden" name="pickup[lat]" class="js-transfer-pickup-lat" value="{{ $pickupData['lat'] ?? '' }}">
            <input type="hidden" name="pickup[lng]" class="js-transfer-pickup-lng" value="{{ $pickupData['lng'] ?? '' }}">
            <input type="hidden" name="dropoff[address]" class="js-transfer-dropoff-address" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}">
            <input type="hidden" name="dropoff[name]" class="js-transfer-dropoff-name" value="{{ $dropoffData['name'] ?? $dropoffData['address'] ?? '' }}">
            <input type="hidden" name="dropoff[lat]" class="js-transfer-dropoff-lat" value="{{ $dropoffData['lat'] ?? '' }}">
            <input type="hidden" name="dropoff[lng]" class="js-transfer-dropoff-lng" value="{{ $dropoffData['lng'] ?? '' }}">

            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-date item">
                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Date') }}</h4>
                    <div class="text-15 text-light-1 ls-2 lh-16">
                        <input type="date" name="transfer_date" class="form-control js-transfer-date" value="{{ $transferDate }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-3 align-self-center px-30 lg:py-20 lg:px-0">
                <div class="searchMenu-date item">
                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Time') }}</h4>
                    <div class="text-15 text-light-1 ls-2 lh-16">
                        <input type="time" name="transfer_time" class="form-control js-transfer-time" value="{{ $transferTime }}">
                    </div>
                </div>
            </div>
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
            <span class="text-search">{{__("Search")}}</span>
        </button>
    </div>
</form>
