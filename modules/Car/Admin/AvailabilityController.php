<?php
namespace Modules\Car\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Modules\Booking\Models\Booking;
use Modules\Car\Models\Car;
use Modules\Car\Models\CarDate;

class AvailabilityController extends \Modules\Car\Controllers\AvailabilityController
{
    protected $carClass;
    protected $carDateClass;
    protected $bookingClass;
    protected $indexView = 'Car::admin.availability';

    public function __construct(Car $carClass, CarDate $carDateClass, Booking $bookingClass)
    {
        $this->setActiveMenu(route('car.admin.index'));
        $this->middleware('dashboard');
        $this->carClass = $carClass;
        $this->carDateClass = $carDateClass;
        $this->bookingClass = $bookingClass;
    }

    public function updateSettings(Request $request, Car $car)
    {
        $this->checkPermission('car_update');

        $validator = Validator::make($request->all(), [
            'service_radius_km' => ['nullable', 'numeric', 'min:0'],
            'pricing_mode' => ['required', 'in:per_km,fixed'],
            'price_per_km' => ['nullable', 'numeric', 'min:0'],
            'fixed_price' => ['nullable', 'numeric', 'min:0'],
            'transfer_time_start' => ['nullable', 'date_format:H:i'],
            'transfer_time_end' => ['nullable', 'date_format:H:i'],
        ], [], [
            'service_radius_km' => __('transfers.admin.pricing.service_radius'),
            'pricing_mode' => __('transfers.admin.pricing.mode_label'),
            'price_per_km' => __('transfers.admin.pricing.price_per_km'),
            'fixed_price' => __('transfers.admin.pricing.fixed_price'),
            'transfer_time_start' => __('transfers.admin.pricing.time_start'),
            'transfer_time_end' => __('transfers.admin.pricing.time_end'),
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $data = $validator->validated();
        $pricingMode = Arr::get($data, 'pricing_mode', 'per_km');

        if ($pricingMode === 'per_km') {
            $pricePerKm = Arr::get($data, 'price_per_km');
            if ($pricePerKm === null || $pricePerKm <= 0) {
                return $this->sendError(__('transfers.admin.pricing.price_per_km_required'));
            }
        } elseif ($pricingMode === 'fixed') {
            $fixedPrice = Arr::get($data, 'fixed_price');
            if ($fixedPrice === null || $fixedPrice <= 0) {
                return $this->sendError(__('transfers.admin.pricing.fixed_price_required'));
            }
        }

        $car->service_radius_km = Arr::get($data, 'service_radius_km');
        $car->pricing_mode = $pricingMode;
        $car->price_per_km = $pricingMode === 'per_km' ? Arr::get($data, 'price_per_km') : null;
        $car->fixed_price = $pricingMode === 'fixed' ? Arr::get($data, 'fixed_price') : null;
        $car->transfer_time_start = Arr::get($data, 'transfer_time_start');
        $car->transfer_time_end = Arr::get($data, 'transfer_time_end');
        $car->save();
        $car->refresh();

        return $this->sendSuccess([
            'car' => [
                'id' => $car->id,
                'service_radius_km' => $car->service_radius_km,
                'pricing_mode' => $car->pricing_mode,
                'price_per_km' => $car->price_per_km,
                'fixed_price' => $car->fixed_price,
                'transfer_time_start' => $car->transfer_time_start,
                'transfer_time_end' => $car->transfer_time_end,
            ],
        ], __('transfers.admin.pricing.settings_updated'));
    }

}
