<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OperatorRole;
use App\Exports\RingExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubmitRingRequest;
use App\Http\Requests\Api\V1\UpdateRingRequest;
use App\Http\Resources\Api\V1\RingMemberResource;
use App\Http\Resources\Api\V1\RingResource;
use App\Models\Ring;
use App\Models\RingMember;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RingController extends Controller
{
    private function getRole()
    {
        return UserRole::query()
            ->where('user_id' , auth()->id())
            ->where('item_id' , \request()->query('item_id'))
            ->where('role' , OperatorRole::MOSQUE_HEAD_COACH)
            ->whereHas('unit')
            ->first();

    }
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $validRole = $this->getRole();
        if (! $validRole) {
            return \response()->json([
                'message' => 'عدم دسترسی'
            ] , 403);
        }

        $items = Ring::query()
            ->withCount(['members'])
            ->latest()
            ->where('owner_id' , auth()->id())
            ->when($request->filled('type') , function (Builder $builder) use ($request) {
                $builder->whereHas('item' , function (Builder $builder) use ($request) {
                    $builder->where('type' , $request->get('type'));
                });
            })->paginate($request->get('per_page' , 10));

        return RingResource::collection($items);
    }

    public function show($ring): JsonResponse|RingResource
    {
        $validRole = $this->getRole();
        if (! $validRole) {
            return \response()->json([
                'message' => 'عدم دسترسی'
            ] , 403);
        }

        $item = Ring::query()
            ->withCount(['members'])
            ->with(['members','owner','image','members.image'])
            ->findOrFail($ring);

        return RingResource::make($item);
    }

    public function store(SubmitRingRequest $request): JsonResponse|RingResource
    {
        $data = $request->except(['image','birthdate']);
        $now = now();
        $disk = config('site.default_disk');
        $validRole = $this->getRole();
        if (! $validRole) {
            return \response()->json([
                'message' => 'عدم دسترسی'
            ] , 403);
        }

        try {
            DB::beginTransaction();
            $data['birthdate'] = dateConverter($request->birthdate ,'m');
            $r = new Ring;
            $r->owner()->associate(Auth::user());
            $r->item()->associate($request->query('item_id'));
            $r->role()->associate($validRole);
            if ($user = User::query()->where('national_id',$data['national_code'])->first()) {
                $r->user()->associate($user);
            }
            $r->fill($data)->save();
            if ($request->hasFile('image')) {
                $path =  '/rings/'.$now->year.'/'.$now->month.'/'.$now->day;
                $image = $request->file('image');
                $r->image()->create([
                    'path' => $image->store($path,$disk),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'disk' => $disk,
                    'subject' => $r::FILE_IMAGE_SUBJECT
                ]);
            }

            $memImage = $request->file('members');
            foreach ($request->input('members') as $k => $member) {
                $m = new RingMember;
                $m->fill([
                    'name' => $member['name'],
                    'national_code' => $member['national_code'],
                    'birthdate' => dateConverter($member['birthdate'] ,'m'),
                    'postal_code' => $member['postal_code'],
                    'address' => $member['address'],
                    'phone' => $member['phone'],
                    'father_name' => $member['father_name'],
                ]);
                $m->ring()->associate($r);
                $m->save();
                if (! empty($memImage[$k]['image']) && $memImage[$k]['image'] instanceof UploadedFile) {
                    $path =  '/rings/'.$now->year.'/'.$now->month.'/'.$now->day;
                    $image = $memImage[$k]['image'];
                    $m->image()->create([
                        'path' => $image->store($path,$disk),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'disk' => $disk,
                        'subject' => $m::FILE_IMAGE_SUBJECT
                    ]);
                }
            }
            DB::commit();
            $r->load(['members','image','members.image']);
            return RingResource::make($r);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }

        return response()->json([
            'error' => 'مشکلی در حین ارسال درخواست به وجود آمده است ، لطفا مجدد تلاش کنید',
//            'error' => $exception->getMessage(),
        ] , 500);
    }

    public function update(UpdateRingRequest $request): JsonResponse|RingResource
    {
        $validRole = $this->getRole();
        if (! $validRole) {
            return \response()->json([
                'message' => 'عدم دسترسی'
            ] , 403);
        }

        $data = $request->except(['image','birthdate']);
        $now = now();
        $disk = config('site.default_disk');
        $r = $request->ring;
        $r->load(['image']);

        try {
            DB::beginTransaction();
            if ($request->hasFile('image')) {
                $path =  '/rings/'.$now->year.'/'.$now->month.'/'.$now->day;
                $image = $request->file('image');
                $r->image()->create([
                    'path' => $image->store($path,$disk),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'disk' => $disk,
                    'subject' => $r::FILE_IMAGE_SUBJECT
                ]);
                if ($r->image) {
                    $r->image->delete();
                }
            }
            if ($request->has('birthdate')) {
                $data['birthdate'] = dateConverter($request->birthdate ,'m');
            }
            $r->fill($data)->save();
            $memImage = $request->file('members');
            if ($request->filled('members')) {
                foreach ($request->input('members') ?? []  as $k => $member) {
                    $m = ! empty($member['id']) ? RingMember::query()->with(['image'])->findOr($member['id'] , function () {
                        return new RingMember;
                    }) : new RingMember;
                    if ($m) {
                        if (! empty($member['birthdate'])) {
                            $member['birthdate'] =  dateConverter($member['birthdate'] ,'m');
                        }
                        $m->fill($member);
                        $m->ring()->associate($r);
                        $m->save();
                        if (! empty($memImage[$k]['image']) && $memImage[$k]['image'] instanceof UploadedFile) {
                            $path =  '/rings/'.$now->year.'/'.$now->month.'/'.$now->day;
                            $m->image()->create([
                                'path' => $memImage[$k]['image']->store($path,$disk),
                                'mime_type' => $memImage[$k]['image']->getMimeType(),
                                'size' => $memImage[$k]['image']->getSize(),
                                'disk' => $disk,
                                'subject' => $m::FILE_IMAGE_SUBJECT
                            ]);
                            if ($m->image) {
                                $m->image->delete();
                            }
                        }
                    }
                }
            }
            DB::commit();
            $r->load(['members','image','members.image']);
            return RingResource::make($r);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }

        return response()->json([
            'error' => 'مشکلی در حین ارسال درخواست به وجود آمده است ، لطفا مجدد تلاش کنید',
//            'error' => $exception->getMessage(),
        ] , 500);

    }


    public function destroy($ring): JsonResponse|RingResource
    {
        $validRole = $this->getRole();
        if (! $validRole) {
            return \response()->json([
                'message' => 'عدم دسترسی'
            ] , 403);
        }

        $r = Ring::query()->with(['members'])->where('owner_id' , \auth()->id())->find($ring);

        try {
            foreach ($r->members ?? [] as $member) {
                if ($member->image) {
                    $member->image->delete();
                }
                $member->delete();
            }
            if ($r->image) {
                $r->image->delete();
            }
            $r->delete();
            return RingResource::make($r);
        } catch (\Exception $exception) {
            return response()->json([
//                'error' => 'مشکلی در حین ارسال درخواست به وجود آمده است ، لطفا مجدد تلاش کنید',
            'error' => $exception->getMessage(),
            ] , 500);
        }
    }


    public function destroyMember($ring , $member): JsonResponse|RingMemberResource
    {
        $validRole = $this->getRole();
        if (! $validRole) {
            return \response()->json([
                'message' => 'عدم دسترسی'
            ] , 403);
        }

        $r = RingMember::query()
            ->with(['image'])
            ->whereHas('ring' , function ($q) use ($ring) {
                $q->where('id' , $ring)->where('owner_id' , \auth()->id());
            })->findOrFail($member);

        if ($r->image) {
            $r->image->delete();
        }
        $r->delete();
        return RingMemberResource::make($r);
    }


    public function export(): Response|BinaryFileResponse|JsonResponse
    {
        $validRole = $this->getRole();
        if (! $validRole) {
            return \response()->json([
                'message' => 'عدم دسترسی'
            ] , 403);
        }

        return (new RingExport(
            owner: \auth()->id(),
            type: \request()->query('type')
        ))->download('rings.xlsx');
    }
}
