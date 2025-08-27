<?php

namespace App\Livewire\DashboardItems;

use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\User;
use Livewire\WithPagination;

class StoreItem extends BaseComponent
{
    public $title , $body , $link , $image;

    public $logo , $color;
    public function mount($action , $id = null)
    {
        $this->authorize('edit_dashboard_items');
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->model = DashboardItem::query()->findOrFail($id);
            $this->title = $this->model->title;
            $this->body = $this->model->body;
            $this->link = $this->model->link;
            $this->image = $this->model->image;
            $this->color = $this->model->color;
            $this->logo = $this->model->logo;
            $this->header = $this->title;
        } elseif ($this->isCreatingMode()) {
            $this->header = 'ایتم جدید';
        } else abort(404);
    }

    public function store()
    {
        $this->validate([
            'title' => ['required','string','max:100'],
            'body' => ['required','string','max:10000'],
            'link' => ['nullable','url','max:200'],
            'image' => ['required','string','max:150000'],
            'logo' => ['required','string','max:150000'],
            'color' => ['nullable','string'],
        ]);
        $data = [
            'title' => $this->title,
            'body' => $this->body,
            'link' => $this->link,
            'image' => $this->image,
            'logo' => $this->logo,
            'color' => $this->color
        ];
        $model = $this->model ?: new DashboardItem;
        $model->fill($data)->save();
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.dashboard-items.index');
    }

    public function deleteItem()
    {
        $this->authorize('delete_dashboard_items');
        if ($this->isUpdatingMode()) {
//            $this->model->delete();
//            redirect()->route('admin.dashboard-items.index');
        }
    }

    public function render()
    {
        return view('livewire.dashboard-items.store-item')->extends('livewire.layouts.admin');
    }
}
