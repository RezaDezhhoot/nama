<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        return BannerResource::collection(
            Banner::query()->where('item_id' , \request()->get('item_id',2))->orderBy('position')->get()
        );
    }
}
