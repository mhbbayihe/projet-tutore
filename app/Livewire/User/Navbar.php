<?php

namespace App\Livewire\User;

use Livewire\Component;

class Navbar extends Component
{
    public $unreadNotificationsCount = 0; // Example of interactive data

    public function mount()
    {
        // Load notification count or other dynamic data
        // For example: $this->unreadNotificationsCount = auth()->user()->unreadNotifications()->count();
        // For now, let's use a random number for demonstration:
        $this->unreadNotificationsCount = rand(0, 10);
    }

    // Example of an interactive method
    public function markAllNotificationsAsRead()
    {
        // Implement logic here to mark user's notifications as read
        // Example: auth()->user()->unreadNotifications->markAsRead();
        $this->unreadNotificationsCount = 0; // Update the count instantly
        session()->flash('navbar-message', 'All notifications marked as read!');
    }

    public function render()
    {
        return view('livewire.user.navbar');
    }
}