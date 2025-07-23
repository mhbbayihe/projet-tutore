<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Community;

class CommunityUserChart extends Component
{
    public $labels = [];
    public $counts = [];

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        $communities = Community::withCount('users')->orderBy('name')->get();

        $this->labels = $communities->pluck('name')->toArray();
        $this->counts = $communities->pluck('users_count')->toArray();
    }

    public function render()
    {
        return view('livewire.community-user-chart');
    }
}
