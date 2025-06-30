<?php

namespace App\Models;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Request extends Model
{
    use SoftDeletes , SimpleSearchable;

    public array $searchAbleColumns = ['id','body'];
    protected $guarded = ['id'];

    protected $with = ['item','user','unit'];

    const FILE_IMAM_LETTER_SUBJECT = 'request_imam_letter';
    const FILE_IMAGES_SUBJECT = 'request_images';
    const FILE_OTHER_IMAM_LETTER_SUBJECT = 'request_other_imam_letter';
    const FILE_AREA_INTERFACE_LETTER_SUBJECT = 'request_area_interface_letter';
    const FILE_OTHER_AREA_INTERFACE_LETTER_SUBJECT = 'request_other_area_interface_letter';

    protected $casts = [
        'confirm' => 'boolean',
        'single_step' => 'boolean',
        'status' => RequestStatus::class,
        'step' => RequestStep::class,
        'last_updated_by' => RequestStep::class,
        'messages' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(RequestPlan::class,'request_plan_id')->withTrashed();
    }

    public function imamLetter(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_IMAM_LETTER_SUBJECT);
    }

    public function areaInterfaceLetter(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_AREA_INTERFACE_LETTER_SUBJECT);
    }

    public function scopeRelations($q)
    {
        return $q
            ->with(['areaInterfaceLetter','imamLetter','plan','report','report.images','report.otherVideos','report.video','unit','otherImamLetter','otherAreaInterfaceLetter','images','report.images2']);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(File::class,'fileable')->subject(self::FILE_IMAGES_SUBJECT);
    }

    public function otherImamLetter(): MorphMany
    {
        return $this->morphMany(File::class,'fileable')->subject(self::FILE_OTHER_IMAM_LETTER_SUBJECT);
    }

    public function otherAreaInterfaceLetter(): MorphMany
    {
        return $this->morphMany(File::class,'fileable')->subject(self::FILE_OTHER_AREA_INTERFACE_LETTER_SUBJECT);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class,'commentable')->latest();
    }

    public function scopeConfirmed(Builder $builder): Builder
    {
        return $builder->where('confirm' , true);
    }

    public function scopeRoleFilter(Builder $builder): Builder
    {
        return $builder;
//        return $builder->whereIn('step' , auth()->user()->nama_role->step());
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class,'request_id');
    }

    public function scopeItem($q , $id)
    {
        return $q->where('requests.item_id' , $id);
    }

    public function scopeRole(Builder $builder , $role = null): Builder
    {
        return $builder->where(function (Builder $builder) use ($role) {
            $role = OperatorRole::tryFrom($role);
            if ($role && $role !== OperatorRole::MOSQUE_HEAD_COACH) {
                return $builder->where(function (Builder $builder) use ($role) {
                    if (in_array($role,[OperatorRole::MOSQUE_CULTURAL_OFFICER,OperatorRole::AREA_INTERFACE]) || request()->filled('status')) {
                        $builder->whereIn('step' ,$role->step())->orWhereIn('step',$role->history());
                    }
                })->whereHas('unit' , function (Builder $builder) use ($role) {
                    if ($role === OperatorRole::MOSQUE_CULTURAL_OFFICER) {
                        return $builder->whereHas('roles' , function (Builder $builder) use ($role) {
                            $builder->where('role' , $role)->where('user_id' , auth()->id());
                        })->orWhereHas('parent' , function (Builder $builder) use ($role) {
                            $builder->whereHas('roles' , function (Builder $builder) use ($role) {
                                $builder->where('role' , $role)->where('user_id' , auth()->id());
                            });
                        });
                    } elseif ($role === OperatorRole::AREA_INTERFACE) {
                        [$cities , $regions] = auth()->user()->getAreaInterfaceLocations(request()->get('item_id'));
                        $builder
                                    ->whereIn('city_id' , $cities)
                                    ->whereIn('region_id' , $regions)
                        ;
                    }
                    return $builder;
                 });
            }
            return $builder->where('user_id' , auth()->id());
        });
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function date(): Attribute
    {
        return Attribute::get(function (){
            return Carbon::make($this->created_at)->format('Y-m-d');
        });
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class,'user_id','user_id')->whereColumn('user_roles.item_id','=','requests.item_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }
}
