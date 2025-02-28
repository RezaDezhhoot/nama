<?php

namespace App\Models;

use App\Enums\OperatorRole;
use Illuminate\Database\Eloquent\Model;

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

    public function item()
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }
}
