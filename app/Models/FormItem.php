<?php

namespace App\Models;

use App\Enums\FormItemType;
use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormItem extends Model
{
    use SoftDeletes , Loggable , SimpleSearchable;

    public array $searchAbleColumns = ['title'];

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
