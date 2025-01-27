<?php

namespace App\Livewire;

use App\Enums\PageAction;
use Livewire\Component;

abstract class BaseComponent extends Component
{
    public function __construct()
    {
    }

    public $model;


    public ?string $header = null;
    public bool $loading = true;
    public $mode = null , $search = '';
    public int $per_page = 10;
    public $searchable = true , $sortable = false;

    public array $data = [];

    public function isUpdatingMode(): bool
    {
        return $this->mode === PageAction::UPDATE;
    }

    public function isCreatingMode(): bool
    {
        return $this->mode === PageAction::CREATE;
    }

    protected function emitShowModal($id): void
    {
        $this->dispatch('showModal', $id);
    }

    protected function emitHideModal($id): void
    {
        $this->dispatch('hideModal', $id);
    }

    public function init(): void
    {
        $this->disableLoader();
    }

    public function disableLoader(): void
    {
        $this->loading = false;
    }

    public function enableLoader(): void
    {
        $this->loading = true;
    }

    protected function setMode($mode): void
    {
        $this->mode = PageAction::tryFrom($mode);
    }

    protected function emitNotify($title, $icon = 'success')
    {
        $data['title'] = $title;
        $data['icon'] = $icon;

        return $this->dispatch('notify', $data);
    }
}
