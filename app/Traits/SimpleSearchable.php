<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SimpleSearchable
{
    public function scopeSearch(Builder $query, $search): Builder
    {
        if ($search) {
            $query->whereAny($this->searchAbleColumns,'LIKE',"%$search%");
        }

        return $query;
    }
}
