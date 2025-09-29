<?php

namespace App\Livewire\Forms;

use App\Enums\FormItemType;
use App\Enums\FormStatus;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\Form;
use App\Models\FormItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mavinoo\Batch\Batch;

class StoreForm extends BaseComponent
{
    public $qsearch , $qitem , $qstatus;

    public $form;
    public $title , $required = false, $sort = 0 , $body , $item , $status;

    public $forms = [];

    public $q;
    public $iTitle , $iRequired = false , $iType , $iPlaceHolder , $iHelp , $iMax , $iMin , $iMimeTypes , $iOptions = [] , $iConditions = [] , $iSort = 0;

    public function queryString()
    {
        return [
            'qsearch' => [
                'as' => 'search'
            ],
            'qitem' => [
                'as' => 'item'
            ],
            'qstatus' => [
                'as' => 'status'
            ]
        ];
    }

    public function mount($action , $id = null)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->authorize('edit_forms');
            $this->form = Form::query()->with(['item','items'])->findOrFail($id);
            $this->title = $this->form->title;
            $this->required = $this->form->required;
            $this->sort = $this->form->sort;
            $this->body = $this->form->body;
            $this->item = $this->form->item_id;
            $this->header = $this->title ?? $this->form->id;
            $this->status = $this->form->status?->value;
        } elseif ($this->isCreatingMode()) {
            $this->authorize('create_forms');
            $this->form = Form::query()->create();
            $this->header = "فرم جدید";
        }
        $this->data['items'] = DashboardItem::query()->get()->pluck('title','id');
        $this->data['status'] = FormStatus::labels();
        $this->data['types'] = FormItemType::labels();
            $this->data['actions'] = [
            'hidden' => 'مخفی شود',
            'visible' => 'ظاهر شود',
        ];
    }

    public function store()
    {
        $this->validate([
            'title' => ['required','string','max:100'],
            'required' => ['nullable','boolean'],
            'sort' => ['required','integer'],
            'item' => ['required',Rule::exists('dashboard_items' ,'id')],
            'status' => ['required',Rule::enum(FormStatus::class)],
            'body' => ['nullable','string','max:1500']
        ]);
        $data = [
            'title' => $this->title,
            'required' => (bool)$this->required,
            'sort' => $this->sort,
            'item_id' => $this->item,
            'status' => $this->status,
            'body' => $this->body,
        ];
        $this->form->fill($data)->save();
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        return redirect()->route('admin.forms.index', [
            'search' => $this->qsearch,
            'item' => $this->qitem,
            'status' => $this->qstatus
        ]);
    }

    public function resetItem(): void
    {
        $this->reset(['iTitle','iRequired','iType','iPlaceHolder','iHelp','iMax','iMin','iMimeTypes','iOptions','iConditions','iSort','q','forms']);
    }

    public function openItem($id = null): void
    {
        $this->resetItem();
        if ($id) {
            $this->q = FormItem::query()->findOrFail($id);
            $this->iTitle = $this->q->title;
            $this->iRequired = $this->q->required;
            $this->iType = $this->q->type?->value;
            $this->iPlaceHolder = $this->q->placeholder;
            $this->iHelp = $this->q->help;
            $this->iMin = $this->q->min;
            $this->iMax = $this->q->max;
            $this->iMimeTypes = $this->q->mime_types;
            $this->iConditions = $this->q->conditions ?? [];
            $this->iOptions = $this->q->options ?? [];
        }
        $this->forms = $this->form->items()->when($id , function ($q) use ($id){
            $q->where('id' ,'!=' , $id);
        })->whereIn('type',[FormItemType::RADIO->value,FormItemType::SELECT->value,FormItemType::SELECT2->value])->get()->pluck('title','id')->toArray();
        $this->emitShowModal('item');
    }

    public function storeItem(): void
    {
        $this->validate([
            'iTitle' => ['required','string','max:100'],
            'iRequired' => ['boolean','nullable'],
            'iType' => ['required',Rule::enum(FormItemType::class)],
            'iPlaceHolder' => ['nullable','string','max:150'],
            'iHelp' => ['nullable','string','max:150'],
            'iMax' => ['nullable','numeric'],
            'iMin' => ['nullable','numeric'],
            'iMimeTypes' => ['nullable','string','max:200'],
            'iConditions' => ['array','nullable'],
            'iConditions.*.form' => ['required','integer',Rule::exists('form_items','id')->where('form_id' , $this->form->id)],
            'iConditions.*.target' => ['required','string','max:200'],
            'iConditions.*.action' => ['required','string','in:visible,hidden'],
            'iOptions' => ['array', in_array($this->iType,[FormItemType::CHECKBOX->value,FormItemType::RADIO->value,FormItemType::SELECT->value,FormItemType::SELECT2->value]) ? "required" : "nullable"],
            'iOptions.*' => ['required']
        ]);
        $data = [
            'title' => $this->iTitle,
            'required' => $this->iRequired,
            'type' => $this->iType,
            'placeholder' => $this->iPlaceHolder,
            'help' => $this->iHelp,
            'max' => $this->iMax,
            'min' => $this->iMin,
            'mime_types' => $this->iMimeTypes,
            'conditions' => $this->iConditions,
            'options' => $this->iOptions,
            'form_id' => $this->form->id
        ];
        $fi = $this->q ?: new FormItem;
        $fi->fill($data)->save();
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        $this->emitHideModal('item');
    }

    public function updateFormSort($data) {
        $updates = [];
        foreach ($data as $k => $v) {
            $updates[] = [
                'id' => $k,
                'sort' => $v
            ];
        }
        batch()->update(new FormItem, $updates, 'id');
    }

    public function deleteQ($id)
    {
        FormItem::destroy($id);
    }

    public function addOption()
    {
        $this->iOptions[] = null;
    }

    public function addCondition()
    {
        $this->iConditions[] = [
            'form' => null,
            'action' => null,
            'target' => null,
        ];
    }

    public function deleteOption($k)
    {
        unset($this->iOptions[$k]);
    }

    public function deleteCondition($k)
    {
        unset($this->iConditions[$k]);
    }

    public function deleteItem()
    {
        $this->authorize('delete_forms');
        $this->form->delete();
        return redirect()->route('admin.forms.index', [
            'search' => $this->qsearch,
            'item' => $this->qitem,
            'status' => $this->qstatus
        ]);
    }

    public function render()
    {
        $items = $this->form->items()->get();
        return view('livewire.forms.store-form' , get_defined_vars())->extends('livewire.layouts.admin');
    }
}
