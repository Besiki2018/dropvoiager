<?php

namespace Modules\Car\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class TransferServiceCenter extends Model
{
    protected $table = 'car_transfer_service_centers';

    protected $fillable = [
        'location_id',
        'vendor_id',
        'name',
        'address',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(TransferLocation::class, 'location_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
