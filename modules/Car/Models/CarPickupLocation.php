<?php

namespace Modules\Car\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CarPickupLocation extends Model
{
    protected $table = 'car_pickup_locations';

    protected $fillable = [
        'car_id',
        'vendor_id',
        'name',
        'address',
        'place_id',
        'lat',
        'lng',
        'map_zoom',
        'service_center_name',
        'service_center_address',
        'service_center_lat',
        'service_center_lng',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'map_zoom' => 'integer',
        'service_center_lat' => 'float',
        'service_center_lng' => 'float',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'display_name',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForOwner($query, int $userId)
    {
        return $query->where(function ($builder) use ($userId) {
            $builder->where('vendor_id', $userId)
                ->orWhereHas('car', function ($carQuery) use ($userId) {
                    $carQuery->where('author_id', $userId);
                });
        });
    }

    public function scopeAvailableForCar($query, ?Car $car = null)
    {
        $query->active();

        if ($car) {
            $vendorId = $car->author_id;

            $query->where(function ($builder) use ($car, $vendorId) {
                $builder->where('car_id', $car->id)
                    ->orWhere(function ($inner) use ($vendorId) {
                        $inner->whereNull('car_id');

                        if ($vendorId) {
                            $inner->where(function ($vendorClause) use ($vendorId) {
                                $vendorClause->whereNull('vendor_id')
                                    ->orWhere('vendor_id', $vendorId);
                            });
                        } else {
                            $inner->whereNull('vendor_id');
                        }
                    });
            });
        } else {
            $query->whereNull('car_id')
                ->whereNull('vendor_id');
        }

        return $query;
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->car_id,
            'vendor_id' => $this->vendor_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'address' => $this->address,
            'place_id' => $this->place_id,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'map_zoom' => $this->map_zoom,
            'service_center_name' => $this->service_center_name,
            'service_center_address' => $this->service_center_address,
            'service_center_lat' => $this->service_center_lat,
            'service_center_lng' => $this->service_center_lng,
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
