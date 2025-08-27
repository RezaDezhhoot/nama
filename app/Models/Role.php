<?php

namespace App\Models;

use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    use SimpleSearchable;

    public array $searchAbleColumns = ['name'];
    protected $connection = 'mysql';
    public function scopeSelect2($q)
    {
        return $q->selectRaw("name as text , id");
    }
}
