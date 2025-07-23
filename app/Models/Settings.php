<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use Loggable;

    protected $table = 'settings';

    protected $guarded = ['id'];

    public static function getSingleRow($name, $default = '')
    {
        return self::where('name', $name)->first()?->value ?? $default;
    }

    public function value(): Attribute
    {
        return  Attribute::make(
            get: function($value) {
                $data = json_decode($value, true);
                return is_array($data) ? $data : $value;
            }
        );
    }
}
