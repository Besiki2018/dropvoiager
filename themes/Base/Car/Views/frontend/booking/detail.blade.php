@php
    $lang_local = app()->getLocale();
    $pickupMeta = $booking->getJsonMeta('transfer_pickup') ?: [];
    $dropoffMeta = $booking->getJsonMeta('transfer_dropoff') ?: [];
    $distanceMeta = $booking->getJsonMeta('distance_pricing') ?: [];
    $buildMapLink = function ($meta) {
        if (empty($meta)) {
            return '';
        }
        $lat = $meta['lat'] ?? null;
        $lng = $meta['lng'] ?? null;
        $query = '';
        if (is_numeric($lat) && is_numeric($lng)) {
            $query = $lat . ',' . $lng;
        } elseif (!empty($meta['display_name'])) {
            $query = $meta['display_name'];
        } elseif (!empty($meta['address'])) {
            $query = $meta['address'];
        }
        if ($query === '') {
            return '';
        }
        $params = [
            'api=1',
            'query=' . urlencode($query)
        ];
        if (!empty($meta['place_id'])) {
            $params[] = 'query_place_id=' . urlencode($meta['place_id']);
        }
        return 'https://www.google.com/maps/search/?' . implode('&', $params);
    };
@endphp
<div class="booking-review">
    <h4 class="booking-review-title">{{__("Your Booking")}}</h4>
    <div class="booking-review-content">
        <div class="review-section">
            <div class="service-info">
                <div>
                    @php
                        $service_translation = $service->translate($lang_local);
                    @endphp
                    <h3 class="service-name"><a href="{{$service->getDetailUrl()}}">{!! clean($service_translation->title) !!}</a></h3>
                    @if($service_translation->address)
                        <p class="address"><i class="fa fa-map-marker"></i>
                            {{$service_translation->address}}
                        </p>
                    @endif
                </div>
                <div>
                    @if($image_url = $service->image_url)
                        @if(!empty($disable_lazyload))
                            <img src="{{$service->image_url}}" class="img-responsive" alt="{!! clean($service_translation->title) !!}">
                        @else
                            {!! get_image_tag($service->image_id,'medium',['class'=>'img-responsive','alt'=>$service_translation->title]) !!}
                        @endif
                    @endif
                </div>
                @php $vendor = $service->author; @endphp
                @if($vendor->hasPermission('dashboard_vendor_access') and !$vendor->hasPermission('dashboard_access'))
                    <div class="mt-2">
                        <i class="icofont-info-circle"></i>
                        {{ __("Vendor") }}: <a href="{{route('user.profile',['id'=>$vendor->id])}}" target="_blank" >{{$vendor->getDisplayName()}}</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="review-section">
            <ul class="review-list">
                @if($booking->start_date)
                    <li>
                        <div class="label">{{__('Start date:')}}</div>
                        <div class="val">
                            {{display_date($booking->start_date)}}
                        </div>
                    </li>
                    <li>
                        <div class="label">{{__('End date:')}}</div>
                        <div class="val">
                            {{display_date($booking->end_date)}}
                        </div>
                    </li>
                    <li>
                        <div class="label">{{__('Days:')}}</div>
                        <div class="val">
                            {{$booking->duration_days}}
                        </div>
                    </li>
                @endif
                @if(!empty($pickupMeta))
                    @php $pickupLink = $buildMapLink($pickupMeta); @endphp
                    <li>
                        <div class="label">{{ __('Pickup location:') }}</div>
                        <div class="val">
                            @if($pickupLink)
                                <a href="{{ $pickupLink }}" target="_blank">{{ $pickupMeta['display_name'] ?? $pickupMeta['address'] ?? '' }}</a>
                            @else
                                {{ $pickupMeta['display_name'] ?? $pickupMeta['address'] ?? '' }}
                            @endif
                        </div>
                    </li>
                @endif
                @if(!empty($dropoffMeta))
                    @php $dropoffLink = $buildMapLink($dropoffMeta); @endphp
                    <li>
                        <div class="label">{{ __('Drop-off location:') }}</div>
                        <div class="val">
                            @if($dropoffLink)
                                <a href="{{ $dropoffLink }}" target="_blank">{{ $dropoffMeta['display_name'] ?? $dropoffMeta['address'] ?? '' }}</a>
                            @else
                                {{ $dropoffMeta['display_name'] ?? $dropoffMeta['address'] ?? '' }}
                            @endif
                        </div>
                    </li>
                @endif
                @if(!empty($distanceMeta['distance_text']))
                    <li>
                        <div class="label">{{ __('Route distance:') }}</div>
                        <div class="val">{{ $distanceMeta['distance_text'] }}</div>
                    </li>
                @endif
                @if($meta = $booking->number)
                    <li>
                        <div class="label">{{__('Number:')}}</div>
                        <div class="val">
                            {{$meta}}
                        </div>
                    </li>
                @endif
                    <li class="flex-wrap">
                        <div class="flex-grow-0 flex-shrink-0 w-100">
                            <p class="text-center">
                                <a data-toggle="modal" data-target="#detailBookingDate{{$booking->code}}" aria-expanded="false"
                                   aria-controls="detailBookingDate{{$booking->code}}">
                                    {{__('Detail')}} <i class="icofont-list"></i>
                                </a>
                            </p>
                        </div>
                    </li>
            </ul>
        </div>
        <div class="review-section total-review">
            <ul class="review-list">
                @php
                    $price_item = $booking->total_before_extra_price;
                @endphp
                @if(!empty($price_item))
                    <li>
                        <div class="label">{{__('Rental price')}}
                        </div>
                        <div class="val">
                            {{format_money( $price_item)}}
                        </div>
                    </li>
                @endif
                @php $extra_price = $booking->getJsonMeta('extra_price') @endphp
                @if(!empty($extra_price))
                    <li>
                        <div class="label-title"><strong>{{__("Extra Prices:")}}</strong></div>
                    </li>
                    <li class="no-flex">
                        <ul>
                            @foreach($extra_price as $type)
                                <li>
                                    <div class="label">{{$type['name_'.$lang_local] ?? $type['name']}}:</div>
                                    <div class="val">
                                        {{format_money($type['total'] ?? 0)}}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                @php
                    $list_all_fee = [];
                    if(!empty($booking->buyer_fees)){
                        $buyer_fees = json_decode($booking->buyer_fees , true);
                        $list_all_fee = $buyer_fees;
                    }
                    if(!empty($vendor_service_fee = $booking->vendor_service_fee)){
                        $list_all_fee = array_merge($list_all_fee , $vendor_service_fee);
                    }
                @endphp
                @if(!empty($list_all_fee))
                    @foreach ($list_all_fee as $item)
                        @php
                            $fee_price = $item['price'];
                            if(!empty($item['unit']) and $item['unit'] == "percent"){
                                $fee_price = ( $booking->total_before_fees / 100 ) * $item['price'];
                            }
                        @endphp
                        <li>
                            <div class="label">
                                {{$item['name_'.$lang_local] ?? $item['name']}}
                                <i class="icofont-info-circle" data-toggle="tooltip" data-placement="top" title="{{ $item['desc_'.$lang_local] ?? $item['desc'] }}"></i>
                                @if(!empty($item['per_person']) and $item['per_person'] == "on")
                                    : {{$booking->total_guests}} * {{format_money( $fee_price )}}
                                @endif
                            </div>
                            <div class="val">
                                @if(!empty($item['per_person']) and $item['per_person'] == "on")
                                    {{ format_money( $fee_price * $booking->total_guests ) }}
                                @else
                                    {{ format_money( $fee_price ) }}
                                @endif
                            </div>
                        </li>
                    @endforeach
                @endif
                @includeIf('Coupon::frontend/booking/checkout-coupon')
                <li class="final-total d-block">
                    <div class="d-flex justify-content-between">
                        <div class="label">{{__("Total:")}}</div>
                        <div class="val">{{format_money($booking->total)}}</div>
                    </div>
                @if($booking->status !='draft')
                    <div class="d-flex justify-content-between">
                        <div class="label">{{__("Paid:")}}</div>
                        <div class="val">{{format_money($booking->paid)}}</div>
                    </div>
                    @if($booking->paid < $booking->total )
                        <div class="d-flex justify-content-between">
                            <div class="label">{{__("Remain:")}}</div>
                            <div class="val">{{format_money($booking->total - $booking->paid)}}</div>
                        </div>
                        @endif
                    @endif
                </li>
                @include ('Booking::frontend/booking/checkout-deposit-amount')
            </ul>
        </div>
    </div>
</div>

<?php
$dateDetail = $service->detailBookingEachDate($booking);
;?>
<div class="modal fade" id="detailBookingDate{{$booking->code}}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">{{__('Detail')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="review-list list-unstyled">
                    <li class="mb-3 pb-1 border-bottom">
                        <h6 class="label text-center font-weight-bold mb-1"></h6>
                        <div>
                            @includeIf("Car::frontend.booking.detail-date",['rows'=>$dateDetail,'number'=>$booking->number])
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold px-2">
                            <span>{{__("Total:")}}</span>
                            <span>{{format_money(array_sum(\Illuminate\Support\Arr::pluck($dateDetail,['price']))*$booking->number)}}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
