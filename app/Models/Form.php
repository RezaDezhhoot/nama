<?php

namespace App\Models;

use App\Enums\FormStatus;
use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use SoftDeletes , SimpleSearchable , Loggable;

    public array $searchAbleColumns = ['id','title'];

    protected $casts = [
        "required" => "boolean",
        "status" => FormStatus::class
    ];

    protected $guarded = ['id'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FormItem::class,'form_id')->orderBy('sort');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(FormReport::class,'form_id');
    }

    public function skips(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'forms_skipped','form_id','user_id');
    }

    public function report(): HasOne
    {
        return $this->hasOne(FormReport::class,'form_id')->where('user_id' , auth()->id());
    }
}
