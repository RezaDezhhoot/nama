<?php

namespace App\Models;

use App\Enums\FormItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormItem extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    protected $casts = [
        "required" => "boolean",
        "options" => "array",
        "conditions" => "array",
        "type" => FormItemType::class
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
