<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class LastUser extends Component
{
     use WithPagination;

    public $search = '';
    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleUserBlock(User $user)
    {
        if ($user->status === 1) {
            $user->status = 0;
            session()->flash('message', 'User unblocked successfully!');
        } else {
            $user->status = 1;
            session()->flash('message', 'User blocked successfully!');
        }
        $user->save();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('surname', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })->where('role', 'users')
            ->orderBy('name')
            ->paginate(6);

        return view('livewire.last-user', [ // This view should match the name of the Blade file you are using for the table.
            'users' => $users,
        ]);
    }
}
