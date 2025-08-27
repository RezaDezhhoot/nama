<?php

namespace App\Livewire\Banners;

use App\Livewire\BaseComponent;
use App\Models\Banner;
use App\Models\DashboardItem;

class StoreBanner extends BaseComponent
{
    public $title , $image , $item , $mobile_image;

    public function mount($action , $id = null)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->authorize('edit_banners');
            $this->model = Banner::query()->with(['item'])->findOrFail($id);
            $this->title = $this->model->title;
            $this->image = $this->model->image;
            $this->mobile_image = $this->model->mobile_image;
            $this->item = $this->model->item_id;
            $this->header = $this->title;
        } elseif ($this->isCreatingMode()) {
            $this->authorize('create_banners');
            $this->header = 'بنر جدید';
        } else abort(404);

        $this->data['items'] = DashboardItem::query()->pluck('title','id');
    }

    public function store()
    {
        $this->validate([
            'title' => ['required','string','max:150'],
            'image' => ['required','string','max:150000'],
            'mobile_image' => ['nullable','string','max:150000'],
            'item' => ['required']
        ]);
        $model = $this->model ?: new Banner;
        $data = [
            'title' => $this->title,
            'image' => $this->image,
            'item_id' => $this->item,
            'mobile_image' => $this->mobile_image,
        ];
        $model->fill($data)->save();

        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.banners.index');
    }

    public function deleteItem()
    {
        $this->authorize('delete_banners');
        if ($this->isUpdatingMode()) {
            redirect()->route('admin.banners.index');
        }
    }

    public function render()
    {
        return view('livewire.banners.store-banner')->extends('livewire.layouts.admin');
    }
}
