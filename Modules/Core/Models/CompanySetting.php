<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_logo',
        'company_favicon',
        'currency',
        'timezone',
        'date_format',
        'time_format',
        'fiscal_year_start',
        'language',
        'theme',
        'tax_number',
        'registration_number',
    ];

    protected $casts = [
        'fiscal_year_start' => 'date',
    ];

    public static function get($key, $default = null)
    {
        $setting = static::first();
        return $setting ? ($setting->$key ?? $default) : $default;
    }

    public static function set($key, $value)
    {
        $setting = static::firstOrCreate([]);
        $setting->$key = $value;
        $setting->save();
        return $setting;
    }
}
