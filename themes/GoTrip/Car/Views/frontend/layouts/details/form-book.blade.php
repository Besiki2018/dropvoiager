@php
    $review_score = $row->review_data;
    $pickupLocations = $pickup_locations ?? collect();
    $selectedPickup = $pickup_location ?? null;
    $selectedPickupId = $selectedPickup->id ?? ($booking_data['pickup_location_id'] ?? '');
    $dropoffData = $dropoff ?? ($booking_data['dropoff'] ?? []);
    $selectedPickupPayload = $pickup_payload ?? ($selectedPickup ? $selectedPickup->toFrontendArray() : null);
    $userPickupData = $user_pickup_payload ?? ($booking_data['user_pickup'] ?? []);
    $transferDatetimeValue = $transfer_datetime_value ?? ($booking_data['transfer_datetime'] ?? '');
    $transferDateValue = '';
    $transferTimeValue = '';
    $transferDateDisplay = '';
    $pricingMeta = [
        'mode' => $row->pricing_mode ?: 'per_km',
        'price_per_km' => $row->price_per_km,
        'fixed_price' => $row->fixed_price,
        'base_fee' => $row->base_fee ?? null,
        'service_radius_km' => $row->service_radius_km,
        'available_time_start' => $row->transfer_time_start,
        'available_time_end' => $row->transfer_time_end,
    ];
    $detailPickupLabel = $selectedPickupPayload['display_name'] ?? $selectedPickupPayload['name'] ?? ($selectedPickupPayload['address'] ?? '');
    $detailDropoffLabel = $dropoffData['address'] ?? ($dropoffData['name'] ?? '');
    $detailDistance = $transfer_distance_km ?? $row->transfer_distance_km;
    $detailDuration = $row->transfer_duration_min;
    $detailPricingMode = $row->transfer_pricing_mode;
    $detailUnitPrice = $row->transfer_unit_price;
    $detailTotalPrice = $row->calculated_price;
    $detailBaseFee = $row->transfer_base_fee;
    if ($detailBaseFee === null && $detailPricingMode === 'fixed') {
        $detailBaseFee = $detailTotalPrice ?? $detailUnitPrice;
    }
    $initialQuote = null;
    if (!empty($detailPricingMode) && $detailTotalPrice) {
        $initialQuote = [
            'price' => $detailTotalPrice,
            'total_price' => $detailTotalPrice,
            'distance_km' => $detailDistance,
            'duration_min' => $detailDuration,
            'pricing_mode' => $detailPricingMode,
            'unit_price' => $detailUnitPrice,
            'base_fee' => $detailBaseFee,
            'total_price_formatted' => $detailTotalPrice ? format_money($detailTotalPrice) : null,
            'distance_formatted' => $detailDistance !== null ? number_format((float) $detailDistance, 2) . ' km' : null,
            'passengers' => $row->transfer_passengers,
            'pickup' => $selectedPickupPayload,
            'dropoff' => $dropoffData,
            'pickup_label' => $detailPickupLabel,
            'dropoff_label' => $detailDropoffLabel,
        ];
    }
    $availabilityMessages = [
        'fetch_failed' => __('transfers.booking.availability_fetch_failed'),
        'no_slots' => __('transfers.booking.availability_no_slots'),
        'unavailable' => __('transfers.booking.availability_unavailable'),
        'invalid_date' => __('transfers.booking.availability_invalid_date'),
        'time_required' => __('transfers.booking.availability_time_required'),
        'loading' => __('transfers.booking.availability_loading'),
        'available_hours_range' => __('transfers.booking.available_hours_range'),
    ];
    if (!empty($transfer_datetime_display)) {
        $transferDateValue = $transfer_datetime_display->toDateString();
        $transferTimeValue = $transfer_datetime_display->format('H:i');
    } elseif (!empty($transferDatetimeValue)) {
        try {
            $transferDateValue = \Carbon\Carbon::parse($transferDatetimeValue, 'Asia/Tbilisi')->setTimezone('Asia/Tbilisi')->toDateString();
            $transferTimeValue = \Carbon\Carbon::parse($transferDatetimeValue, 'Asia/Tbilisi')->setTimezone('Asia/Tbilisi')->format('H:i');
        } catch (\Exception $exception) {
            $transferDateValue = '';
            $transferTimeValue = '';
        }
    }
    if (!empty($transferDateValue)) {
        try {
            $transferDateDisplay = display_date($transferDateValue);
        } catch (\Exception $exception) {
            $transferDateDisplay = $transferDateValue;
        }
    }
@endphp
<div class="bravo_single_book_wrap d-flex justify-end">
    <div class="bravo_single_book">
        @include('Layout::common.detail.vendor')
        <div id="bravo_car_book_app"
             v-cloak
             class="px-30 py-30 rounded-4 border-light shadow-4 bg-white w-360 lg:w-full"
             data-datetime-required="{{ __('transfers.form.datetime_required') }}"
             data-date-invalid="{{ __('transfers.form.date_invalid') }}"
             data-timezone-offset="{{ \Carbon\Carbon::now('Asia/Tbilisi')->format('P') }}"
             data-availability-url="{{ route('car.transfer.availability', $row->id) }}"
             data-availability-messages='@json($availabilityMessages)'
             data-quote-url="{{ route('car.transfer.quote', $row->id) }}"
             data-pricing-meta='@json($pricingMeta)'
             data-initial-quote='@json($initialQuote)'>
            @if($review_score)
                <div class="row y-gap-15 items-center justify-between">
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
                </div>
            @endif
            <div class="nav-enquiry" v-if="is_form_enquiry_and_book">
                <div class="enquiry-item active" >
                    <span>{{ __("Book") }}</span>
                </div>
                <div class="enquiry-item" data-toggle="modal" data-target="#enquiry_form_modal">
                    <span>{{ __("Enquiry") }}</span>
                </div>
            </div>
            <div class="form-book" :class="{'d-none':enquiry_type!='book'}">
                <div class="form-content js-transfer-form"
                     data-restore-error="{{ __('transfers.booking.state_restore_failed') }}">
                    <div class="row y-gap-20 pt-20">
                        <div class="col-12">
                            <div class="form-group px-20 py-10 border-light rounded-4">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.from_label') }}</h4>
                                <select class="form-control js-transfer-pickup"
                                        :class="{'is-invalid': fieldErrors.pickup}"
                                        name="pickup_location_id"
                                        data-fetch-url="{{ route('car.pickup_locations', ['car_id' => $row->id]) }}"
                                        data-default-label="{{ __('transfers.form.select_pickup_option') }}">
                                    <option value="">{{ __('transfers.form.select_pickup_option') }}</option>
                                    @foreach($pickupLocations as $location)
                                        @php
                                            $payload = $location->toFrontendArray();
                                            $label = $payload['display_name'] ?? $location->display_name ?? $location->name ?? $location->address ?? '';
                                            if (!empty($location->car?->title)) {
                                                $label .= ' — ' . $location->car->title;
                                            }
                                        @endphp
                                        <option value="{{ $location->id }}" data-source="backend" data-payload='@json($payload)' @if($location->id == $selectedPickupId) selected @endif>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="text-13 text-red-1 mt-10" v-if="fieldErrors.pickup" v-text="fieldErrors.pickup"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group px-20 py-10 border-light rounded-4">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.to_label') }}</h4>
                                <input type="text"
                                       class="form-control js-transfer-dropoff-display"
                                       :class="{'is-invalid': fieldErrors.dropoff}"
                                       value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}"
                                       placeholder="{{ __('transfers.form.to_placeholder') }}"
                                       minlength="3"
                                       autocomplete="off">
                                <div class="mt-15">
                                    <div class="transfer-dropoff-map rounded-4 overflow-hidden" style="height: 260px;">
                                        <div class="w-100 h-100 js-transfer-dropoff-map"></div>
                                    </div>
                                </div>
                                <input type="hidden" class="js-transfer-dropoff-address" value="{{ $dropoffData['address'] ?? $dropoffData['name'] ?? '' }}">
                                <input type="hidden" class="js-transfer-dropoff-name" value="{{ $dropoffData['name'] ?? $dropoffData['address'] ?? '' }}">
                                <input type="hidden" class="js-transfer-dropoff-lat" value="{{ $dropoffData['lat'] ?? '' }}">
                                <input type="hidden" class="js-transfer-dropoff-lng" value="{{ $dropoffData['lng'] ?? '' }}">
                                <input type="hidden" class="js-transfer-dropoff-place-id" value="{{ $dropoffData['place_id'] ?? '' }}">
                                <input type="hidden" class="js-transfer-pickup-payload" value='@json($selectedPickupPayload)'>
                                <input type="hidden" class="js-transfer-pickup-json" value='@json($selectedPickupPayload)'>
                                <input type="hidden" class="js-transfer-dropoff-json" value='@json($dropoffData)'>
                                <div class="text-13 text-red-1 mt-10" v-if="fieldErrors.dropoff" v-text="fieldErrors.dropoff"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group px-20 py-10 border-light rounded-4">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.exact_pickup_label') }}</h4>
                                <input type="text"
                                       class="form-control js-transfer-user-pickup-display"
                                       :class="{'is-invalid': fieldErrors.user_pickup}"
                                       value="{{ $userPickupData['formatted_address'] ?? $userPickupData['address'] ?? '' }}"
                                       placeholder="{{ __('transfers.form.exact_pickup_placeholder') }}"
                                       autocomplete="off">
                                <div class="mt-15">
                                    <div class="transfer-user-map rounded-4 overflow-hidden" style="height: 260px;">
                                        <div class="w-100 h-100 js-transfer-user-pickup-map"></div>
                                    </div>
                                </div>
                                <input type="hidden" class="js-transfer-user-pickup-json" value='@json($userPickupData)'>
                                <input type="hidden" class="js-transfer-user-pickup-formatted" name="user_pickup[formatted_address]" value="{{ $userPickupData['formatted_address'] ?? $userPickupData['address'] ?? '' }}">
                                <input type="hidden" class="js-transfer-user-pickup-address" name="user_pickup[address]" value="{{ $userPickupData['address'] ?? $userPickupData['formatted_address'] ?? '' }}">
                                <input type="hidden" class="js-transfer-user-pickup-place-id" name="user_pickup[place_id]" value="{{ $userPickupData['place_id'] ?? '' }}">
                                <input type="hidden" class="js-transfer-user-pickup-lat" name="user_pickup[lat]" value="{{ $userPickupData['lat'] ?? '' }}">
                                <input type="hidden" class="js-transfer-user-pickup-lng" name="user_pickup[lng]" value="{{ $userPickupData['lng'] ?? '' }}">
                                <div class="text-13 text-red-1 mt-10" v-if="fieldErrors.user_pickup" v-text="fieldErrors.user_pickup"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group px-20 py-10 border-light rounded-4 js-transfer-date-field" tabindex="0" data-display-format="{{ get_moment_date_format() }}">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.date_label') }}</h4>
                                <input type="text"
                                       class="form-control js-transfer-date-display"
                                       :class="{'is-invalid': fieldErrors.datetime}"
                                       data-display-format="{{ get_moment_date_format() }}"
                                       data-invalid-message="{{ __('transfers.form.date_invalid') }}"
                                       value="{{ $transferDateDisplay }}"
                                       placeholder="{{ __('transfers.form.date_label') }}"
                                       readonly
                                       autocomplete="off"
                                       ref="start_date">
                                <input type="hidden"
                                       class="js-transfer-date"
                                       ref="transfer_date"
                                       v-model="transfer_date"
                                       value="{{ $transferDateValue }}">
                                <div class="text-13 text-red-1 mt-5 js-transfer-date-error d-none"></div>
                                <div class="text-13 text-red-1 mt-5" v-if="fieldErrors.datetime" v-text="fieldErrors.datetime"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group px-20 py-10 border-light rounded-4">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('transfers.form.time_label') }}</h4>
                                <select class="form-control js-transfer-time"
                                        :class="{'is-invalid': fieldErrors.datetime}"
                                        v-model="transfer_time"
                                        :disabled="!transfer_time_slots.length">
                                    <option value="">{{ __('transfers.form.time_label') }}</option>
                                    <option v-for="slot in transfer_time_slots"
                                            :key="'slot-' + slot.value"
                                            :value="slot.value"
                                            :disabled="slot.disabled">
                                        @{{ slot.label }}
                                    </option>
                                </select>
                                <div class="text-13 text-muted mt-10" v-if="transfer_availability_loading" v-text="getAvailabilityMessage('loading')"></div>
                                <div class="text-13 text-red-1 mt-10" v-if="transfer_availability_error" v-text="transfer_availability_error"></div>
                                <div class="text-13 text-dark-1 mt-10" v-if="!transfer_availability_error && transfer_availability_note" v-text="transfer_availability_note"></div>
                                <div class="text-13 text-dark-1 mt-10" v-if="availableHoursMessage && !transfer_availability_error" v-text="availableHoursMessage"></div>
                            </div>
                        </div>
                        <input type="hidden" class="js-transfer-datetime" value="{{ $transferDatetimeValue }}">
                        <div class="col-12">
                            <div class="form-group px-20 py-10 border-light rounded-4">
                                <h4 class="text-15 fw-500 ls-2 lh-16">{{ __('Select Number') }}</h4>
                                <div class="d-flex align-items-center gap-10">
                                    <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4"
                                            type="button"
                                            @click="minusNumberType()">
                                        <i class="icon-minus text-12"></i>
                                    </button>
                                    <input type="number"
                                           class="form-control text-center w-auto"
                                           v-model="number"
                                           min="1"
                                           :class="{'is-invalid': fieldErrors.passengers}"
                                           @change="handlePassengerInput"
                                           @blur="handlePassengerInput">
                                    <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4"
                                            type="button"
                                            @click="addNumberType()">
                                        <i class="icon-plus text-12"></i>
                                    </button>
                                </div>
                                <div class="text-13 text-red-1 mt-10" v-if="fieldErrors.passengers" v-text="fieldErrors.passengers"></div>
                            </div>
                        </div>
                        <div class="col-12" v-if="transfer_quote_loading">
                            <div class="px-20 py-15 border-light rounded-4 bg-light">
                                <div class="text-13 text-muted">{{ __('transfers.booking.price_details_loading') }}</div>
                            </div>
                        </div>
                        <div class="col-12" v-if="!transfer_quote_loading && (transfer_quote_error || form_error_message)">
                            <div class="px-20 py-15 border-light rounded-4 bg-light">
                                <div class="text-13 text-red-1" role="alert" v-if="transfer_quote_error" v-text="transfer_quote_error"></div>
                                <div class="text-13 text-red-1" role="alert" v-if="form_error_message" v-text="form_error_message"></div>
                            </div>
                        </div>
                        <div class="col-12" v-if="html">
                            <div v-html="html"></div>
                        </div>
                        <div class="col-12" v-if="priceSummary">
                            <div class="px-20 py-15 border-light rounded-4 bg-light">
                                <div class="text-22 text-dark-1 fw-600" v-if="priceSummary.total">
                                    {{ __('transfers.booking.total_price_label') }}:
                                    <span v-html="priceSummary.total"></span>
                                </div>
                                <div class="text-13 text-dark-1 mt-10" v-if="priceSummary.distance">
                                    {{ __('transfers.booking.distance_label') }}: @{{ priceSummary.distance }}
                                </div>
                            </div>
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
@push('js')
    @once('transfer-form-script')
        @include('Car::frontend.layouts.partials.transfer-form-script')
    @endonce
@endpush
