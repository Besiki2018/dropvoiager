<?php

namespace Modules\Car\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\User\Models\User;

class TransferLocation extends Model
{
    protected $table = 'car_transfer_locations';

    protected $fillable = [
        'vendor_id',
        'name',
        'address',
        'lat',
        'lng',
        'map_zoom',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'map_zoom' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'display_name',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function serviceCenters(): HasMany
    {
        return $this->hasMany(TransferServiceCenter::class, 'location_id');
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'map_zoom' => $this->map_zoom,
            'vendor_id' => $this->vendor_id,
            'is_active' => $this->is_active,
            'display_name' => $this->display_name,
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
