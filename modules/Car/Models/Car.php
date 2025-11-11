<?php

namespace Modules\Car\Models;

use App\Currency;
use Illuminate\Http\Response;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Modules\Booking\Models\Bookable;
use Modules\Booking\Models\Booking;
use Modules\Booking\Traits\CapturesService;
use Modules\Core\Models\Attributes;
use Modules\Core\Models\SEO;
use Modules\Core\Models\Terms;
use Modules\Media\Helpers\FileHelper;
use Modules\Review\Models\Review;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Car\Models\CarTranslation;
use Modules\Car\Models\CarPickupLocation;
use Modules\Car\Models\TransferLocation;
use Modules\User\Models\UserWishList;
use Modules\Location\Models\Location;

class Car extends Bookable
{
    use Notifiable;
    use SoftDeletes;
    use CapturesService;

    protected $table = 'bravo_cars';
    public $type = 'car';
    public $checkout_booking_detail_file       = 'Car::frontend/booking/detail';
    public $checkout_booking_detail_modal_file = 'Car::frontend/booking/detail-modal';
    public $set_paid_modal_file                = 'Car::frontend/booking/set-paid-modal';
    public $email_new_booking_file             = 'Car::emails.new_booking_detail';
    public $availabilityClass = CarDate::class;
    protected $translation_class = CarTranslation::class;

    protected $fillable = [
        'title',
        'content',
        'status',
        'faqs'
    ];
    protected $slugField     = 'slug';
    protected $slugFromField = 'title';
    protected $seo_type = 'car';

    protected $casts = [
        'faqs'  => 'array',
        'extra_price'  => 'array',
        'service_fee'  => 'array',
        'price'=>'float',
        'sale_price'=>'float',
        'service_center_lat' => 'float',
        'service_center_lng' => 'float',
        'service_radius_km' => 'float',
        'fixed_price' => 'float',
        'price_per_km' => 'float',
        'transfer_time_start' => 'string',
        'transfer_time_end' => 'string',
    ];
    /**
     * @var Booking
     */
    protected $bookingClass;
    /**
     * @var Review
     */
    protected $reviewClass;

    /**
     * @var CarDate
     */
    protected $carDateClass;

    /**
     * @var CarTerm
     */
    protected $carTermClass;

    protected $carTranslationClass;
    protected $userWishListClass;

    protected $tmp_price = 0;
    protected $tmp_dates = [];
    protected array $transferContext = [
        'price' => null,
        'price_single' => null,
        'route_distance' => null,
        'route_duration' => null,
        'pickup_location' => null,
        'dropoff' => null,
        'pickup_location_id' => null,
        'transfer_datetime' => null,
        'transfer_date' => null,
        'pricing_mode' => null,
        'unit_price' => null,
        'base_fee' => null,
        'passengers' => 1,
    ];

    protected function normalizeTimeValue($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('H:i:s');
        }

        $value = trim((string) $value);

        $formats = ['H:i:s', 'H:i'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value, 'Asia/Tbilisi')->format('H:i:s');
            } catch (\Exception $exception) {
                continue;
            }
        }

        return null;
    }

    protected function formatTimeValue($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('H:i');
        }

        $value = trim((string) $value);
        $formats = ['H:i:s', 'H:i'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value, 'Asia/Tbilisi')->format('H:i');
            } catch (\Exception $exception) {
                continue;
            }
        }

        return null;
    }

    public function getTransferTimeStartAttribute($value): ?string
    {
        return $this->formatTimeValue($value);
    }

    public function getTransferTimeEndAttribute($value): ?string
    {
        return $this->formatTimeValue($value);
    }

    public function setTransferTimeStartAttribute($value): void
    {
        $this->attributes['transfer_time_start'] = $this->normalizeTimeValue($value);
    }

    public function setTransferTimeEndAttribute($value): void
    {
        $this->attributes['transfer_time_end'] = $this->normalizeTimeValue($value);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->bookingClass = Booking::class;
        $this->reviewClass = Review::class;
        $this->carDateClass = CarDate::class;
        $this->carTermClass = CarTerm::class;
        $this->carTranslationClass = CarTranslation::class;
        $this->userWishListClass = UserWishList::class;
    }

    public static function getModelName()
    {
        return __("Car");
    }

    public static function getTableName()
    {
        return with(new static)->table;
    }


    /**
     * Get SEO fop page list
     *
     * @return mixed
     */
    static public function getSeoMetaForPageList()
    {
        $meta['seo_title'] = __("Search for Cars");
        if (!empty($title = setting_item_with_lang("car_page_list_seo_title",false))) {
            $meta['seo_title'] = $title;
        }else if(!empty($title = setting_item_with_lang("car_page_search_title"))) {
            $meta['seo_title'] = $title;
        }
        $meta['seo_image'] = null;
        if (!empty($title = setting_item("car_page_list_seo_image"))) {
            $meta['seo_image'] = $title;
        }else if(!empty($title = setting_item("car_page_search_banner"))) {
            $meta['seo_image'] = $title;
        }
        $meta['seo_desc'] = setting_item_with_lang("car_page_list_seo_desc");
        $meta['seo_share'] = setting_item_with_lang("car_page_list_seo_share");
        $meta['full_url'] = url()->current();
        return $meta;
    }


    public function terms(){
        return $this->hasMany($this->carTermClass, "target_id");
    }

    public function pickupLocations()
    {
        return $this->hasMany(CarPickupLocation::class, 'car_id');
    }

    public function getDetailUrl($include_param = true)
    {
        $param = [];
        if($include_param){
            if(!empty($date =  request()->input('date'))){
                $dates = explode(" - ",$date);
                if(!empty($dates)){
                    $param['start'] = $dates[0] ?? "";
                    $param['end'] = $dates[1] ?? "";
                }
            }
            if(!empty($adults =  request()->input('adults'))){
                $param['adults'] = $adults;
            }
            if(!empty($children =  request()->input('children'))){
                $param['children'] = $children;
            }
            $preserveKeys = [
                'pickup',
                'pickup_location_id',
                'dropoff',
                'transfer_datetime',
                'transfer_date',
                'transfer_time',
                'number',
                'passengers',
                'user_pickup',
            ];
            foreach ($preserveKeys as $key) {
                $value = request()->input($key);
                if ($value !== null && $value !== '') {
                    $param[$key] = $value;
                }
            }
        }
        $urlDetail = app_get_locale(false, false, '/') . config('car.car_route_prefix') . "/" . $this->slug;
        if(!empty($param)){
            $urlDetail .= "?".http_build_query($param);
        }
        return url($urlDetail);
    }

    public static function getLinkForPageSearch( $locale = false , $param = [] ){

        return url(app_get_locale(false , false , '/'). config('car.car_route_prefix')."?".http_build_query($param));
    }

    public function getEditUrl()
    {
        return url(route('car.admin.edit',['id'=>$this->id]));
    }

    public function getDiscountPercentAttribute()
    {
        if (    !empty($this->price) and $this->price > 0
            and !empty($this->sale_price) and $this->sale_price > 0
            and $this->price > $this->sale_price
        ) {
            $percent = 100 - ceil($this->sale_price / ($this->price / 100));
            return $percent . "%";
        }
    }

    public function fill(array $attributes)
    {
        if(!empty($attributes)){
            foreach ( $this->fillable as $item ){
                $attributes[$item] = $attributes[$item] ?? null;
            }
        }
        return parent::fill($attributes); // TODO: Change the autogenerated stub
    }

    public function isBookable()
    {
        if ($this->status != 'publish')
            return false;
        return parent::isBookable();
    }

    public function addToCart(Request $request)
    {
        $res = $this->addToCartValidate($request);
        if($res !== true) return $res;
        // Add Booking
        $start_date = new \DateTime($request->input('start_date'));
        $end_date = new \DateTime($request->input('end_date'));
        $extra_price_input = $request->input('extra_price');
        $extra_price = [];
        $number = $request->input('number',1);

        if ($this->hasTransferContext()) {
            $total = $this->transferContext['price'] ?? 0;
        } else {
            $total = $this->tmp_price * $number;
        }

        $duration_in_day = max(1,ceil(($end_date->getTimestamp() - $start_date->getTimestamp()) / DAY_IN_SECONDS ) + 1 );
        if ($this->enable_extra_price and !empty($this->extra_price)) {
            if (!empty($this->extra_price)) {
                foreach (array_values($this->extra_price) as $k => $type) {
                    if (isset($extra_price_input[$k]) and !empty($extra_price_input[$k]['enable'])) {
                        $type_total = 0;
                        switch ($type['type']) {
                            case "one_time":
                                $type_total = $type['price'] * $number;
                                break;
                            case "per_day":
                                $type_total = $type['price'] * $duration_in_day * $number;
                                break;
                        }
                        $type['total'] = $type_total;
                        $total += $type_total;
                        $extra_price[] = $type;
                    }
                }
            }
        }

        //Buyer Fees for Admin
        $total_before_fees = $total;
        $total_buyer_fee = 0;
        if (!empty($list_buyer_fees = setting_item('car_booking_buyer_fees'))) {
            $list_fees = json_decode($list_buyer_fees, true);
            $total_buyer_fee = $this->calculateServiceFees($list_fees , $total_before_fees , 1);
            $total += $total_buyer_fee;
        }

        //Service Fees for Vendor
        $total_service_fee = 0;
        if(!empty($this->enable_service_fee) and !empty($list_service_fee = $this->service_fee)){
            $total_service_fee = $this->calculateServiceFees($list_service_fee , $total_before_fees , 1);
            $total += $total_service_fee;
        }

        if (empty($start_date) or empty($end_date)) {
            return $this->sendError(__("Your selected dates are not valid"));
        }
        $booking = new $this->bookingClass();
        $booking->status = 'draft';
        $booking->object_id = $request->input('service_id');
        $booking->object_model = $request->input('service_type');
        $booking->vendor_id = $this->author_id;
        $booking->customer_id = Auth::id();
        $booking->total = $total;
        $transferPassengers = (int) $request->attributes->get('transfer_passengers', $number);
        $booking->total_guests = $transferPassengers > 0 ? $transferPassengers : 1;
        $booking->start_date = $start_date->format('Y-m-d H:i:s');
        $booking->end_date = $end_date->format('Y-m-d H:i:s');

        $booking->vendor_service_fee_amount = $total_service_fee ?? '';
        $booking->vendor_service_fee = $list_service_fee ?? '';
        $booking->buyer_fees = $list_buyer_fees ?? '';
        $booking->total_before_fees = $total_before_fees;
        $booking->total_before_discount = $total_before_fees;

        $booking->calculateCommission();
        $booking->number = $number;

        if($this->isDepositEnable())
        {
            $booking_deposit_fomular = $this->getDepositFomular();
            $tmp_price_total = $booking->total;
            if($booking_deposit_fomular == "deposit_and_fee"){
                $tmp_price_total = $booking->total_before_fees;
            }

            switch ($this->getDepositType()){
                case "percent":
                    $booking->deposit = $tmp_price_total * $this->getDepositAmount() / 100;
                    break;
                default:
                    $booking->deposit = $this->getDepositAmount();
                    break;
            }
            if($booking_deposit_fomular == "deposit_and_fee"){
                $booking->deposit = $booking->deposit + $total_buyer_fee + $total_service_fee;
            }
        }

        if ($pickupPayload = $request->attributes->get('transfer_pickup_payload')) {
            $booking->pickup_name = Arr::get($pickupPayload, 'name');
            $booking->pickup_source = Arr::get($pickupPayload, 'source');
            $booking->pickup_lat = Arr::get($pickupPayload, 'lat');
            $booking->pickup_lng = Arr::get($pickupPayload, 'lng');
        }

        if ($dropoffPayload = $request->attributes->get('transfer_dropoff')) {
            $booking->dropoff_address = Arr::get($dropoffPayload, 'address') ?: Arr::get($dropoffPayload, 'name');
            $booking->dropoff_lat = Arr::get($dropoffPayload, 'lat');
            $booking->dropoff_lng = Arr::get($dropoffPayload, 'lng');
        }

        $userPickupPayload = $request->attributes->get('transfer_user_pickup');

        if ($this->hasTransferContext()) {
            $booking->distance_km = $this->transferContext['route_distance'];
            $booking->duration_min = $this->transferContext['route_duration'];
            $booking->pricing_mode = $this->transferContext['pricing_mode'];
            $booking->unit_price = $this->transferContext['unit_price'];
            $booking->total_price = $this->transferContext['price'];
        }

        $check = $booking->save();
        if ($check) {

            $this->bookingClass::clearDraftBookings();
            $booking->addMeta('duration', $this->duration);
            $booking->addMeta('base_price', $this->price);
            $booking->addMeta('sale_price', $this->sale_price);
            $booking->addMeta('extra_price', $extra_price);
            $booking->addMeta('tmp_dates', $this->tmp_dates);
            if($this->isDepositEnable())
            {
                $booking->addMeta('deposit_info',[
                    'type'=>$this->getDepositType(),
                    'amount'=>$this->getDepositAmount(),
                    'fomular'=>$this->getDepositFomular(),
                ]);
            }
            if ($pickupLocation = $request->attributes->get('transfer_pickup_location')) {
                /** @var CarPickupLocation|TransferLocation $pickupLocation */
                $booking->addMeta('transfer_pickup_location_id', $pickupLocation->id);
            }

            if ($pickupPayload) {
                $booking->addMeta('transfer_pickup_location', $pickupPayload);
            }

            if ($dropoffPayload) {
                $booking->addMeta('transfer_dropoff', $dropoffPayload);
            }

            if ($userPickupPayload) {
                $booking->addMeta('transfer_user_pickup', $userPickupPayload);
            }

            if ($this->hasTransferContext()) {
                $booking->addMeta('transfer_distance_km', $this->transferContext['route_distance']);
                $booking->addMeta('transfer_duration_min', $this->transferContext['route_duration']);
                $booking->addMeta('transfer_datetime', $request->input('transfer_datetime'));
                $booking->addMeta('transfer_pricing_mode', $this->transferContext['pricing_mode']);
                $booking->addMeta('transfer_unit_price', $this->transferContext['unit_price']);
                if ($this->transferContext['base_fee'] !== null) {
                    $booking->addMeta('transfer_base_fee', $this->transferContext['base_fee']);
                }
                if ($this->transferContext['price_single'] !== null) {
                    $booking->addMeta('transfer_price_single', $this->transferContext['price_single']);
                }
                if (!empty($this->transferContext['passengers'])) {
                    $booking->addMeta('transfer_passengers', $this->transferContext['passengers']);
                }
                $booking->addMeta('transfer_price', $this->transferContext['price']);
            }

            $this->clearTransferContext();
            return $this->sendSuccess([
                'url' => $booking->getCheckoutUrl(),
                'booking_code' => $booking->code,
            ]);
        }
        return $this->sendError(__("Can not check availability"));
    }

    public function addToCartValidate(Request $request)
    {
        $rules = [
            'number' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d'
        ];

        // Validation
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError('', ['errors' => $validator->errors()]);
            }

        }
        $total_number = $request->input('number');

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if(strtotime($start_date) < strtotime(date('Y-m-d 00:00:00')) or strtotime($start_date) > strtotime($end_date))
        {
            return $this->sendError(__("Your selected dates are not valid"));
        }

        // Validate Date and Booking
        if(!$this->isAvailableInRanges($start_date,$end_date,$total_number)){
            return $this->sendError(__("This car is not available at selected dates"));
        }

        $numberDays = ( abs(strtotime($end_date) - strtotime($start_date)) / 86400 ) + 1;
        if(!empty($this->min_day_stays) and  $numberDays < $this->min_day_stays){
            return $this->sendError(__("You must to book a minimum of :number days",['number'=>$this->min_day_stays]));
        }

        if(!empty($this->min_day_before_booking)){
            $minday_before = strtotime("today +".$this->min_day_before_booking." days");
            if(  strtotime($start_date) < $minday_before){
                return $this->sendError(__("You must book the service for :number days in advance",["number"=>$this->min_day_before_booking]));
            }
        }

        $this->clearTransferContext();
        $passengers = (int) $total_number;
        if ($passengers < 1) {
            return $this->sendError(__('transfers.booking.passengers_invalid'));
        }
        $maxPassengers = (int) ($this->number ?? 0);
        if ($maxPassengers > 0 && $passengers > $maxPassengers) {
            return $this->sendError(__('transfers.booking.passengers_invalid'));
        }
        $pickupPayload = $request->input('pickup');
        if (is_string($pickupPayload)) {
            $decoded = json_decode($pickupPayload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $pickupPayload = $decoded;
            }
        }
        if (!is_array($pickupPayload)) {
            $pickupPayload = [];
        }

        $pickupLocationId = $request->input('pickup_location_id') ?? Arr::get($pickupPayload, 'id');
        $pickupLocation = null;
        if ($pickupLocationId) {
            $pickupLocation = CarPickupLocation::query()
                ->availableForCar($this)
                ->where('id', $pickupLocationId)
                ->first();

            if (!$pickupLocation) {
                return $this->sendError(__('transfers.booking.invalid_pickup_location'));
            }
        }

        if (empty($pickupPayload) && $pickupLocation) {
            $pickupPayload = $pickupLocation->toFrontendArray();
        }

        $dropoff = $request->input('dropoff');
        if (is_string($dropoff)) {
            $decodedDropoff = json_decode($dropoff, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $dropoff = $decodedDropoff;
            }
        }
        if (!is_array($dropoff)) {
            $dropoff = [];
        }

        $userPickup = $request->input('user_pickup');
        if (is_string($userPickup)) {
            $decodedUserPickup = json_decode($userPickup, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $userPickup = $decodedUserPickup;
            }
        }
        if (!is_array($userPickup)) {
            $userPickup = [];
        }

        $pickupLat = static::toFloat(Arr::get($pickupPayload, 'lat', $pickupLocation?->lat));
        $pickupLng = static::toFloat(Arr::get($pickupPayload, 'lng', $pickupLocation?->lng));
        if ($pickupLat === null || $pickupLng === null) {
            return $this->sendError(__('transfers.booking.pickup_required'));
        }

        $dropoffLat = static::toFloat(Arr::get($dropoff, 'lat'));
        $dropoffLng = static::toFloat(Arr::get($dropoff, 'lng'));
        if ($dropoffLat === null || $dropoffLng === null) {
            return $this->sendError(__('transfers.booking.missing_dropoff'));
        }

        $userPickupLat = static::toFloat(Arr::get($userPickup, 'lat'));
        $userPickupLng = static::toFloat(Arr::get($userPickup, 'lng'));
        $userPickupPlaceId = Arr::get($userPickup, 'place_id');
        $normalizedUserPickup = null;
        if ($userPickupLat !== null && $userPickupLng !== null) {
            $normalizedUserPickup = array_merge($userPickup, [
                'lat' => $userPickupLat,
                'lng' => $userPickupLng,
            ]);
        }

        $pricingMode = $this->pricing_mode ?: 'per_km';
        if ($pricingMode !== 'fixed' && (!$normalizedUserPickup || empty($userPickupPlaceId))) {
            return $this->sendError(__('transfers.form.pickup_coordinates_required'));
        }

        $transferDatetime = $request->input('transfer_datetime');
        $transferDate = null;
        if ($transferDatetime) {
            try {
                $transferDate = Carbon::parse($transferDatetime, 'Asia/Tbilisi')->toDateString();
            } catch (\Exception $exception) {
                $transferDate = null;
            }
        }

        $metrics = static::resolveRouteMetrics(array_merge($pickupPayload, [
            'lat' => $pickupLat,
            'lng' => $pickupLng,
        ]), $dropoff);
        if ($metrics['distance_km'] === null) {
            return $this->sendError(__('transfers.booking.distance_error'));
        }

        $userRouteMetrics = null;
        if ($normalizedUserPickup) {
            $userRouteMetrics = static::resolveRouteMetrics($normalizedUserPickup, $dropoff);
        }

        if (!$this->applyTransferContext($pickupLocation, array_merge($pickupPayload, [
            'lat' => $pickupLat,
            'lng' => $pickupLng,
        ]), $dropoff, $metrics['distance_km'], $metrics['duration_min'], $transferDate, $transferDatetime, $passengers, $normalizedUserPickup, $userRouteMetrics['distance_km'] ?? null, $userRouteMetrics['duration_min'] ?? null)) {
            return $this->sendError(__('transfers.booking.unavailable_pickup'));
        }

        $this->tmp_price = $this->transferContext['price_single'] ?? $this->transferContext['price'];
        $request->attributes->set('transfer_pickup_location', $pickupLocation);
        $request->attributes->set('transfer_pickup_payload', $this->transferContext['pickup_location']);
        $request->attributes->set('transfer_dropoff', $dropoff);
        if ($normalizedUserPickup) {
            $request->attributes->set('transfer_user_pickup', $normalizedUserPickup);
        }
        $request->attributes->set('transfer_distance_km', $this->transferContext['route_distance']);
        $request->attributes->set('transfer_duration_min', $this->transferContext['route_duration']);
        $request->attributes->set('transfer_pricing_mode', $this->transferContext['pricing_mode']);
        $request->attributes->set('transfer_unit_price', $this->transferContext['unit_price']);
        $request->attributes->set('transfer_base_fee', $this->transferContext['base_fee']);
        $request->attributes->set('transfer_passengers', $this->transferContext['passengers']);

        return true;
    }

    public function beforeCheckout(Request $request, $booking)
    {
        if(!$this->isAvailableInRanges($booking->start_date,$booking->end_date,$booking->number)){
            return $this->sendError(__("This car is not available at selected dates"));
        }
    }

    public function isAvailableInRanges($start_date,$end_date,$number = 1){

        $allDates = [];

        $period = periodDate($start_date,$end_date);
        foreach ($period as $dt) {
            $allDates[$dt->format('Y-m-d')] = [
                'number'=>$this->number,
                'price'=>($this->sale_price && $this->sale_price < $this->price) ? $this->sale_price : $this->price,
                'status'=>$this->default_state
            ];
        }

        $datesData = $this->getDatesInRange($start_date,$end_date);

        if(!empty($datesData)){
            foreach ($datesData as $date)
            {
                if(empty($allDates[date('Y-m-d',strtotime($date->start_date))])) continue;
                if(!$date->active) return false;

                $rawCapacity = $date->number;
                if($rawCapacity === null || $rawCapacity === ''){
                    $rawCapacity = $this->number;
                }
                $capacity = is_numeric($rawCapacity) ? (int)$rawCapacity : null;
                if($capacity === null){
                    $capacity = (int)($this->number ?? 0);
                }
                if($capacity <= 0){
                    return false;
                }

                $priceForDate = static::toFloat($date->price);
                if ($priceForDate === null) {
                    $priceForDate = static::toFloat($this->price) ?? 0.0;
                }

                $allDates[date('Y-m-d',strtotime($date->start_date))] = [
                    'number'=>$capacity,
                    'price'=>$priceForDate,
                    'status'=>true
                ];
            }
        }

        $bookingData = $this->getBookingsInRange($start_date,$end_date);
        if(!empty($bookingData)){
            foreach ($bookingData as $booking){
                $period = periodDate($booking->start_date,$booking->end_date);
                foreach ($period as $dt) {
                   $date = $dt->format('Y-m-d');
                    if(!array_key_exists($date,$allDates)) continue;
                    $allDates[$date]['number'] -= $booking->number;
                    if($allDates[$date]['number'] <= 0){
                        return false;
                    }
                }
            }
        }

        if(empty($allDates)) return false;
        foreach ($allDates as $date=>$data)
        {
            if($data['number'] < $number){
                return false;
            }
        }

        $this->tmp_price = array_sum(array_column($allDates,'price'));
        $this->tmp_dates = $allDates;

        return true;
    }

    public function getDatesInRange($start_date,$end_date)
    {
        $query = $this->carDateClass::query();
        $query->where('target_id',$this->id);
        $query->where('start_date','>=',date('Y-m-d H:i:s',strtotime($start_date)));
        $query->where('end_date','<=',date('Y-m-d H:i:s',strtotime($end_date)));
        $query->orderBy('start_date');
        if ($this->author_id) {
            $query->orderByRaw('CASE WHEN create_user = ? THEN 0 ELSE 1 END ASC', [$this->author_id]);
        }

        return $query->take(100)->get();
    }

    public function getBookingData()
    {
        if (!empty($start = request()->input('start'))) {
            $start_html = display_date($start);
            $end_html = request()->input('end') ? display_date(request()->input('end')) : "";
            $date_html = $start_html . '<i class="fa fa-long-arrow-right" style="font-size: inherit"></i>' . $end_html;
        }
        $booking_data = [
            'id'              => $this->id,
            'extra_price'     => [],
            'minDate'         => date('m/d/Y'),
            'max_number'      => $this->number ?? 1,
            'buyer_fees'      => [],
            'start_date'      => request()->input('start') ?? "",
            'start_date_html' => $date_html ?? __('Please select date!'),
            'end_date'        => request()->input('end') ?? "",
            'deposit'=>$this->isDepositEnable(),
            'deposit_type'=>$this->getDepositType(),
            'deposit_amount'=>$this->getDepositAmount(),
            'deposit_fomular'=>$this->getDepositFomular(),
            'is_form_enquiry_and_book'=> $this->isFormEnquiryAndBook(),
            'enquiry_type'=> $this->getBookingEnquiryType(),
        ];
        $pickupLocationId = request()->input('pickup_location_id');
        if ($pickupLocationId) {
            $pickupLocation = CarPickupLocation::query()
                ->availableForCar($this)
                ->where('id', $pickupLocationId)
                ->first();

            if ($pickupLocation) {
                $booking_data['pickup_location_id'] = $pickupLocation->id;
                $booking_data['pickup_location'] = $pickupLocation->toFrontendArray();
            }
        }
        if ($dropoff = request()->input('dropoff')) {
            $booking_data['dropoff'] = $dropoff;
        }
        if ($transferDatetime = request()->input('transfer_datetime')) {
            $booking_data['transfer_datetime'] = $transferDatetime;
        }
        $lang = app()->getLocale();
        if ($this->enable_extra_price) {
            $booking_data['extra_price'] = $this->extra_price;
            if (!empty($booking_data['extra_price'])) {
                foreach ($booking_data['extra_price'] as $k => &$type) {
                    if (!empty($lang) and !empty($type['name_' . $lang])) {
                        $type['name'] = $type['name_' . $lang];
                    }
                    $type['number'] = 0;
                    $type['enable'] = 0;
                    $type['price_html'] = format_money($type['price']);
                    $type['price_type'] = '';
                    switch ($type['type']) {
                        case "per_day":
                            $type['price_type'] .= '/' . __('day');
                            break;
                        case "per_hour":
                            $type['price_type'] .= '/' . __('hour');
                            break;
                    }
                    if (!empty($type['per_person'])) {
                        $type['price_type'] .= '/' . __('guest');
                    }
                }
            }

            $booking_data['extra_price'] = array_values((array)$booking_data['extra_price']);
        }

        $list_fees = setting_item_array('car_booking_buyer_fees');
        if(!empty($list_fees)){
            foreach ($list_fees as $item){
                $item['type_name'] = $item['name_'.app()->getLocale()] ?? $item['name'] ?? '';
                $item['type_desc'] = $item['desc_'.app()->getLocale()] ?? $item['desc'] ?? '';
                $item['price_type'] = '';
                if (!empty($item['per_person']) and $item['per_person'] == 'on') {
                    $item['price_type'] .= '/' . __('guest');
                }
                $booking_data['buyer_fees'][] = $item;
            }
        }
        if(!empty($this->enable_service_fee) and !empty($service_fee = $this->service_fee)){
            foreach ($service_fee as $item) {
                $item['type_name'] = $item['name_' . app()->getLocale()] ?? $item['name'] ?? '';
                $item['type_desc'] = $item['desc_' . app()->getLocale()] ?? $item['desc'] ?? '';
                $item['price_type'] = '';
                if (!empty($item['per_person']) and $item['per_person'] == 'on') {
                    $item['price_type'] .= '/' . __('guest');
                }
                $booking_data['buyer_fees'][] = $item;
            }
        }
        return $booking_data;
    }

    public static function searchForMenu($q = false)
    {
        $query = static::select('id', 'title as name');
        if (strlen($q)) {

            $query->where('title', 'like', "%" . $q . "%");
        }
        $a = $query->orderBy('id', 'desc')->limit(10)->get();
        return $a;
    }

    public static function getMinMaxPrice()
    {
        $model = parent::selectRaw('MIN( CASE WHEN sale_price > 0 THEN sale_price ELSE ( price ) END ) AS min_price ,
                                    MAX( CASE WHEN sale_price > 0 THEN sale_price ELSE ( price ) END ) AS max_price ')->where("status", "publish")->first();
        if (empty($model->min_price) and empty($model->max_price)) {
            return [
                0,
                100
            ];
        }
        return [
            $model->min_price,
            $model->max_price
        ];
    }

    public function getReviewEnable()
    {
        return setting_item("car_enable_review", 0);
    }

    public function getReviewApproved()
    {
        return setting_item("car_review_approved", 0);
    }

    public function review_after_booking(){
        return setting_item("car_enable_review_after_booking", 0);
    }

    public function count_remain_review()
    {
        $status_making_completed_booking = [];
        $options = setting_item("car_allow_review_after_making_completed_booking", false);
        if (!empty($options)) {
            $status_making_completed_booking = json_decode($options);
        }
        $number_review = $this->reviewClass::countReviewByServiceID($this->id, Auth::id(), false, $this->type) ?? 0;
        $number_booking = $this->bookingClass::countBookingByServiceID($this->id, Auth::id(),$status_making_completed_booking) ?? 0;
        $number = $number_booking - $number_review;
        if($number < 0) $number = 0;
        return $number;
    }

    public static function getReviewStats()
    {
        $reviewStats = [];
        if (!empty($list = setting_item("car_review_stats", []))) {
            $list = json_decode($list, true);
            foreach ($list as $item) {
                $reviewStats[] = $item['title'];
            }
        }
        return $reviewStats;
    }

    public function getReviewDataAttribute()
    {
        $list_score = [
            'score_total'  => 0,
            'score_text'   => __("Not rated"),
            'total_review' => 0,
            'rate_score'   => [],
        ];
        $dataTotalReview = $this->reviewClass::selectRaw(" AVG(rate_number) as score_total , COUNT(id) as total_review ")->where('object_id', $this->id)->where('object_model', $this->type)->where("status", "approved")->first();
        if (!empty($dataTotalReview->score_total)) {
            $list_score['score_total'] = number_format($dataTotalReview->score_total, 1);
            $list_score['score_text'] = Review::getDisplayTextScoreByLever(round($list_score['score_total']));
        }
        if (!empty($dataTotalReview->total_review)) {
            $list_score['total_review'] = $dataTotalReview->total_review;
        }
        $list_data_rate = $this->reviewClass::selectRaw('COUNT( CASE WHEN rate_number = 5 THEN rate_number ELSE NULL END ) AS rate_5,
                                                            COUNT( CASE WHEN rate_number = 4 THEN rate_number ELSE NULL END ) AS rate_4,
                                                            COUNT( CASE WHEN rate_number = 3 THEN rate_number ELSE NULL END ) AS rate_3,
                                                            COUNT( CASE WHEN rate_number = 2 THEN rate_number ELSE NULL END ) AS rate_2,
                                                            COUNT( CASE WHEN rate_number = 1 THEN rate_number ELSE NULL END ) AS rate_1 ')->where('object_id', $this->id)->where('object_model', $this->type)->where("status", "approved")->first()->toArray();
        for ($rate = 5; $rate >= 1; $rate--) {
            if (!empty($number = $list_data_rate['rate_' . $rate])) {
                $percent = ($number / $list_score['total_review']) * 100;
            } else {
                $percent = 0;
            }
            $list_score['rate_score'][$rate] = [
                'title'   => $this->reviewClass::getDisplayTextScoreByLever($rate),
                'total'   => $number,
                'percent' => round($percent),
            ];
        }
        return $list_score;
    }

    /**
     * Get Score Review
     *
     * Using for loop space
     */
    public function getScoreReview()
    {
        $car_id = $this->id;
        $list_score = Cache::rememberForever('review_'.$this->type.'_' . $car_id, function () use ($car_id) {
            $dataReview = $this->reviewClass::selectRaw(" AVG(rate_number) as score_total , COUNT(id) as total_review ")->where('object_id', $car_id)->where('object_model', "car")->where("status", "approved")->first();
            $score_total = !empty($dataReview->score_total) ? number_format($dataReview->score_total, 1) : 0;
            return [
                'score_total'  => $score_total,
                'total_review' => !empty($dataReview->total_review) ? $dataReview->total_review : 0,
            ];
        });
        $list_score['review_text'] =  $list_score['score_total'] ? Review::getDisplayTextScoreByLever( round( $list_score['score_total'] )) : __("Not rated");
        return $list_score;
    }

    public function getNumberReviewsInService($status = false)
    {
        return $this->reviewClass::countReviewByServiceID($this->id, false, $status,$this->type) ?? 0;
    }

    public function getReviewList(){
        return $this->reviewClass::select(['id','title','content','rate_number','author_ip','status','created_at','vendor_id','author_id'])->where('object_id', $this->id)->where('object_model', 'car')->where("status", "approved")->orderBy("id", "desc")->with('author')->paginate(setting_item('car_review_number_per_page', 5));
    }

    public function getNumberServiceInLocation($location)
    {
        $number = 0;
        if(!empty($location)) {
            $number = parent::join('bravo_locations', function ($join) use ($location) {
                $join->on('bravo_locations.id', '=', $this->table.'.location_id')->where('bravo_locations._lft', '>=', $location->_lft)->where('bravo_locations._rgt', '<=', $location->_rgt);
            })->where($this->table.".status", "publish")->with(['translation'])->count($this->table.".id");
        }
        if(empty($number)) return false;
        if ($number > 1) {
            return __(":number Cars", ['number' => $number]);
        }
        return __(":number Car", ['number' => $number]);
    }

    /**
     * @param $from
     * @param $to
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getBookingsInRange($from,$to){

        $query = $this->bookingClass::query();
        $query->whereNotIn('status',$this->bookingClass::$notAcceptedStatus);
        $query->where('start_date','<=',$to)->where('end_date','>=',$from)->take(100);

        $query->where('object_id',$this->id);
        $query->where('object_model',$this->type);

        return $query->orderBy('id','asc')->get();

    }

    public function saveCloneByID($clone_id){
        $old = parent::find($clone_id);
        if(empty($old)) return false;
        $selected_terms = $old->terms->pluck('term_id');
        $old->title = $old->title." - Copy";
        $new = $old->replicate();
        $new->save();
        //Terms
        foreach ($selected_terms as $term_id) {
            $this->carTermClass::firstOrCreate([
                'term_id' => $term_id,
                'target_id' => $new->id
            ]);
        }
        //Language
        $langs = $this->carTranslationClass::where("origin_id",$old->id)->get();
        if(!empty($langs)){
            foreach ($langs as $lang){
                $langNew = $lang->replicate();
                $langNew->origin_id = $new->id;
                $langNew->save();
                $langSeo = SEO::where('object_id', $lang->id)->where('object_model', $lang->getSeoType()."_".$lang->locale)->first();
                if(!empty($langSeo)){
                    $langSeoNew = $langSeo->replicate();
                    $langSeoNew->object_id = $langNew->id;
                    $langSeoNew->save();
                }
            }
        }
        //SEO
        $metaSeo = SEO::where('object_id', $old->id)->where('object_model', $this->seo_type)->first();
        if(!empty($metaSeo)){
            $metaSeoNew = $metaSeo->replicate();
            $metaSeoNew->object_id = $new->id;
            $metaSeoNew->save();
        }
    }

    public function hasWishList(){
        return $this->hasOne($this->userWishListClass, 'object_id','id')->where('object_model' , $this->type)->where('user_id' , Auth::id() ?? 0);
    }

    public function isWishList()
    {
        if(Auth::check()){
            if(!empty($this->hasWishList) and !empty($this->hasWishList->id)){
                return 'active';
            }
        }
        return '';
    }
    public static function getServiceIconFeatured(){
        return "icofont-car";
    }

    public static function isEnable(){
        return setting_item('car_disable') == false;
    }


    public function getBookingInRanges($object_id,$object_model,$from,$to,$object_child_id = false){

        $query = $this->bookingClass::selectRaw(" * , SUM( number ) as total_numbers ")->where([
            'object_id'=>$object_id,
            'object_model'=>$object_model,
        ])->whereNotIn('status',$this->bookingClass::$notAcceptedStatus)
            ->where('end_date','>=',$from)
            ->where('start_date','<=',$to)
            ->groupBy('start_date')
            ->take(200);

        if($object_child_id){
            $query->where('object_child_id',$object_child_id);
        }

        return $query->get();
    }

    public function isDepositEnable(){
        return (setting_item('car_deposit_enable') and setting_item('car_deposit_amount'));
    }
    public function getDepositAmount(){
        return setting_item('car_deposit_amount');
    }
    public function getDepositType(){
        return setting_item('car_deposit_type');
    }
    public function getDepositFomular(){
        return setting_item('car_deposit_fomular','default');
    }
	public function detailBookingEachDate($booking){
		$startDate = $booking->start_date;
		$endDate = $booking->end_date;
		$rowDates= json_decode($booking->getMeta('tmp_dates'));

		$allDates=[];
		$service = $booking->service;
        $period = periodDate($startDate,$endDate);
        foreach ($period as $dt) {

			$price = (!empty($service->sale_price) and $service->sale_price > 0 and $service->sale_price < $service->price) ? $service->sale_price : $service->price;
			$date['price'] =$price;
			$date['price_html'] = format_money($price);
			$date['from'] = $dt->getTimestamp();
			$date['from_html'] = $dt->format('d/m/Y');
			$date['to'] = $dt->getTimestamp();
			$date['to_html'] = $dt->format('d/m/Y');
			$allDates[$dt->format(('Y-m-d'))] = $date;
		}

		if(!empty($rowDates))
		{
			foreach ($rowDates as $item => $row)
			{
				$startDate = strtotime($item);
				$price = $row->price;
				$date['price'] = $price;
				$date['price_html'] = format_money($price);
				$date['from'] = $startDate;
				$date['from_html'] = date('d/m/Y',$startDate);
				$date['to'] = $startDate;
				$date['to_html'] = date('d/m/Y',($startDate));
				$allDates[date('Y-m-d',$startDate)] = $date;
			}
		}
		return $allDates;
	}

    public static function isEnableEnquiry(){
        if(!empty(setting_item('booking_enquiry_for_car'))){
            return true;
        }
        return false;
    }
    public static function isFormEnquiryAndBook(){
        $check = setting_item('booking_enquiry_for_car');
        if(!empty($check) and setting_item('booking_enquiry_type_car') == "booking_and_enquiry" ){
            return true;
        }
        return false;
    }
    public static function getBookingEnquiryType(){
        $check = setting_item('booking_enquiry_for_car');
        if(!empty($check)){
            if( setting_item('booking_enquiry_type_car') == "only_enquiry" ) {
                return "enquiry";
            }
        }
        return "book";
    }


    /**
     * @param $request
     * [location_id] -> number
     * [s] -> keyword
     * @return array|\Illuminate\Database\Eloquent\Builder
     */
    public function search($request)
    {
        $query = parent::query()->select("bravo_cars.*");
        $query->where("bravo_cars.status", "publish");
        if (!empty($location_id = $request['location_id'] ?? "" )) {
            $location = Location::query()->where('id', $location_id)->where("status","publish")->first();
            if(!empty($location)){
                $query->join('bravo_locations', function ($join) use ($location) {
                    $join->on('bravo_locations.id', '=', 'bravo_cars.location_id')
                        ->where('bravo_locations._lft', '>=', $location->_lft)
                        ->where('bravo_locations._rgt', '<=', $location->_rgt);
                });
            }
        }
        if (!empty($price_range = $request['price_range'] ?? "")) {
            $pri_from = Currency::convertPriceToMain(explode(";", $price_range)[0]);
            $pri_to =  Currency::convertPriceToMain(explode(";", $price_range)[1]);
            $raw_sql_min_max = "( (IFNULL(bravo_cars.sale_price,0) > 0 and bravo_cars.sale_price >= ? ) OR (IFNULL(bravo_cars.sale_price,0) <= 0 and bravo_cars.price >= ? ) )
                            AND ( (IFNULL(bravo_cars.sale_price,0) > 0 and bravo_cars.sale_price <= ? ) OR (IFNULL(bravo_cars.sale_price,0) <= 0 and bravo_cars.price <= ? ) )";
            $query->WhereRaw($raw_sql_min_max,[$pri_from,$pri_from,$pri_to,$pri_to]);
        }

        if($term_id = $request['term_id'] ?? "")
        {
            $query->join('bravo_car_term as tt1', function($join) use ($term_id){
                $join->on('tt1.target_id', "bravo_cars.id");
                $join->where('tt1.term_id', $term_id);
            });
        }

        if(!empty($request['attrs'])){
            $this->filterAttrs($query,$request['attrs'],'bravo_car_term');
        }

        $review_scores = $request["review_score"] ?? "";
        if (is_array($review_scores)) $review_scores = array_filter($review_scores);
        if (!empty($review_scores) && count($review_scores)) {
            $this->filterReviewScore($query,$review_scores);
        }

        if(!empty( $service_name = $request['service_name'] ?? "" )){
            if( setting_item('site_enable_multi_lang') && setting_item('site_locale') != app()->getLocale() ){
                $query->leftJoin('bravo_car_translations', function ($join) {
                    $join->on('bravo_cars.id', '=', 'bravo_car_translations.origin_id');
                });
                $query->where('bravo_car_translations.title', 'LIKE', '%' . $service_name . '%');

            }else{
                $query->where('bravo_cars.title', 'LIKE', '%' . $service_name . '%');
            }
        }

        if(!empty($lat = $request["map_lat"] ?? "") and !empty($lgn = $request["map_lgn"] ?? "") and !empty($request["map_place"] ?? ""))
        {
            $this->filterLatLng($query,$lat,$lgn);
        }

        if(!empty($request['is_featured']))
        {
            $query->where('bravo_cars.is_featured',1);
        }
        if (!empty($request['custom_ids']) and !empty( $ids = array_filter($request['custom_ids']) )) {
            $query->whereIn("bravo_cars.id", $ids);
            $query->orderByRaw('FIELD (' . $query->qualifyColumn("id") . ', ' . implode(', ', $ids) . ') ASC');
        }

        if ($pickupLocationId = $request['pickup_location_id'] ?? null) {
            $pickupLocation = CarPickupLocation::query()
                ->active()
                ->where('id', $pickupLocationId)
                ->first();

            if ($pickupLocation) {
                if ($pickupLocation->car_id) {
                    $query->where($query->qualifyColumn('id'), $pickupLocation->car_id);
                } elseif ($pickupLocation->vendor_id) {
                    $query->where($query->qualifyColumn('author_id'), $pickupLocation->vendor_id);
                }
            }
        }

        $pickupFilter = $request['pickup'] ?? null;
        if (is_string($pickupFilter)) {
            $decodedPickup = json_decode($pickupFilter, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $pickupFilter = $decodedPickup;
            }
        }
        if (is_array($pickupFilter)) {
            $pickupLat = isset($pickupFilter['lat']) && is_numeric($pickupFilter['lat']) ? (float) $pickupFilter['lat'] : null;
            $pickupLng = isset($pickupFilter['lng']) && is_numeric($pickupFilter['lng']) ? (float) $pickupFilter['lng'] : null;
            $pickupSource = Arr::get($pickupFilter, 'source');
            $isBackendPickup = ($pickupSource === 'backend') || Arr::get($pickupFilter, 'id');

            if ($pickupLat !== null && $pickupLng !== null && !$isBackendPickup) {
                $latColumn = $query->qualifyColumn('service_center_lat');
                $lngColumn = $query->qualifyColumn('service_center_lng');
                $radiusColumn = $query->qualifyColumn('service_radius_km');
                $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(' . $latColumn . ')) * cos(radians(' . $lngColumn . ') - radians(?)) + sin(radians(?)) * sin(radians(' . $latColumn . '))))';
                $query->whereNotNull('service_center_lat')
                    ->whereNotNull('service_center_lng')
                    ->where($radiusColumn, '>', 0)
                    ->whereRaw($haversine . ' <= ' . $radiusColumn, [$pickupLat, $pickupLng, $pickupLat]);
            }
        }
        $orderby = $request['orderby'] ?? "";
        switch ($orderby){
            case "price_low_high":
                $raw_sql = "CASE WHEN IFNULL( bravo_cars.sale_price, 0 ) > 0 THEN bravo_cars.sale_price ELSE bravo_cars.price END AS tmp_min_price";
                $query->selectRaw($raw_sql);
                $query->orderBy("tmp_min_price", "asc");
                break;
            case "price_high_low":
                $raw_sql = "CASE WHEN IFNULL( bravo_cars.sale_price, 0 ) > 0 THEN bravo_cars.sale_price ELSE bravo_cars.price END AS tmp_min_price";
                $query->selectRaw($raw_sql);
                $query->orderBy("tmp_min_price", "desc");
                break;
            case "rate_high_low":
                $query->orderBy("review_score", "desc");
                break;
            default:
                if(!empty($request['order']) and !empty($request['order_by'])){
                    $query->orderBy("bravo_cars.".$request['order'], $request['order_by']);
                }else{
                    $query->orderBy($query->qualifyColumn("is_featured"), "desc");
                    $query->orderBy($query->qualifyColumn("id"), "desc");
                }
        }

        $query->groupBy("bravo_cars.id");

        $max_guests = (int)( ($request['adults'] ?? 0) + ($request['children'] ?? 0));
        if($max_guests){
            $query->where('max_guests','>=',$max_guests);
        }

        return $query->with(['location','hasWishList','translation','pickupLocations' => function ($query) {
            $query->active();
        }]);
    }

    public function dataForApi($forSingle = false){
        $data = parent::dataForApi($forSingle);
        $data['passenger'] = $this->passenger;
        $data['gear'] = $this->gear;
        $data['baggage'] = $this->baggage;
        $data['door'] = $this->door;
        if($forSingle){
            $data['review_score'] = $this->getReviewDataAttribute();
            $data['review_stats'] = $this->getReviewStats();
            $data['review_lists'] = $this->getReviewList();
            $data['faqs'] = $this->faqs;
            $data['is_instant'] = $this->is_instant;
            $data['number'] = $this->number;
            $data['discount_by_days'] = $this->discount_by_days;
            $data['default_state'] = $this->default_state;
            $data['booking_fee'] = setting_item_array('car_booking_buyer_fees');
            if (!empty($location_id = $this->location_id)) {
                $related =  parent::query()->where('location_id', $location_id)->where("status", "publish")->take(4)->whereNotIn('id', [$this->id])->with(['location','translation','hasWishList'])->get();
                $data['related'] = $related->map(function ($related) {
                        return $related->dataForApi();
                    }) ?? null;
            }
            $data['terms'] = Terms::getTermsByIdForAPI($this->terms->pluck('term_id'));
        }else{
            $data['review_score'] = $this->getScoreReview();
        }
        return $data;
    }

    static public function getClassAvailability()
    {
        return "\Modules\Car\Controllers\AvailabilityController";
    }

    static public function getFiltersSearch()
    {
        $min_max_price = self::getMinMaxPrice();
        return [
            [
                "title"    => __("Filter Price"),
                "field"    => "price_range",
                "position" => "1",
                "min_price" => floor ( Currency::convertPrice($min_max_price[0]) ),
                "max_price" => ceil (Currency::convertPrice($min_max_price[1]) ),
            ],
            [
                "title"    => __("Review Score"),
                "field"    => "review_score",
                "position" => "2",
                "min" => "1",
                "max" => "5",
            ],
            [
                "title"    => __("Attributes"),
                "field"    => "terms",
                "position" => "3",
                "data" => Attributes::getAllAttributesForApi("car")
            ]
        ];
    }

    static public function getFormSearch()
    {
        $search_fields = setting_item_array('car_search_fields');
        $search_fields = array_values(\Illuminate\Support\Arr::sort($search_fields, function ($value) {
            return $value['position'] ?? 0;
        }));
        foreach ( $search_fields as &$item){
            if($item['field'] == 'attr' and !empty($item['attr']) ){
                $attr = Attributes::find($item['attr']);
                $item['attr_title'] = $attr->translate()->name;
                foreach($attr->terms as $term)
                {
                    $translate = $term->translate();
                    $item['terms'][] =  [
                        'id' => $term->id,
                        'title' => $translate->name,
                    ];
                }
            }
        }
        return $search_fields;
    }

    public static function getAvailablePickupLocations()
    {
        return CarPickupLocation::query()
            ->availableForCar(null)
            ->orderByRaw("CASE WHEN name IS NULL OR name = '' THEN 1 ELSE 0 END")
            ->orderByRaw("CASE WHEN address IS NULL OR address = '' THEN 1 ELSE 0 END")
            ->orderBy('name')
            ->orderBy('address')
            ->orderBy('id')
            ->get();
    }

    public function applyTransferContext(
        ?CarPickupLocation $pickupLocation,
        array $pickupPayload,
        array $dropoff,
        ?float $routeDistanceKm = null,
        ?float $routeDurationMin = null,
        ?string $transferDate = null,
        ?string $transferDatetime = null,
        ?int $passengers = null,
        ?array $userPickup = null,
        ?float $userRouteDistanceKm = null,
        ?float $userRouteDurationMin = null
    ): bool
    {
        $pickupLat = static::toFloat(Arr::get($pickupPayload, 'lat', $pickupLocation?->lat));
        $pickupLng = static::toFloat(Arr::get($pickupPayload, 'lng', $pickupLocation?->lng));
        $dropoffLat = static::toFloat(Arr::get($dropoff, 'lat'));
        $dropoffLng = static::toFloat(Arr::get($dropoff, 'lng'));

        if ($pickupLat === null || $pickupLng === null || $dropoffLat === null || $dropoffLng === null) {
            return false;
        }

        $normalizedUserPickup = null;
        if (is_array($userPickup)) {
            $userPickupLat = static::toFloat(Arr::get($userPickup, 'lat'));
            $userPickupLng = static::toFloat(Arr::get($userPickup, 'lng'));
            if ($userPickupLat !== null && $userPickupLng !== null) {
                $normalizedUserPickup = array_merge([
                    'formatted_address' => Arr::get($userPickup, 'formatted_address') ?: Arr::get($userPickup, 'address'),
                    'address' => Arr::get($userPickup, 'address')
                        ?: Arr::get($userPickup, 'formatted_address')
                        ?: Arr::get($userPickup, 'name'),
                    'name' => Arr::get($userPickup, 'name')
                        ?: Arr::get($userPickup, 'formatted_address')
                        ?: Arr::get($userPickup, 'address'),
                    'place_id' => Arr::get($userPickup, 'place_id'),
                ], $userPickup, [
                    'lat' => $userPickupLat,
                    'lng' => $userPickupLng,
                ]);
            }
        }

        $radiusLat = $normalizedUserPickup['lat'] ?? $pickupLat;
        $radiusLng = $normalizedUserPickup['lng'] ?? $pickupLng;

        if (!$this->isWithinServiceRadius($radiusLat, $radiusLng)) {
            return false;
        }

        $radiusLimit = static::toFloat($this->service_radius_km);
        $pricingMode = $this->pricing_mode ?: 'per_km';
        $distance = null;
        $duration = null;

        if ($pricingMode === 'fixed') {
            $distance = $routeDistanceKm;
            $duration = $routeDurationMin;
            if ($distance === null || $duration === null) {
                $resolvedMetrics = static::resolveRouteMetrics(
                    array_merge($pickupLocation?->toFrontendArray() ?? [], $pickupPayload, [
                        'lat' => $pickupLat,
                        'lng' => $pickupLng,
                    ]),
                    $dropoff
                );
                if ($distance === null) {
                    $distance = $resolvedMetrics['distance_km'];
                }
                if ($duration === null) {
                    $duration = $resolvedMetrics['duration_min'];
                }
            }
        } else {
            $pricingMode = 'per_km';
            if ($normalizedUserPickup === null) {
                return false;
            }
            $distance = $userRouteDistanceKm;
            $duration = $userRouteDurationMin;
            if ($distance === null || $duration === null) {
                $resolvedMetrics = static::resolveRouteMetrics($normalizedUserPickup, $dropoff);
                if ($distance === null) {
                    $distance = $resolvedMetrics['distance_km'];
                }
                if ($duration === null) {
                    $duration = $resolvedMetrics['duration_min'];
                }
            }
        }

        if ($distance === null) {
            return false;
        }

        if ($radiusLimit !== null && $radiusLimit > 0 && $distance > $radiusLimit && $pricingMode === 'per_km') {
            return false;
        }

        $passengerCount = $passengers ?? 1;
        $passengerCount = (int) $passengerCount;
        if ($passengerCount < 1) {
            $passengerCount = 1;
        }
        $maxPassengers = (int) ($this->number ?? 0);
        if ($maxPassengers > 0 && $passengerCount > $maxPassengers) {
            $passengerCount = $maxPassengers;
        }

        $unitPrice = null;
        $singlePrice = null;
        $baseFee = null;
        $baseFeeValue = null;

        if (property_exists($this, 'base_fee') || isset($this->base_fee)) {
            $baseFeeValue = static::toFloat($this->base_fee);
            if ($baseFeeValue !== null && $baseFeeValue < 0) {
                $baseFeeValue = 0.0;
            }
        }

        if ($distance !== null) {
            $distance = round($distance, 2);
        }
        if ($duration !== null) {
            $duration = round($duration, 2);
        }

        if ($pricingMode !== 'fixed' && ($distance === null || $distance <= 0)) {
            return false;
        }

        if ($pricingMode === 'fixed') {
            $unitPrice = static::toFloat($this->fixed_price);
            if ($unitPrice === null || $unitPrice < 0) {
                return false;
            }
            $singlePrice = round(max($unitPrice, 0), 2);
        } else {
            $pricingMode = 'per_km';
            $unitPrice = static::toFloat($this->price_per_km);
            if ($unitPrice === null || $unitPrice <= 0) {
                return false;
            }
            $singlePrice = round($distance * $unitPrice, 2);
        }

        if ($singlePrice === null || $singlePrice < 0) {
            return false;
        }

        $basePortion = $baseFeeValue !== null ? round(max($baseFeeValue, 0), 2) : 0.0;
        $totalPrice = round(($singlePrice * $passengerCount) + $basePortion, 2);
        if ($pricingMode === 'per_km' && $singlePrice <= 0 && $basePortion <= 0) {
            return false;
        }
        if ($totalPrice <= 0) {
            return false;
        }

        $baseFee = $baseFeeValue !== null ? $basePortion : null;

        $pickupPayload = array_merge([
            'id' => $pickupLocation?->id,
            'name' => Arr::get($pickupPayload, 'name', $pickupLocation?->name),
            'source' => Arr::get($pickupPayload, 'source', $pickupLocation ? 'backend' : null),
        ], $pickupPayload, [
            'lat' => $pickupLat,
            'lng' => $pickupLng,
        ]);

        $this->setTransferContext([
            'price' => $totalPrice,
            'price_single' => $singlePrice,
            'route_distance' => $distance,
            'route_duration' => $duration,
            'pickup_location' => $pickupPayload,
            'user_pickup' => $normalizedUserPickup,
            'dropoff' => $dropoff,
            'pickup_location_id' => $pickupLocation?->id,
            'transfer_datetime' => $transferDatetime,
            'pricing_mode' => $pricingMode,
            'unit_price' => $unitPrice,
            'base_fee' => $baseFee,
            'passengers' => $passengerCount,
            'transfer_date' => $transferDate,
        ]);

        if ($transferDate) {
            try {
                $transferDay = Carbon::parse($transferDate, 'Asia/Tbilisi')->setTimezone('Asia/Tbilisi');
            } catch (\Exception $exception) {
                $this->clearTransferContext();
                return false;
            }

            $startOfDay = $transferDay->copy()->startOfDay();
            $endOfDay = $transferDay->copy()->endOfDay();

            if (!$this->isAvailableInRanges(
                $startOfDay->format('Y-m-d H:i:s'),
                $endOfDay->format('Y-m-d H:i:s'),
                $passengerCount
            )) {
                $this->clearTransferContext();
                return false;
            }
        }

        return true;
    }

    public function hasTransferContext(): bool
    {
        return $this->transferContext['price'] !== null;
    }

    public function isWithinServiceRadius(?float $lat, ?float $lng): bool
    {
        if ($lat === null || $lng === null) {
            return false;
        }

        $radius = static::toFloat($this->service_radius_km);
        $centerLat = static::toFloat($this->service_center_lat);
        $centerLng = static::toFloat($this->service_center_lng);

        if ($radius === null || $radius <= 0 || $centerLat === null || $centerLng === null) {
            return true;
        }

        return static::haversineDistance($centerLat, $centerLng, $lat, $lng) <= $radius;
    }

    public function getCalculatedPriceAttribute(): ?float
    {
        return $this->transferContext['price'];
    }

    public function getTransferDistanceKmAttribute(): ?float
    {
        return $this->transferContext['route_distance'];
    }

    public function getTransferDurationMinAttribute(): ?float
    {
        return $this->transferContext['route_duration'];
    }

    public function getTransferPricingModeAttribute(): ?string
    {
        return $this->transferContext['pricing_mode'];
    }

    public function getTransferUnitPriceAttribute(): ?float
    {
        return $this->transferContext['unit_price'];
    }

    public function getTransferBaseFeeAttribute(): ?float
    {
        return $this->transferContext['base_fee'];
    }

    public function getTransferPriceSingleAttribute(): ?float
    {
        return $this->transferContext['price_single'];
    }

    public function getTransferPassengersAttribute(): int
    {
        return (int) ($this->transferContext['passengers'] ?? 1);
    }

    public function getDisplayPriceAttribute()
    {
        if ($this->hasTransferContext()) {
            return format_money($this->transferContext['price']);
        }

        return parent::getDisplayPriceAttribute();
    }

    public function getDisplaySalePriceAttribute()
    {
        if ($this->hasTransferContext()) {
            return false;
        }

        return parent::getDisplaySalePriceAttribute();
    }

    public function clearTransferContext(): void
    {
        $this->transferContext = [
            'price' => null,
            'price_single' => null,
            'route_distance' => null,
            'route_duration' => null,
            'pickup_location' => null,
            'user_pickup' => null,
            'dropoff' => null,
            'pickup_location_id' => null,
            'transfer_datetime' => null,
            'transfer_date' => null,
            'pricing_mode' => null,
            'unit_price' => null,
            'base_fee' => null,
            'passengers' => 1,
        ];
    }

    protected function setTransferContext(array $context): void
    {
        $this->transferContext = array_merge($this->transferContext, $context);
    }

    public function getTransferPickupAttribute(): ?array
    {
        return $this->transferContext['pickup_location'];
    }

    public function getTransferDropoffAttribute(): ?array
    {
        return $this->transferContext['dropoff'];
    }

    public function getTransferUserPickupAttribute(): ?array
    {
        return $this->transferContext['user_pickup'];
    }

    public function getTransferPickupNameAttribute(): ?string
    {
        return Arr::get($this->transferContext['pickup_location'], 'name') ?: Arr::get($this->transferContext['pickup_location'], 'address');
    }

    public function getTransferDropoffNameAttribute(): ?string
    {
        return Arr::get($this->transferContext['dropoff'], 'name') ?: Arr::get($this->transferContext['dropoff'], 'address');
    }

    public function getTransferPickupLocationIdAttribute(): ?int
    {
        return $this->transferContext['pickup_location_id'];
    }

    public function getTransferDatetimeAttribute(): ?string
    {
        return $this->transferContext['transfer_datetime'];
    }

    public static function resolveRouteDistanceKm(array $pickup, array $dropoff): ?float
    {
        return static::resolveRouteMetrics($pickup, $dropoff)['distance_km'];
    }

    public static function resolveRouteMetrics(array $pickup, array $dropoff, array $options = []): array
    {
        $pickupLat = static::toFloat(Arr::get($pickup, 'lat'));
        $pickupLng = static::toFloat(Arr::get($pickup, 'lng'));
        $dropoffLat = static::toFloat(Arr::get($dropoff, 'lat'));
        $dropoffLng = static::toFloat(Arr::get($dropoff, 'lng'));

        if ($pickupLat === null || $pickupLng === null || $dropoffLat === null || $dropoffLng === null) {
            return [
                'distance_km' => null,
                'duration_min' => null,
            ];
        }

        $distanceKm = null;
        $durationMin = null;

        $apiKey = setting_item('map_gmap_key') ?: config('services.google.maps_api_key');
        if ($apiKey) {
            try {
                $requestParams = [
                    'origin' => $pickupLat . ',' . $pickupLng,
                    'destination' => $dropoffLat . ',' . $dropoffLng,
                    'key' => $apiKey,
                ];
                $waypoints = array_filter(array_map(function ($point) {
                    $lat = static::toFloat(Arr::get($point, 'lat'));
                    $lng = static::toFloat(Arr::get($point, 'lng'));
                    if ($lat === null || $lng === null) {
                        return null;
                    }
                    return $lat . ',' . $lng;
                }, Arr::get($options, 'waypoints', [])));
                if (!empty($waypoints)) {
                    $requestParams['waypoints'] = implode('|', array_map(function ($coord) {
                        return 'via:' . $coord;
                    }, $waypoints));
                }

                $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/directions/json', $requestParams);

                if ($response->successful()) {
                    $data = $response->json();
                    $legs = Arr::get($data, 'routes.0.legs', []);
                    if (is_array($legs) && !empty($legs)) {
                        $distanceAccumulator = 0;
                        $durationAccumulator = 0;
                        foreach ($legs as $leg) {
                            $legDistance = Arr::get($leg, 'distance.value');
                            $legDuration = Arr::get($leg, 'duration.value');
                            if ($legDistance !== null) {
                                $distanceAccumulator += max(0, (float) $legDistance);
                            }
                            if ($legDuration !== null) {
                                $durationAccumulator += max(0, (float) $legDuration);
                            }
                        }
                        if ($distanceAccumulator > 0) {
                            $distanceKm = (float) $distanceAccumulator / 1000;
                        }
                        if ($durationAccumulator > 0) {
                            $durationMin = round($durationAccumulator / 60, 2);
                        }
                    } else {
                        $distance = Arr::get($data, 'routes.0.legs.0.distance.value');
                        $duration = Arr::get($data, 'routes.0.legs.0.duration.value');
                        if ($distance !== null) {
                            $distanceKm = max(0, (float) $distance / 1000);
                        }
                        if ($duration !== null) {
                            $durationMin = max(0, round(((float) $duration) / 60, 2));
                        }
                    }
                    if (Arr::get($data, 'status') !== 'OK') {
                        Log::warning('Google Directions API returned status', ['status' => Arr::get($data, 'status'), 'error_message' => Arr::get($data, 'error_message')]);
                    }
                }
            } catch (\Throwable $exception) {
                Log::warning('Failed to call Google Directions API for car transfer search', ['exception' => $exception->getMessage()]);
            }
        }

        if ($distanceKm === null) {
            $distanceKm = static::haversineDistance($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);
        }

        if ($distanceKm !== null) {
            $distanceKm = round($distanceKm, 2);
        }
        if ($durationMin !== null) {
            $durationMin = round($durationMin, 2);
        }

        return [
            'distance_km' => $distanceKm,
            'duration_min' => $durationMin,
        ];
    }

    protected static function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Kilometers

        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $lonFrom = deg2rad($lng1);
        $lonTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $earthRadius * $angle;
    }

    protected static function toFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float)$value : null;
    }

    public function syncPickupLocationsFromArray(array $locations): void
    {
        $idsToKeep = [];
        foreach ($locations as $location) {
            $name = trim((string) Arr::get($location, 'name'));
            $address = trim((string) Arr::get($location, 'address'));
            $placeId = trim((string) Arr::get($location, 'place_id'));
            $lat = static::toFloat(Arr::get($location, 'lat'));
            $lng = static::toFloat(Arr::get($location, 'lng'));

            if (!$name || $lat === null || $lng === null) {
                continue;
            }

            $payload = [
                'name' => $name,
                'address' => $address ?: null,
                'place_id' => $placeId ?: null,
                'lat' => $lat,
                'lng' => $lng,
                'is_active' => Arr::get($location, 'is_active', true) ? true : false,
            ];

            $existingId = Arr::get($location, 'id');
            if ($existingId) {
                $pickup = $this->pickupLocations()->where('id', $existingId)->first();
                if ($pickup) {
                    $pickup->fill($payload);
                    $pickup->save();
                    $idsToKeep[] = $pickup->id;
                    continue;
                }
            }

            $newPickup = $this->pickupLocations()->create($payload);
            $idsToKeep[] = $newPickup->id;
        }

        if (!empty($idsToKeep)) {
            $this->pickupLocations()->whereNotIn('id', $idsToKeep)->delete();
        } else {
            $this->pickupLocations()->delete();
        }
    }
}
