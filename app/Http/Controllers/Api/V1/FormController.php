<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\FormItemType;
use App\Enums\FormReportEnum;
use App\Enums\FormStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SkipFormRequest;
use App\Http\Requests\Api\V1\SubmitFormRequest;
use App\Http\Resources\Api\V1\FormReportResource;
use App\Http\Resources\Api\V1\FormResource;
use App\Models\Form;
use App\Models\FormReport;
use App\Rules\JDateRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FormController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = Form::query()
            ->orderBy('sort')
            ->with(['item','report'])
            ->where('status' , FormStatus::PUBLISHED)
            ->withCount('items')
            ->where(function (Builder $builder) {
                $builder->whereDoesntHave('reports' , function (Builder $builder) {
                    $builder->where('user_id' , auth()->id())->whereIn('status' , [FormReportEnum::DONE->value,FormReportEnum::PENDING->value]);
                })->orWhereHas('reports' , function (Builder $builder) {
                    $builder->where('user_id',auth()->id())->where('status' , FormReportEnum::ACTION_NEEDED->value);
                });
            })
            ->when($request->filled('q') , function ($q) use ($request) {
                $q->search($request->get('q'));
            })->where('item_id',\request()->get('item_id'))->paginate($request->get('per_page' , 10));

        return FormResource::collection($items)->additional([
            'types' => FormItemType::labels()
        ]);
    }

    public function show($form): FormResource
    {
        $item = Form::query()
            ->orderBy('sort')
            ->with(['item','items','report'])
            ->where('status' , FormStatus::PUBLISHED)
            ->withCount('items')
            ->findOrFail($form);

        return FormResource::make($item);
    }

    public function skip(SkipFormRequest $request): AnonymousResourceCollection
    {
        $user = auth()->user();
        $ids = $request->input('forms');
        $user->formSkips()->attach($ids);
        $forms = Form::query()->findMany($ids);
        return FormResource::collection($forms);
    }

    /**
     * @throws ValidationException
     */
    public function submit(SubmitFormRequest $request , $form): FormReportResource|\Illuminate\Http\JsonResponse
    {
        $f = $request->form;
        $f->load('report');
        $hasSent = $f->report && in_array($f->report->status , [FormReportEnum::DONE,FormReportEnum::PENDING]);
        if ($hasSent) {
            return response()->json([
                "message" => "فرم از قبل ارسال شده است"
            ] , 400);
        }

        $items = $request->input('items');
        $v = Validator::make([],[]);
        $reports = [];

        foreach ($f->items as $item) {
            $value = $items[$item->id] ?? null;
            $run = false;
            if (sizeof($item->conditions) === 0) {
                $run = true;
            } else {
                foreach ($item->conditions as $c) {
                    if (
                        (empty($items[$c['form']]) || $items[$c['form']] != $c['target']) && $c['action'] === "visible"
                    ) {
                        $run = false;
                    } elseif (
                       ! empty($items[$c['form']]) && $items[$c['form']] == $c['target'] && $c['action'] === "hidden"
                    ) {
                        $run = false;
                    } else {
                        $run = true;
                    }
                }
            }
            if ($run) {
                switch ($item->type) {
                    case FormItemType::TEXTAREA:
                    case FormItemType::TEXT:
                    case FormItemType::SELECT:
                    case FormItemType::SELECT2:
                    case FormItemType::RADIO:
                    case FormItemType::DATE:
                        if ($item->required && (!$value || is_array($value))) {
                            $v->errors()->add($item->id,'اجباری می باشد');
                            throw new ValidationException($v);
                        }
                        if ($value) {
                            if (! empty($item->max) && strlen($value) > $item->max) {
                                $v->errors()->add($item->id,'حداکثر طول فیلد رعایت نشده است');
                                throw new ValidationException($v);
                            }
                            if (! empty($item->min) && strlen($value) < $item->min) {
                                $v->errors()->add($item->id,'حداقل طول فیلد رعایت نشده است');
                                throw new ValidationException($v);
                            }
                        }
                        break;
                    case FormItemType::CHECKBOX:
                        if ($item->required && (!$value || sizeof($value) == 0)) {
                            $v->errors()->add($item->id,'اجباری می باشد');
                            throw new ValidationException($v);
                        }
                        break;
                    case FormItemType::LOCATION:
                        if ($item->required && (!$value || sizeof($value) != 2)) {
                            $v->errors()->add($item->id,'اجباری می باشد');
                            throw new ValidationException($v);
                        }
                        if ($value) {
                            if (empty($value['lat']) || empty($value['lng'])) {
                                $v->errors()->add($item->id,'مقدار نامعتبر می باشد');
                                throw new ValidationException($v);
                            }
                        }
                        break;
                    case FormItemType::NUMBER:
                        if ($item->required && !$value) {
                            $v->errors()->add($item->id,'اجباری می باشد');
                            throw new ValidationException($v);
                        }
                        if ($value) {
                            if (! empty($item->max) && $value > $item->max) {
                                $v->errors()->add($item->id,'حداکثر طول فیلد رعایت نشده است');
                                throw new ValidationException($v);
                            }
                            if (! empty($item->min) && strlen($value) < $item->min) {
                                $v->errors()->add($item->id,'حداقل طول فیلد رعایت نشده است');
                                throw new ValidationException($v);
                            }
                        }
                        break;
                    case FormItemType::FILE:
                        $value = $request->file('items')[$item->id];
                        if ($item->required && (! $value instanceof UploadedFile)) {
                            $v->errors()->add($item->id,'اجباری می باشد');
                            throw new ValidationException($v);
                        }
                        if ($value instanceof UploadedFile) {
                            if (! empty($item->max) && ($item->max * 1000 * 1000) < $value->getSize()) {
                                $v->errors()->add($item->id,'حداکثر سایز فیلد رعایت نشده است');
                                throw new ValidationException($v);
                            }
                            if (! empty($item->min) && ($item->max * 1000 * 1000) < $value->getSize()) {
                                $v->errors()->add($item->id,'حداقل طول فیلد رعایت نشده است');
                                throw new ValidationException($v);
                            }
                            if (! empty($item->mime_types) && ! in_array(pathinfo(basename($value->getClientOriginalName()),PATHINFO_EXTENSION) , explode(',' ,$item->mime_types))) {
                                $v->errors()->add($item->id,'فرمت های مجاز برای فیلد رعایت نشده است');
                                throw new ValidationException($v);
                            }
                            $value = sprintf("storage/%s",$value->store("forms","public"));
                        }
                        break;
                }
                $reports[$item->id] = [
                    'value' => $value,
                    'form' => $item
                ];
            }
        }
        $r = $f->report ?: new FormReport;
        $r->user()->associate(auth()->user());
        $r->form()->associate($f);
        $r->fill([
            'reports' => $reports,
            'status' => FormReportEnum::PENDING
        ])->save();
        $r->load(['form','form.items']);
        return FormReportResource::make($r);
    }
}
