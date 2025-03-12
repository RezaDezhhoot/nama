<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DashboardItemResource;
use App\Models\DashboardItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardItemController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return DashboardItemResource::collection(
            DashboardItem::query()->where('item_id',\request()->get('item_id'))->paginate((int)\request()->query('per_page' , 10))
        );
    }

    public function show($dashboardItem)
    {
        return DashboardItemResource::make(
            DashboardItem::query()->findOrFail($dashboardItem)
        );
    }
}
