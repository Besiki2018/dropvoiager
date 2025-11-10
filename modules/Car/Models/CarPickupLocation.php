<?php

namespace Modules\Car\Models;

use Illuminate\Database\Eloquent\Model;

class CarPickupLocation extends Model
{
    protected $table = 'car_pickup_locations';

    protected $fillable = [
        'car_id',
        'name',
        'lat',
        'lng',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'is_active' => 'boolean',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->car_id,
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'is_active' => $this->is_active,
        ];
    }
}
