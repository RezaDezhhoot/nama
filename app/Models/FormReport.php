<?php

namespace App\Models;

use App\Enums\FormReportEnum;
use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormReport extends Model
{
    use SoftDeletes  , Loggable;

    public function scopeSearch(Builder $query, $search): Builder
    {
        if ($search) {
            $query->whereHas('user' , function (Builder $builder) use ($search) {
                $builder->search($search);
            })->orWhereHas('form' , function (Builder $builder) use ($search) {
                $builder->search($search);
            });
        }
        return $query;
    }

    protected $guarded = ['id'];
    protected $casts = [
        "reports" => "array",
        'status' => FormReportEnum::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class,'form_id')->withTrashed();
    }
}
