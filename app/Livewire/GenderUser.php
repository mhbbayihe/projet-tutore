<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class GenderUser extends Component
{
    public $activeUsersCount;
    public $blockedUsersCount;

    public function mount()
    {
        $this->activeUsersCount = User::where('role', 'users')->where('status', 1)->count();
        $this->blockedUsersCount = User::where('role', 'users')->where('status', 0)->count();
    }
    public function render()
    {
        return view('livewire.gender-user', [
            'activeUsersCount' => $this->activeUsersCount,
            'blockedUsersCount' => $this->blockedUsersCount,
        ]);
    }
}
