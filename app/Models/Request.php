<?php

namespace App\Models;

use App\Enums\OperatorRole;
use App\Enums\PlanTypes;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Request extends Model
{
    use SoftDeletes , SimpleSearchable , Loggable;

    public array $searchAbleColumns = ['requests.id','requests.amount','requests.total_amount','requests.final_amount','requests.offer_amount'];
    protected $guarded = ['id'];

    protected $with = ['item','user','unit','members','members.image'];

    const FILE_IMAM_LETTER_SUBJECT = 'request_imam_letter';
    const FILE_IMAGES_SUBJECT = 'request_images';
    const FILE_OTHER_IMAM_LETTER_SUBJECT = 'request_other_imam_letter';
    const FILE_AREA_INTERFACE_LETTER_SUBJECT = 'request_area_interface_letter';
    const FILE_OTHER_AREA_INTERFACE_LETTER_SUBJECT = 'request_other_area_interface_letter';

    protected $casts = [
        'confirm' => 'boolean',
        'single_step' => 'boolean',
        'golden' => 'boolean',
        'staff' => 'boolean',
        'status' => RequestStatus::class,
        'step' => RequestStep::class,
        'last_updated_by' => RequestStep::class,
        'messages' => 'array',
        'plan_data' => 'array',
        'date' => 'datetime',
        'staff_amount' => 'float',
        'plan_type' => PlanTypes::class,
        'designated_by_council' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function plan(): BelongsTo
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
        return $this->morphMany(Comment::class,'commentable')->latest('id');
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
                $builder->where(function (Builder $builder) use ($role) {
                    if (in_array($role,[OperatorRole::MOSQUE_CULTURAL_OFFICER,OperatorRole::AREA_INTERFACE]) || request()->filled('status')) {
                        $builder->whereIn('requests.step' ,$role->step())->orWhereIn('requests.step',$role->history());
                    }
                });
                if (! in_array($role,[ OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES, OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING])) {
                    $builder ->whereHas('unit' , function (Builder $builder) use ($role) {
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

                return $builder;
            }
            return $builder->where('user_id' , auth()->id());
        });
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    public function dateFormatted(): Attribute
    {
        return Attribute::get(function (){
            return Carbon::make($this->created_at)->format('Y-m-d');
        });
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class,'user_id','user_id')->whereColumn('user_roles.item_id','=','item_id');
    }

    public function coach(): HasOne
    {
        return $this->roles()->one()->where('role',OperatorRole::MOSQUE_HEAD_COACH);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }

    public function toNextStep($offer_amount = null , $final_amount = null): static
    {
        switch ($this->step) {
//                case RequestStep::APPROVAL_MOSQUE_HEAD_COACH:
//                    $request->step = RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER;
//                    break;
            case RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER:
                $this->step = RequestStep::APPROVAL_AREA_INTERFACE;
                if ($this->notify_period) {
                    $this->next_notify_at = now()->addHours((int)$this->notify_period);
                } else if ($this->unit && $this->unit->city_id && $this->unit->region_id) {
                    $area_interface = UserRole::query()
                        ->with('user')
                        ->where('item_id' , $this->item_id)
                        ->where('city_id' , $this->unit->city_id)
                        ->where('region_id' , $this->unit->region_id)
                        ->where('role' , OperatorRole::AREA_INTERFACE)
                        ->whereNotNull('notify_period')
                        ->first();
                    if ($area_interface && $area_interface->notify_period) {
                        $this->next_notify_at =  now()->addHours((int)$area_interface->notify_period);
                        $this->notify_period = $area_interface->notify_period;
                        if ($area_interface->user) {
                            $this->controller2()->associate($area_interface->user);
                        }
                    }
                }
                break;
            case RequestStep::APPROVAL_AREA_INTERFACE:
                $this->step = RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES;
                break;
            case RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES:
                $this->step = RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING;
                $this->offer_amount = $offer_amount;
                break;
            case RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING:
                $this->step = RequestStep::FINISH;
                $this->status = RequestStatus::DONE;
                $this->final_amount = $final_amount;
                $amount = null;
                if ($this->staff && $this->staff_amount !== null) {
                    $amount = $this->staff_amount / 2;
                }
                if ($this->single_step) {
                    $amount = 0;
                }

                $this->report()->create([
                    'step' => $this->single_step ? RequestStep::FINISH : RequestStep::APPROVAL_MOSQUE_HEAD_COACH,
                    'status' => $this->single_step ? RequestStatus::DONE : RequestStatus::PENDING,
                    'amount' => 0,
                    'offer_amount' => $amount,
                    'final_amount' => $amount,
                    'confirm' => true,
                    'item_id' => $this->item_id,
                    'auto_accept_period' => $this->auto_accept_period,
                    'notify_period' => $this->notify_period,
                    'controller_id' => $this->controller_id,
                    'controller2_id' => $this->controller2_id,
                    'students' => $this->students,
                    'date' => $this->date,
                    'body' => $this->body,
                ]);
                break;
        }
        return $this;
    }

    public function controller(): BelongsTo
    {
        return $this->belongsTo(User::class,'controller_id');
    }

    public function controller2(): BelongsTo
    {
        return $this->belongsTo(User::class,'controller2_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(RingMember::class,'request_ring_member','request_id','ring_member_id')->withTrashed();
    }
}
