<?php
namespace Modules\Car\Models;

use App\BaseModel;
use Carbon\Carbon;

class CarDate extends BaseModel
{
    protected $table = 'bravo_car_dates';

    protected $casts = [
        'person_types' => 'array',
        'price' => 'float',
        'sale_price' => 'float',
    ];

    protected $appends = [
        'available_start',
        'available_end',
    ];

    public static function getDatesInRanges($start_date,$end_date,$id){
        return static::query()->where([
            ['start_date','>=',$start_date],
            ['end_date','<=',$end_date],
            ['target_id','=',$id],
        ])->take(100)->get();
    }

    public function getAvailableStartAttribute(): ?string
    {
        return $this->resolveTimeAttribute($this->start_date);
    }

    public function getAvailableEndAttribute(): ?string
    {
        return $this->resolveTimeAttribute($this->end_date);
    }

    protected function resolveTimeAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $storageTz = config('app.timezone', 'UTC');
        $displayTz = 'Asia/Tbilisi';

        try {
            return Carbon::parse($value, $storageTz)
                ->setTimezone($displayTz)
                ->format('H:i');
        } catch (\Exception $exception) {
            return null;
        }
    }
}
