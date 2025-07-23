<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class AdminList extends Component
{
    use WithPagination;

    public $search = '';
    protected $queryString = ['search'];
    protected $listeners = [
        'adminDeleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteAdmin($adminId)
    {
        try {
            $admin = User::findOrFail($adminId);
            if ($admin->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($admin->image);
            }
            $admin->delete();

            session()->flash('message', 'Admin deleted successfully !');
        } catch (\Exception $e) {
            session()->flash('error', 'Error : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $admins = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('surname', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
            })->where('role', 'admins')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin-list', [
            'admins' => $admins,
        ]);
    }
}