<?php

namespace App\Models;

use App\Enums\OperatorRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $guarded = ['id'];
    protected $table = 'user_roles';
    protected $connection = 'mysql';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => OperatorRole::class
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
