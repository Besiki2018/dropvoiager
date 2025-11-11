@php
    $review_score = $row->review_data;
    $pickupLocations = $pickup_locations ?? collect();
    $selectedPickup = $pickup_location ?? null;
    $selectedPickupId = $selectedPickup->id ?? ($booking_data['pickup_location_id'] ?? '');
    $dropoffData = $dropoff ?? ($booking_data['dropoff'] ?? []);
    $selectedPickupPayload = $pickup_payload ?? ($selectedPickup ? $selectedPickup->toFrontendArray() : null);
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
    $detailPickupLabel = $selectedPickupPayload['name'] ?? ($selectedPickupPayload['address'] ?? '');
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
        'pricing_mode_label' => __('transfers.booking.pricing_mode_label'),
        'pricing_mode_fixed' => __('transfers.booking.pricing_mode_fixed'),
        'pricing_mode_per_km' => __('transfers.booking.pricing_mode_per_km'),
        'price_per_km_display' => __('transfers.booking.price_per_km_display'),
        'fixed_price_display' => __('transfers.booking.fixed_price_display'),
        'service_radius_display' => __('transfers.booking.service_radius_display'),
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
                                        data-default-label="{{ __('transfers.form.select_pickup_option') }}">
                                    <option value="">{{ __('transfers.form.select_pickup_option') }}</option>
                                    @foreach($pickupLocations as $location)
                                        @php
                                            $payload = $location->toFrontendArray();
                                            $label = $location->name;
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
                                <template v-if="hasTimeSlots">
                                    <select class="form-control js-transfer-time"
                                            :class="{'is-invalid': fieldErrors.datetime}"
                                            v-model="transfer_time">
                                        <option value="">{{ __('transfers.form.time_label') }}</option>
                                        <option v-for="slot in transfer_time_slots"
                                                :key="'slot-' + slot.value"
                                                :value="slot.value"
                                                :disabled="slot.disabled">
                                            @{{ slot.label }}
                                        </option>
                                    </select>
                                </template>
                                <template v-else>
                                    <input type="time"
                                           class="form-control js-transfer-time"
                                           :class="{'is-invalid': fieldErrors.datetime}"
                                           v-model="transfer_time"
                                           step="60"
                                           value="{{ $transferTimeValue }}">
                                </template>
                                <div class="text-13 text-muted mt-10" v-if="transfer_availability_loading" v-text="getAvailabilityMessage('loading')"></div>
                                <div class="text-13 text-red-1 mt-10" v-if="transfer_availability_error" v-text="transfer_availability_error"></div>
                                <div class="text-13 text-dark-1 mt-10" v-if="!transfer_availability_error && transfer_availability_note" v-text="transfer_availability_note"></div>
                                <div class="text-13 text-dark-1 mt-10" v-if="availableHoursMessage && !transfer_availability_error" v-text="availableHoursMessage"></div>
                            </div>
                        </div>
                        <input type="hidden" class="js-transfer-datetime" value="{{ $transferDatetimeValue }}">
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
                                                    <span class="input"><input type="number" v-model="number" min="0" :class="{'is-invalid': fieldErrors.passengers}"/></span>
                                                    <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-up" @click="addNumberType()">
                                                        <i class="icon-plus text-12"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                <div class="text-13 text-dark-1 mb-5" v-if="priceSummary.mode_display" v-text="priceSummary.mode_display"></div>
                                <div class="text-13 text-dark-1 mb-5" v-if="priceSummary.unit_price_display" v-text="priceSummary.unit_price_display"></div>
                                <div class="text-13 text-dark-1 mb-5" v-if="priceSummary.service_radius_display" v-text="priceSummary.service_radius_display"></div>
                                <div class="text-13 text-dark-1 mb-5" v-if="priceSummary.distance" v-text="priceSummary.distance"></div>
                                <div class="text-22 text-dark-1 fw-600" v-if="priceSummary.total" v-html="priceSummary.total"></div>
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
