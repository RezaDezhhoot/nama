<?php

namespace App\Models;

use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;

class DashboardItem extends Model
{
    use SimpleSearchable;

    public array $searchAbleColumns = ['title','link','body'];
    protected $guarded = ['id'];
}
