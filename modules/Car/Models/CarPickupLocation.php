<?php

namespace Modules\Car\Models;

use Illuminate\Database\Eloquent\Model;

class CarPickupLocation extends Model
{
    protected $table = 'car_pickup_locations';

    protected $fillable = [
        'car_id',
        'name',
        'address',
        'lat',
        'lng',
        'base_price',
        'price_coefficient',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'base_price' => 'float',
        'price_coefficient' => 'float',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->car_id,
            'name' => $this->name,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'base_price' => $this->base_price,
            'price_coefficient' => $this->price_coefficient,
        ];
    }
}
