<?php

namespace App\Livewire;


use App\Modules\Checkout\Enums\CheckoutStatus;
use App\Modules\Checkout\Interfaces\CheckoutRepositoryInterface;
use App\Modules\Comment\Enums\CommentStatus;
use App\Modules\Comment\Interfaces\CommentRepositoryInterface;
use App\Modules\Settings\Interfaces\SettingsRepositoryInterface;
use App\Modules\Ticket\Enums\TicketStatus;
use App\Modules\Ticket\Interfaces\TicketRepositoryInterface;
use Livewire\Component;

class Sidebar extends Component
{
    public function render()
    {
        return view('livewire.sidebar');
    }
}
