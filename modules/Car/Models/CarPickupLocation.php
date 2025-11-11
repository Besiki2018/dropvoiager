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
        'place_id',
        'lat',
        'lng',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'display_name',
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
            'display_name' => $this->display_name,
            'address' => $this->address,
            'place_id' => $this->place_id,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'is_active' => $this->is_active,
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }

        if ($this->address) {
            return $this->address;
        }

        if ($this->lat !== null && $this->lng !== null) {
            return sprintf('%.6f, %.6f', $this->lat, $this->lng);
        }

        return '';
    }
}
