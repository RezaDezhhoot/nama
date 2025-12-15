<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\UnitSubType;
use App\Enums\UnitType;
use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SimpleSearchable , SoftDeletes , Loggable;

    public array $searchAbleColumns = ['title','systematic_code','code'];

    protected $guarded = ['id'];

    protected $with = ['city','area','parent','region','neighborhood','area'];

    protected $appends = ['full'];

    protected $casts = [
        'auto_accept' => 'boolean',
        'type' => UnitType::class,
        'sub_type' => UnitSubType::class,
        'number_list' => "array",
        "lat" => "float",
        "lng" => "float",
        'gender' => Gender::class,
        'armani' => 'boolean'
    ];

//    public function title(): Attribute
//    {
//        return Attribute::get(function ($v) {
//           return sprintf("%s(%s) - %s",$v,$this->parent_id ? "معمولی" : 'محوری',$this->sub_type instanceof UnitSubType ? $this->sub_type?->label() : UnitSubType::tryFrom($this->sub_type)?->label());
//        });
//    }


    public function text(): Attribute
    {
        return Attribute::get(function ($v) {
            return sprintf("%s(%s) - %s",$v,$this->parent_id ? "معمولی" : 'محوری', $this->sub_type instanceof UnitSubType ? $this->sub_type?->label() : UnitSubType::tryFrom($this->sub_type)?->label());
        });
    }


    public function full(): Attribute
    {
        return Attribute::get(function ($v) {
            $full = sprintf("%s - %s" , $this->title , $this->parent_id ? "معمولی" : 'محوری');

            if ($this->sub_type) {
                $subType = $this->sub_type instanceof UnitSubType ? $this->sub_type?->label() : UnitSubType::tryFrom($this->sub_type)?->label();
                if ($subType) {
                    $full .= " - ".$subType;
                }
            }

            if ($this->region) {
                $full .= " - ".$this->region?->title;
            }

            if ($this->systematic_code) {
                $full .= " - ".$this->systematic_code;
            }

            if ($this->gender) {
                $gender = $this->gender instanceof Gender ? $this->gender?->label() : Gender::tryFrom($this->gender)?->label();
                if ($gender) {
                    $full .= " - ".$gender;
                }
            }

            return $full;
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class,'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class,'parent_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class)->withTrashed();
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class)->withTrashed();
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class)->withTrashed();
    }

    public function scopeSelect2($q)
    {
        return $q->selectRaw("title as text , id,sub_type,parent_id");
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class,'unit_id');
    }

    public function numberListSelect2(): Attribute
    {
        return Attribute::get(function () {
            $data = [];
            foreach ($this->number_list ?? [] as $l) {
                $data[] = [
                    'text' => $l,
                    'id' => $l
                ];
            }

            return $data;
        });
    }

    public function numbers(): Attribute
    {
        return Attribute::get(function () {
            return [
                [
                    'id' => $this->phone1 , 'text' => $this->phone1 ? $this->phone1_title.'-'.$this->phone1 : null,
                ],
                [
                    'id' => $this->phone2 , 'text' => $this->phone2 ?$this->phone2_title.'-'.$this->phone2 : null,
                ],
                [
                    'id' => $this->phone3 , 'text' => $this->phone3 ?$this->phone3_title.'-'.$this->phone3 : null,
                ],
                [
                    'id' => $this->phone4 , 'text' => $this->phone4 ?$this->phone4_title.'-'.$this->phone4 : null,
                ],
                [
                    'id' => $this->phone5 , 'text' => $this->phone5 ? $this->phone5_title.'-'.$this->phone5 : null,
                ],
                [
                    'id' => $this->phone6 , 'text' => $this->phone6 ? $this->phone6_title.'-'.$this->phone6 : null,
                ],
                [
                    'id' => $this->phone7 , 'text' => $this->phone7 ? $this->phone7_title.'-'.$this->phone7 : null,
                ],
                [
                    'id' => $this->phone8 , 'text' => $this->phone8 ? $this->phone8_title.'-'.$this->phone8 : null,
                ],
            ];
        });
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class,'state_id');
    }

    public static function generateCode($min = 1_000_000 , $ignore = []): int
    {
        do {
            $final = mt_rand($min, $min * 10 - 1);
        } while(Unit::query()->where('systematic_code',$final)->exists() || in_array($final , $ignore));

        return $final;
    }
}
