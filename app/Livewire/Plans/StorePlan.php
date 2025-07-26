<?php

namespace App\Livewire\Plans;

use App\Enums\RequestPlanStatus;
use App\Enums\RequestPlanVersion;
use App\Imports\AreaImport;
use App\Imports\PlanLimitImport;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\RequestPlan;
use App\Models\RequestPlanLimit;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class StorePlan extends BaseComponent
{
    use WithPagination , WithFileUploads;

    public $title , $sub_title , $image , $status , $max_number_people_supported = 10 , $support_for_each_person_amount = 1000;
    public $starts_at , $expires_at , $max_allocated_request = 1 , $body , $bold = false , $single_step = false , $images_required = false;

    public $version;

    public $item;

    public $letter_required , $letter2_required;

    public $requirements = [];

    public $show_letter = true, $show_area_interface = true, $show_images = true;
    public $report_video_required = true, $report_other_video_required = true , $report_images_required = true, $report_images2_required = true;
    public $show_report_video = true, $show_report_other_video = true , $show_report_images = true, $show_report_images2 = true;

    public $golden = false , $staff = false , $staff_amount ;

    public $ring_member_required = false , $show_ring_member = false;

    public $limitValue , $limitFile;

    public function mount($action , $id = null)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->model = RequestPlan::query()->with(['requirements' => function ($q) {
                $q->select2();
            }])->findOrFail($id);
            $this->title = $this->model->title;
            $this->sub_title = $this->model->sub_title;
            $this->image = $this->model->image;
            $this->single_step = $this->model->single_step ?? false;
            $this->requirements = $this->model->requirements?->toArray();
            $this->status = $this->model->status?->value;
            $this->max_number_people_supported = $this->model->max_number_people_supported;
            $this->support_for_each_person_amount = $this->model->support_for_each_person_amount;
            $this->starts_at = dateConverter($this->model->starts_at,'j' , 'Y-m-d H:i:s');
            $this->expires_at = dateConverter($this->model->expires_at,'j' , 'Y-m-d H:i:s');
            $this->max_allocated_request = $this->model->max_allocated_request;
            $this->body = $this->model->body;
            $this->bold = $this->model->bold;

            $this->letter_required = $this->model->letter_required ;
            $this->letter2_required = $this->model->letter2_required;
            $this->images_required = $this->model->images_required ;

            $this->report_video_required = $this->model->report_video_required ;
            $this->report_other_video_required = $this->model->report_other_video_required ;
            $this->report_images2_required = $this->model->report_images2_required ;
            $this->report_images_required = $this->model->report_images_required ;

            $this->show_report_video = $this->model->show_report_video ;
            $this->show_report_other_video = $this->model->show_report_other_video ;
            $this->show_report_images2 = $this->model->show_report_images2 ;
            $this->show_report_images = $this->model->show_report_images ;

            $this->show_letter = $this->model->show_letter ;
            $this->show_area_interface = $this->model->show_area_interface;
            $this->show_images = $this->model->show_images;

            $this->version = $this->model->version->value ?? null;
            $this->header = $this->title;

            $this->golden = $this->model->golden;
            $this->staff = $this->model->staff;
            $this->staff_amount = $this->model->staff_amount;

            $this->ring_member_required = $this->model->ring_member_required;
            $this->show_ring_member = $this->model->show_ring_member;

            $this->item = $this->model->item_id;
        } elseif ($this->isCreatingMode()) {
            $this->header = 'اکشن پلن جدید';
            $this->status = RequestPlanStatus::PUBLISHED->value;
            $this->model = RequestPlan::query()->create([
                'title' => 'بدون عنوان',
                'status' => RequestPlanStatus::DRAFT->value,
                'image' => ""
            ]);
        } else abort(404);
        $this->data['status'] = RequestPlanStatus::labels();
        $this->data['version'] = RequestPlanVersion::values();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');

    }

    public function updatedItem($v)
    {
        $i = DashboardItem::query()->find($v);
        $planAjaxRequest = route('admin.feed.plans',[$i?->type?->value]);
        $this->dispatch('reloadAjaxURL#requirements' , $planAjaxRequest);
    }

    public function store()
    {
        $this->starts_at = emptyToNull($this->starts_at);
        $this->expires_at = emptyToNull($this->expires_at);
        $model = $this->model ?: new RequestPlan;

        $this->validate([
            'title' => ['required','string','max:150'],
            'sub_title' => ['nullable','string','max:250'],
            'image' => ['required','string','max:1000'],
            'status' => ['required',Rule::enum(RequestPlanStatus::class)],
            'max_number_people_supported' => ['required','integer','between:1,100000000'],
            'support_for_each_person_amount' => ['required','integer','between:1000,10000000000000'],
            'max_allocated_request' => ['required','integer','between:1,10000000000000'],
            'starts_at' => ['nullable'],
            'expires_at' => ['nullable'],
            'body' => ['nullable','string','max:1500000'],
            'bold' => ['nullable','boolean'],
            'single_step' => ['nullable','boolean'],
            'version' => ['required',Rule::enum(RequestPlanVersion::class)],
            'item' => ['required'],
            'requirements' => ['nullable','array'],

            'letter_required' => ['nullable','boolean'],
            'letter2_required' => ['nullable','boolean'],
            'images_required' => ['nullable','boolean'],

            'show_letter' => ['nullable','boolean'],
            'show_area_interface' => ['nullable','boolean'],
            'show_images' => ['nullable','boolean'],

            'report_video_required' => ['nullable','boolean'],
            'report_other_video_required' => ['nullable','boolean'],
            'report_images2_required' => ['nullable','boolean'],
            'report_images_required' => ['nullable','boolean'],

            'show_report_video' => ['nullable','boolean'],
            'show_report_other_video' => ['nullable','boolean'],
            'show_report_images2' => ['nullable','boolean'],
            'show_report_images' => ['nullable','boolean'],

            'golden' => ['nullable','boolean'],
            'staff' => ['nullable','boolean'],
            'staff_amount' => [ $this->staff ? 'required' : 'nullable' , 'numeric','min:0'],

            'ring_member_required' => ['nullable','boolean'],
            'show_ring_member' => ['nullable','boolean'],

//            'requirements.*' => ['required',Rule::exists('request_plans','id')],
        ]);
        $data = [
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'image' => $this->image,
            'status' => $this->status,
            'max_number_people_supported' => $this->max_number_people_supported,
            'support_for_each_person_amount' => $this->support_for_each_person_amount,
            'max_allocated_request' => $this->max_allocated_request,
            'starts_at' => dateConverter($this->starts_at ,'m','Y-m-d H:i:s'),
            'expires_at' => dateConverter($this->expires_at ,'m','Y-m-d H:i:s'),
            'body' => $this->body,
            'bold' => $this->bold,
            'item_id' => $this->item,
            'version' => $this->version,

            'letter_required' => emptyToNull($this->letter_required) ?? false,
            'letter2_required' => emptyToNull($this->letter2_required) ?? false,
            'images_required' => emptyToNull($this->images_required) ?? false,

            'show_letter' => emptyToNull($this->show_letter) ?? false,
            'show_area_interface' => emptyToNull($this->show_area_interface) ?? false,
            'show_images' => emptyToNull($this->show_images) ?? false,

            'report_video_required' => emptyToNull($this->report_video_required) ?? false,
            'report_other_video_required' => emptyToNull($this->report_other_video_required) ?? false,
            'report_images2_required' => emptyToNull($this->report_images2_required) ?? false,
            'report_images_required' => emptyToNull($this->report_images_required) ?? false,

            'show_report_video' => emptyToNull($this->show_report_video) ?? false,
            'show_report_other_video' => emptyToNull($this->show_report_other_video) ?? false,
            'show_report_images2' => emptyToNull($this->show_report_images2) ?? false,
            'show_report_images' => emptyToNull($this->show_report_images) ?? false,

            'single_step' => emptyToNull($this->single_step) ?? false,
            'golden' => emptyToNull($this->golden) ?? false,
            'staff' => emptyToNull($this->staff) ?? false,
            'staff_amount' => emptyToNull($this->staff_amount) ,

            'ring_member_required' => emptyToNull($this->ring_member_required) ?? false,
            'show_ring_member' => emptyToNull($this->show_ring_member) ?? false,
        ];
        $model->fill($data)->save();
        $model->requirements()->{$model->wasRecentlyCreated ? "attach" : "sync"}($this->requirements);
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.plans.index');
    }

    public function deleteItem()
    {
        if ($this->isUpdatingMode()) {
            $this->model->delete();
            redirect()->route('admin.plans.index');
        }
    }

    public function showLimits()
    {
        $this->resetPage();
        $this->emitShowModal("limits");
    }

    public function addLimit()
    {
        $this->validate([
            'limitValue' => ['nullable','string','max:11'],
            'limitFile' => ['nullable',Rule::file()->extensions('xlsx')]
        ]);
        if ($this->limitValue) {
            $data = [
                'value' => convert2english(trim($this->limitValue)),
                'request_plan_id' => $this->model->id
            ];
            RequestPlanLimit::query()->create($data);
            $this->reset('limitValue');
        }
        if ($this->limitFile instanceof UploadedFile) {
            Excel::import(new PlanLimitImport($this->model),$this->limitFile);
            $this->reset('limitFile');
        }
        $this->emitNotify("اطلاعات با موفقیت ذخیره شد");
    }

    public function deleteLimit($id)
    {
        RequestPlanLimit::destroy($id);
    }

    public function render()
    {
        $limits = $this->model->limits()->when($this->search , function ($q) {
            $q->search($this->search);
        })->latest()->paginate($this->per_page);

        return view('livewire.plans.store-plan' , get_defined_vars())->extends('livewire.layouts.admin');
    }
}
