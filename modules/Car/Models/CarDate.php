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
        'available_hours' => 'array',
    ];

    protected $appends = [
        'available_start',
        'available_end',
    ];

    public function getAvailableHoursAttribute($value): array
    {
        if (is_array($value)) {
            $hours = $value;
        } elseif ($value) {
            $decoded = json_decode($value, true);
            $hours = json_last_error() === JSON_ERROR_NONE ? (array) $decoded : [];
        } else {
            $hours = [];
        }

        $valid = [];
        foreach ($hours as $hour) {
            if (!is_string($hour) && !is_numeric($hour)) {
                continue;
            }
            $hourString = trim((string) $hour);
            if ($hourString === '') {
                continue;
            }
            try {
                $formatted = Carbon::createFromFormat('H:i', $hourString, 'Asia/Tbilisi')->format('H:i');
                $valid[$formatted] = $formatted;
            } catch (\Exception $exception) {
                continue;
            }
        }

        if (empty($valid)) {
            return [];
        }

        ksort($valid);

        return array_values($valid);
    }

    public function setAvailableHoursAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['available_hours'] = null;
            return;
        }

        $hours = [];
        if (is_array($value)) {
            $hours = $value;
        } elseif (is_string($value)) {
            $hours = array_map('trim', explode(',', $value));
        }

        $valid = [];
        foreach ($hours as $hour) {
            if (!is_string($hour) && !is_numeric($hour)) {
                continue;
            }
            $hourString = trim((string) $hour);
            if ($hourString === '') {
                continue;
            }
            try {
                $formatted = Carbon::createFromFormat('H:i', $hourString, 'Asia/Tbilisi')->format('H:i');
                $valid[$formatted] = $formatted;
            } catch (\Exception $exception) {
                continue;
            }
        }

        if (empty($valid)) {
            $this->attributes['available_hours'] = null;
            return;
        }

        ksort($valid);

        $this->attributes['available_hours'] = json_encode(array_values($valid));
    }

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
