@php
    $review_score = $row->review_data;
    $pickupAddress = request()->input('pickup_address');
    $pickupName = request()->input('pickup_name');
    $pickupLat = request()->input('pickup_lat');
    $pickupLng = request()->input('pickup_lng');
    $pickupPlaceId = request()->input('pickup_place_id');
    $pickupPayload = request()->input('pickup_payload');
    $pickupDisplay = request()->input('pickup_display', $pickupName ?: $pickupAddress);
    if ($pickupPayload) {
        try {
            $parsedPickup = json_decode($pickupPayload, true);
            if (is_array($parsedPickup)) {
                $pickupAddress = $pickupAddress ?: ($parsedPickup['address'] ?? '');
                $pickupName = $pickupName ?: ($parsedPickup['name'] ?? $pickupAddress);
                if (empty($pickupDisplay)) {
                    $pickupDisplay = $parsedPickup['display_name'] ?? $pickupName ?? $pickupAddress;
                }
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

    $dropoffAddress = request()->input('dropoff_address');
    $dropoffName = request()->input('dropoff_name');
    $dropoffLat = request()->input('dropoff_lat');
    $dropoffLng = request()->input('dropoff_lng');
    $dropoffPlaceId = request()->input('dropoff_place_id');
    $dropoffJson = request()->input('dropoff_json');
    $dropoffDisplay = request()->input('dropoff_display', $dropoffName ?: $dropoffAddress);
    if ($dropoffJson) {
        try {
            $parsedDropoff = json_decode($dropoffJson, true);
            if (is_array($parsedDropoff)) {
                $dropoffAddress = $dropoffAddress ?: ($parsedDropoff['address'] ?? '');
                $dropoffName = $dropoffName ?: ($parsedDropoff['name'] ?? $dropoffAddress);
                if (empty($dropoffDisplay)) {
                    $dropoffDisplay = $parsedDropoff['display_name'] ?? $dropoffName ?? $dropoffAddress;
                }
                if (empty($dropoffPlaceId) && !empty($parsedDropoff['place_id'])) {
                    $dropoffPlaceId = $parsedDropoff['place_id'];
                }
                if (empty($dropoffLat) && !empty($parsedDropoff['lat'])) {
                    $dropoffLat = $parsedDropoff['lat'];
                }
                if (empty($dropoffLng) && !empty($parsedDropoff['lng'])) {
                    $dropoffLng = $parsedDropoff['lng'];
                }
            }
        } catch (\Exception $exception) {
        }
    }
    if (empty($dropoffDisplay)) {
        $dropoffDisplay = $dropoffName ?: $dropoffAddress;
    }

    $userPickupJson = request()->input('user_pickup');
    $userPickupFormatted = request()->input('user_pickup_formatted');
    $userPickupAddress = request()->input('user_pickup_address');
    $userPickupLat = request()->input('user_pickup_lat');
    $userPickupLng = request()->input('user_pickup_lng');
    $userPickupPlaceId = request()->input('user_pickup_place_id');

    $transferDatetime = request()->input('transfer_datetime');
    $transferDate = request()->input('transfer_date');
    $transferTime = request()->input('transfer_time');
    $carDateRaw = request()->input('car_date', $transferDate);
    $carDateValue = '';
    $carDateDisplay = __('Select date');
    if ($carDateRaw) {
        try {
            $carDateValue = \Illuminate\Support\Carbon::parse($carDateRaw)->format('Y-m-d');
        } catch (\Exception $exception) {
            $carDateValue = $carDateRaw;
        }
        $timestamp = strtotime($carDateValue);
        if ($timestamp) {
            $carDateDisplay = display_date($timestamp);
        } else {
            $carDateDisplay = $carDateValue;
        }
    }
@endphp
<div class="bravo_single_book_wrap d-flex justify-end">
    <div class="bravo_single_book">
        @include('Layout::common.detail.vendor')
        <div id="bravo_car_book_app" v-cloak class="px-30 py-30 rounded-4 border-light shadow-4 bg-white w-360 lg:w-full" data-transfer-form="car-booking">
            <div class="row y-gap-15 items-center justify-between">
                <div class="col-auto">
                    <div class="text-14 text-light-1">
                        {{__("From")}}
                        <span class="text-14 text-red-1 line-through">{{ $row->display_sale_price }}</span>
                        <span class="text-20 fw-500 text-dark-1">{{ $row->display_price }}</span>
                    </div>
                </div>
                @if($review_score)
                    <div class="col-auto">
                        <div class="d-flex items-center">
                            <div class="text-14 text-right mr-10">
                                <div class="lh-15 fw-500">{{$review_score['score_text']}}</div>
                                <div class="lh-15 text-light-1">
                                    @if($review_score['total_review'] > 1)
                                        {{ __(":number reviews",["number"=>$review_score['total_review'] ]) }}
                                    @else
                                        {{ __(":number review",["number"=>$review_score['total_review'] ]) }}
                                    @endif
                                </div>
                            </div>

                            <div class="size-40 flex-center bg-blue-1 rounded-4">
                                <div class="text-14 fw-600 text-white">{{$review_score['score_total']}}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="nav-enquiry" v-if="is_form_enquiry_and_book">
                <div class="enquiry-item active" >
                    <span>{{ __("Book") }}</span>
                </div>
                <div class="enquiry-item" data-toggle="modal" data-target="#enquiry_form_modal">
                    <span>{{ __("Enquiry") }}</span>
                </div>
            </div>
            <div class="form-book" :class="{'d-none':enquiry_type!='book'}">
                <div class="form-content">
                    <div class="row y-gap-20 pt-20">
                        <div class="col-12">
                            <div class="px-20 py-10 border-light rounded-4">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Pickup Location') }}</h4>
                                <div class="text-15 text-dark-1 ls-2 lh-16 mt-5">
                                    <input type="text"
                                           class="w-100 border-0 bg-transparent p-0 text-15 text-dark-1 js-transfer-pickup-display"
                                           value="{{ $pickupDisplay }}"
                                           placeholder="{{ __('Enter pickup location') }}"
                                           autocomplete="off">
                                </div>
                                <input type="hidden" name="pickup_address" class="js-transfer-pickup-address" value="{{ $pickupAddress }}">
                                <input type="hidden" name="pickup_name" class="js-transfer-pickup-name" value="{{ $pickupName }}">
                                <input type="hidden" name="pickup_lat" class="js-transfer-pickup-lat" value="{{ $pickupLat }}">
                                <input type="hidden" name="pickup_lng" class="js-transfer-pickup-lng" value="{{ $pickupLng }}">
                                <input type="hidden" name="pickup_place_id" class="js-transfer-pickup-place-id" value="{{ $pickupPlaceId }}">
                                <input type="hidden" name="pickup_payload" class="js-transfer-pickup-payload" value="{{ $pickupPayload }}">
                                <input type="hidden" name="pickup_location_id" class="js-transfer-pickup" value="{{ request()->input('pickup_location_id') }}">
                                <div class="text-13 text-red-1 mt-5" v-if="fieldErrors && fieldErrors.pickup" v-text="fieldErrors.pickup"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="px-20 py-10 border-light rounded-4">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Drop-off Location') }}</h4>
                                <div class="text-15 text-dark-1 ls-2 lh-16 mt-5">
                                    <input type="text"
                                           class="w-100 border-0 bg-transparent p-0 text-15 text-dark-1 js-transfer-dropoff-display"
                                           value="{{ $dropoffDisplay }}"
                                           placeholder="{{ __('Enter drop-off location') }}"
                                           autocomplete="off">
                                </div>
                                <input type="hidden" name="dropoff_address" class="js-transfer-dropoff-address" value="{{ $dropoffAddress }}">
                                <input type="hidden" name="dropoff_name" class="js-transfer-dropoff-name" value="{{ $dropoffName }}">
                                <input type="hidden" name="dropoff_lat" class="js-transfer-dropoff-lat" value="{{ $dropoffLat }}">
                                <input type="hidden" name="dropoff_lng" class="js-transfer-dropoff-lng" value="{{ $dropoffLng }}">
                                <input type="hidden" name="dropoff_place_id" class="js-transfer-dropoff-place-id" value="{{ $dropoffPlaceId }}">
                                <input type="hidden" name="dropoff_json" class="js-transfer-dropoff-json" value="{{ $dropoffJson }}">
                                <div class="text-13 text-red-1 mt-5" v-if="fieldErrors && fieldErrors.dropoff" v-text="fieldErrors.dropoff"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-date-search is_single_picker position-relative px-20 py-10 border-light rounded-4 js-transfer-car-calendar" data-format="{{ get_moment_date_format() }}">
                                <div class="date-wrapper" data-x-dd-click="car-calendar">
                                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Car Calendar') }}</h4>
                                    <div class="text-15 text-dark-1 ls-2 lh-16 mt-5">
                                        <span class="render check-in-render">{{ $carDateDisplay }}</span>
                                    </div>
                                </div>
                                <input type="hidden" class="check-in-input js-transfer-car-date-input" name="car_date" value="{{ $carDateValue }}">
                                <input type="hidden" class="check-out-input" value="{{ $carDateValue }}">
                                <input type="text" class="check-in-out absolute invisible" autocomplete="off" value="{{ $carDateValue }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="px-20 py-10 border-light rounded-4">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Adjust on Map') }}</h4>
                                <div class="mt-10 rounded-4 overflow-hidden position-relative" style="min-height: 260px;">
                                    <div class="transfer-map h-100 w-100 position-absolute top-0 start-0" data-transfer-map="car-booking" data-default-lat="{{ $row->map_lat }}" data-default-lng="{{ $row->map_lng }}" style="min-height: 260px;"></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="js-transfer-user-pickup-json" value="{{ $userPickupJson }}">
                        <input type="hidden" class="js-transfer-user-pickup-formatted" value="{{ $userPickupFormatted }}">
                        <input type="hidden" class="js-transfer-user-pickup-address" value="{{ $userPickupAddress }}">
                        <input type="hidden" class="js-transfer-user-pickup-lat" value="{{ $userPickupLat }}">
                        <input type="hidden" class="js-transfer-user-pickup-lng" value="{{ $userPickupLng }}">
                        <input type="hidden" class="js-transfer-user-pickup-place-id" value="{{ $userPickupPlaceId }}">
                        <input type="hidden" class="js-transfer-datetime" value="{{ $transferDatetime }}">
                        <input type="hidden" class="js-transfer-date" value="{{ $carDateValue }}">
                        <input type="hidden" class="js-transfer-time" value="{{ $transferTime }}">
                        <div class="col-12">
                            <div class="form-group form-date-field form-date-search clearfix px-20 py-10 border-light rounded-4 -right position-relative" data-format="{{get_moment_date_format()}}">
                                <div class="date-wrapper clearfix" @click="openStartDate">
                                    <div class="check-in-wrapper">
                                        <h4 class="text-15 fw-500 ls-2 lh-16">{{__("Select Dates")}}</h4>
                                        <div class="render check-in-render" v-html="start_date_html"></div>
                                        @if(!empty($row->min_day_before_booking))
                                            <div class="render check-in-render">
                                                <small>
                                                    @if($row->min_day_before_booking > 1)
                                                        - {{ __("Book :number days in advance",["number"=>$row->min_day_before_booking]) }}
                                                    @else
                                                        - {{ __("Book :number day in advance",["number"=>$row->min_day_before_booking]) }}
                                                    @endif
                                                </small>
                                            </div>
                                        @endif
                                        @if(!empty($row->min_day_stays))
                                            <div class="render check-in-render">
                                                <small>
                                                    @if($row->min_day_stays > 1)
                                                        - {{ __("Stay at least :number days",["number"=>$row->min_day_stays]) }}
                                                    @else
                                                        - {{ __("Stay at least :number day",["number"=>$row->min_day_stays]) }}
                                                    @endif
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <input type="text" class="start_date" ref="start_date" style="height: 1px;visibility: hidden;position: absolute;left: 0;">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="searchMenu-guests px-20 py-10 border-light rounded-4 js-form-dd">
                                <div data-x-dd-click="searchMenu-guests">
                                    <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Select Number') }}</h4>
                                    <div class="text-15 text-light-1 ls-2 lh-16">
                                        <span class="js-count-adult">@{{ number }}</span>
                                    </div>
                                </div>
                                <div class="searchMenu-guests__field shadow-2" data-x-dd="searchMenu-guests" data-x-dd-toggle="-is-active">
                                    <div class="bg-white px-30 py-30 rounded-4">
                                        <div class="row y-gap-10 justify-between items-center form-guest-search">
                                            <div class="col-auto">
                                                <div class="text-15 fw-500">{{ __('Number') }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="d-flex items-center js-counter" data-value-change=".js-count-adult">
                                                    <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-down" @click="minusNumberType()">
                                                        <i class="icon-minus text-12"></i>
                                                    </button>
                                                    <span class="input"><input type="number" v-model="number" min="0"/></span>
                                                    <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-up" @click="addNumberType()">
                                                        <i class="icon-plus text-12"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" v-if="extra_price.length">
                            <div class="form-section-group px-20 py-10 border-light rounded-4">
                                <h4 class="form-section-title text-15 fw-500 ls-2 lh-16">{{__('Extra prices:')}}</h4>
                                <div class="form-group " v-for="(type,index) in extra_price">
                                    <div class="extra-price-wrap d-flex justify-content-between">
                                        <div class="flex-grow-1">
                                            <label class="d-flex items-center">
                                                <span class="form-checkbox ">
                                                    <input type="checkbox" true-value="1" false-value="0" v-model="type.enable" style="display: none">
                                                    <span class="form-checkbox__mark">
                                                        <span class="form-checkbox__icon icon-check"></span>
                                                    </span>
                                                </span>
                                                <span class="text-15 ml-10">@{{type.name}}</span>
                                            </label>
                                        </div>
                                        <div class="flex-shrink-0">@{{type.price_html}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" v-if="buyer_fees.length">
                            <div class="form-section-group form-group-padding px-20 py-10 border-light rounded-4">
                                <div class="extra-price-wrap d-flex justify-content-between" v-for="(type,index) in buyer_fees">
                                    <div class="flex-grow-1">
                                        <label class="text-15">@{{type.type_name}}
                                            <i class="fa fa-info-circle" v-if="type.desc" data-bs-toggle="tooltip" data-placement="top" :title="type.type_desc"></i>
                                        </label>
                                        <div class="render" v-if="type.price_type">(@{{type.price_type}})</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="unit" v-if='type.unit == "percent"'>
                                            @{{ type.price }}%
                                        </div>
                                        <div class="unit" v-else >
                                            @{{ formatMoney(type.price) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" v-if="total_price > 0">
                            <ul class="form-section-total list-unstyled px-20 py-10 border-light rounded-4">
                                <li class="d-flex justify-content-between">
                                    <label class="text-15 fw-500">{{__("Total")}}</label>
                                    <span class="price">@{{total_price_html}}</span>
                                </li>
                                <li class="d-flex justify-content-between" v-if="is_deposit_ready">
                                    <label for="">{{__("Pay now")}}</label>
                                    <span class="price">@{{pay_now_price_html}}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12" v-if="html">
                            <div v-html="html"></div>
                        </div>
                        <div class="col-12">
                            <div class="submit-group">
                                <a class="button -dark-1 py-15 px-35 h-60 col-12 rounded-4 bg-blue-1 text-white cursor-pointer" @click="doSubmit($event)" :class="{'disabled':onSubmit,'btn-success':(step == 2),'btn-primary':step == 1}" name="submit">
                                    <span v-if="step == 1">{{__("BOOK NOW")}}</span>
                                    <span v-if="step == 2">{{__("Book Now")}}</span>
                                    <i v-show="onSubmit" class="fa fa-spinner fa-spin"></i>
                                </a>
                                <div class="alert-text mt-10" v-show="message.content" v-html="message.content" :class="{'danger':!message.type,'success':message.type}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-send-enquiry" v-show="enquiry_type=='enquiry'">
                <button class="btn btn-primary" data-toggle="modal" data-target="#enquiry_form_modal">
                    {{ __("Contact Now") }}
                </button>
            </div>
        </div>
    </div>
</div>
@include("Booking::frontend.global.enquiry-form",['service_type'=>'car'])
