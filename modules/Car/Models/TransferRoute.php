<?php

namespace Modules\Car\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferRoute extends Model
{
    use SoftDeletes;

    protected $table = 'car_transfer_routes';

    protected $fillable = [
        'name',
        'pickup_name',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'dropoff_name',
        'dropoff_address',
        'dropoff_lat',
        'dropoff_lng',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'pickup_lat' => 'float',
        'pickup_lng' => 'float',
        'dropoff_lat' => 'float',
        'dropoff_lng' => 'float',
        'sort_order' => 'int',
    ];

    public const STATUS_PUBLISH = 'publish';

    public function scopePublished($query)
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_PUBLISH);
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?: $this->pickup_name . ' → ' . $this->dropoff_name,
            'pickup' => [
                'name' => $this->pickup_name,
                'address' => $this->pickup_address,
                'lat' => $this->pickup_lat,
                'lng' => $this->pickup_lng,
            ],
            'dropoff' => [
                'name' => $this->dropoff_name,
                'address' => $this->dropoff_address,
                'lat' => $this->dropoff_lat,
                'lng' => $this->dropoff_lng,
            ],
        ];
    }

    public function pickupPayload(): array
    {
        $payload = $this->toFrontendArray()['pickup'];
        $payload['route_id'] = $this->id;
        return $payload;
    }

    public function dropoffPayload(): array
    {
        $payload = $this->toFrontendArray()['dropoff'];
        $payload['route_id'] = $this->id;
        return $payload;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->pickup_name . ' → ' . $this->dropoff_name;
    }
}
