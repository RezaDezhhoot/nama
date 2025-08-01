<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\AccountingBatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AccountingBatchFeedController extends Controller
{
    public function __invoke(Request $request , $type , $subType = null)
    {
        $items = AccountingBatch::query()
            ->orderByDesc('batch')
            ->where('unit_type' , $type)
            ->when($subType , function (Builder $builder) use ($subType) {
                $builder->where('unit_sub_type' , $subType);
            })
            ->search($request->get('search'))
            ->take(30)
            ->get();
        return response()->json($items->map(function ($v) {
            return [
                'text' => sprintf("%s %d - %s","دسته",$v->batch , persian_date($v->created_at,"%A, %d %B %Y - H:i")),
                'id' => $v->id,
            ];
        }));
    }
}
