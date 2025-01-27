<?php

namespace App\Livewire\Banners;

use App\Livewire\BaseComponent;
use App\Models\Banner;

class StoreBanner extends BaseComponent
{
    public $title , $image;

    public function mount($action , $id = null)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->model = Banner::query()->findOrFail($id);
            $this->title = $this->model->title;
            $this->image = $this->model->image;
            $this->header = $this->title;
        } elseif ($this->isCreatingMode()) {
            $this->header = 'بنر جدید';
        } else abort(404);
    }

    public function store()
    {
        $this->validate([
            'title' => ['required','string','max:150'],
            'image' => ['required','string','max:150000']
        ]);
        $model = $this->model ?: new Banner;
        $data = [
            'title' => $this->title,
            'image' => $this->image
        ];
        $model->fill($data)->save();

        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.banners.index');
    }

    public function deleteItem()
    {
        if ($this->isUpdatingMode()) {
            redirect()->route('admin.banners.index');
        }
    }

    public function render()
    {
        return view('livewire.banners.store-banner')->extends('livewire.layouts.admin');
    }
}
