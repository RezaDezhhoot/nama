<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\FileStatus;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubmitReportRequest;
use App\Http\Requests\Api\V1\UpdateReportRequest;
use App\Http\Requests\Api\V1\UpdateRequest;
use App\Http\Resources\Api\V1\ReportResource;
use App\Models\Report;
use App\Models\Request as RequestModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use function Livewire\trigger;

class ReportController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'sort' => ['nullable','in:created_at,confirm'],
            'direction' => ['nullable','in:desc,asc'],
            'status' => ['nullable',Rule::enum(RequestStatus::class)],
            'steps' => ['nullable',Rule::enum(RequestStep::class)],
            'q' => ['nullable','string','max:50']
        ]);

        return ReportResource::collection(
            Report::query()->with(['request','request.plan'])->whereHas('request' , function (Builder $builder) use ($request) {
                $builder->where('user_id' , auth()->id())->when($request->filled('q') , function (Builder $builder) use ($request) {
                    $builder->search($request->get('q'));
                });
            })->when($request->filled('sort') , function (Builder $builder) use ($request) {
                $builder->orderBy(emptyToNull($request->get('sort' , 'confirm')) ?? 'confirm', $request->get('direction' , 'asc'));
            })->when(! $request->filled('sort') , function (Builder $builder) {
                $builder->orderBy('confirm');
            })->paginate((int)$request->get('per_page' , 10))
        )->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values()
        ]);
    }

    public function show($report): ReportResource
    {
        return ReportResource::make(
            Report::query()->with(['request','images','video','request.areaInterfaceLetter','request.imamLetter','request.plan'])->whereHas('request' , function (Builder $builder) {
                $builder->where('user_id' , auth()->id());
            })->findOrFail($report)
        )->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values()
        ]);
    }

    public function create(SubmitReportRequest $submitReportRequest , $request): ReportResource|JsonResponse
    {
        $request = RequestModel::query()
            ->with(['areaInterfaceLetter','imamLetter','plan'])
            ->whereHas('plan')
            ->where('user_id' , auth()->id())
            ->whereDoesntHave('report')
            ->confirmed()
            ->where('status' , RequestStatus::DONE)
            ->where('step' , RequestStep::FINISH)
            ->findOrFail($request);
        $data = $submitReportRequest->only(['students','date','body']);
        try {
            DB::beginTransaction();
            $report = $request->report()->create([
                ... $data,
                'step' => RequestStep::APPROVAL_MOSQUE_HEAD_COACH,
                'status' => RequestStatus::IN_PROGRESS,
                'amount' => 0,
            ]);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'reports/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$report->id;

            if ($submitReportRequest->hasFile('video')) {
                $video = $submitReportRequest->file('video');
                $report->video()->create([
                    'path' => $video->store($path,$disk),
                    'mime_type' => $video->getMimeType(),
                    'size' => $video->getSize(),
                    'disk' => $disk,
                    'subject' => $report::FILE_VIDEOS_SUBJECT
                ]);
            }
            $images = [];
            foreach ($submitReportRequest->file('images') as $image) {
                $images[] = [
                    'path' => $image->store($path,$disk),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'disk' => $disk,
                    'subject' => $report::FILE_IMAGES_SUBJECT,
                    'fileable_type' => $report->getMorphClass(),
                    'fileable_id' => $report->id,
                    'status' => FileStatus::PROCESSED
                ];
            }
            $report->images()->insert($images);
            DB::commit();
            $report->load(['request','images','video','request.areaInterfaceLetter','request.imamLetter','request.plan']);
            return ReportResource::make($report);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال گزارش به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }

    public function confirm($report): ReportResource
    {
        $report = Report::query()
            ->with(['request','images','video','request.areaInterfaceLetter','request.imamLetter','request.plan'])
            ->whereHas('request' , function (Builder $builder)  {
                $builder->where('user_id' , auth()->id());
            })->where('confirm' , false)
            ->findOrFail($report);

        $report->update([
            'confirm' => true
        ]);
        return ReportResource::make($report);
    }

    public function update(UpdateReportRequest $updateReportRequest , $report): ReportResource|JsonResponse
    {
        $report =  Report::query()
            ->with(['request','images','video','request.areaInterfaceLetter','request.imamLetter','request.plan'])
            ->where('status' , RequestStatus::ACTION_NEEDED)
            ->confirmed()
            ->whereHas('request' , function (Builder $builder) {
                $builder->where('user_id' , auth()->id());
            })->findOrFail($report);
        $data = $updateReportRequest->only(['students','date','body']);
        $data['status'] = RequestStatus::IN_PROGRESS;
        try {
            DB::beginTransaction();
            $report->update($data);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'reports/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$report->id;

            if ($updateReportRequest->hasFile('video') || ($updateReportRequest->filled('remove_video') && $updateReportRequest->remove_video) ) {
                if ($report->video) {
                    $report->video->delete();
                }
                if ($updateReportRequest->hasFile('video')) {
                    $video = $updateReportRequest->file('video');
                    $report->video()->create([
                        'path' => $video->store($path,$disk),
                        'mime_type' => $video->getMimeType(),
                        'size' => $video->getSize(),
                        'disk' => $disk,
                        'subject' => $report::FILE_VIDEOS_SUBJECT
                    ]);
                }
            }
            $report->files()->whereIn('id' , $updateReportRequest->get('images_to_remove',[]))->delete();
            if ($updateReportRequest->hasFile('images')) {
                $images = [];
                foreach ($updateReportRequest->file('images') ?? [] as $image) {
                    $images[] = [
                        'path' => $image->store($path,$disk),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'disk' => $disk,
                        'subject' => $report::FILE_IMAGES_SUBJECT,
                        'fileable_type' => $report->getMorphClass(),
                        'fileable_id' => $report->id,
                        'status' => FileStatus::PROCESSED
                    ];
                }
                if (sizeof($images) > 0) {
                    $report->images()->insert($images);
                }
            }

            DB::commit();
            $report->refresh();
            $report->load(['images','video']);
            return ReportResource::make($report);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال گزارش به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }
}
