<?php

namespace App\Models;

use App\Enums\AccountingType;
use App\Enums\UnitSubType;
use App\Enums\UnitType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingRecord extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'accounting_records';
    protected $casts = [
        'records' => "array",
        'type' => AccountingType::class,
        'unit_type' => UnitType::class,
        'unit_sub_type' => UnitSubType::class
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class,'unit_id')->withTrashed();
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class,'region_id')->withTrashed();
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(AccountingBatch::class,'accounting_batch_id');
    }
}
