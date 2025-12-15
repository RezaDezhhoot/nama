<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use Loggable;

    protected $guarded = ['id'];
}
