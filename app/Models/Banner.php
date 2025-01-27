<?php

namespace App\Models;

use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use SimpleSearchable;

    public $searchAbleColumns = ['title'];
    protected $guarded = ['id'];
}
