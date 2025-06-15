<?php

namespace App\Models;

use App\Enums\OperatorRole;
use App\Enums\UnitType;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ring extends Model
{
    use SimpleSearchable , SoftDeletes;

    public array $searchAbleColumns = ['title','name','national_code','postal_code','phone','id','address','description','level_of_education','field_of_study','job','sheba_number'];

    protected $guarded = ['id'];

    protected $casts = [
        'functional_area' => "array",
        'skill_area' => "array",
        "type" => UnitType::class
    ];

    const FILE_IMAGE_SUBJECT = "image";

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class,'owner_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(RingMember::class,'ring_id');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_IMAGE_SUBJECT);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }

    public function imagePublic(): Attribute
    {
        return Attribute::get(function () {
            return $this->image ? "storage/".$this->image : null;
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class,'user_role_id')->where('role',OperatorRole::MOSQUE_HEAD_COACH->value);
    }
}
