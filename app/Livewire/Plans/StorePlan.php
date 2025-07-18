<?php

namespace App\Livewire\Plans;

use App\Enums\RequestPlanStatus;
use App\Enums\RequestPlanVersion;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\RequestPlan;
use Illuminate\Validation\Rule;

class StorePlan extends BaseComponent
{
    public $title , $sub_title , $image , $status , $max_number_people_supported = 10 , $support_for_each_person_amount = 1000;
    public $starts_at , $expires_at , $max_allocated_request = 1 , $body , $bold = false , $single_step = false , $images_required = false;

    public $version;

    public $item;

    public $letter_required , $letter2_required;

    public $requirements = [];

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
            $this->letter_required = $this->model->letter_required ?? false;
            $this->letter2_required = $this->model->letter2_required ?? false;
            $this->images_required = $this->model->images_required ?? false;
            $this->version = $this->model->version->value ?? null;
            $this->header = $this->title;
            $this->item = $this->model->item_id;
        } elseif ($this->isCreatingMode()) {
            $this->header = 'اکشن پلن جدید';
            $this->status = RequestPlanStatus::PUBLISHED->value;
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
            'letter_required' => ['nullable','boolean'],
            'letter2_required' => ['nullable','boolean'],
            'images_required' => ['nullable','boolean'],
            'single_step' => ['nullable','boolean'],
            'version' => ['required',Rule::enum(RequestPlanVersion::class)],
            'item' => ['required'],
            'requirements' => ['nullable','array'],
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
            'single_step' => emptyToNull($this->single_step) ?? false,
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

    public function render()
    {
        return view('livewire.plans.store-plan')->extends('livewire.layouts.admin');
    }
}
