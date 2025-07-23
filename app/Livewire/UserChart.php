<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Community;

class UserChart extends Component
{
    public $labels = []; // Stores community names (e.g., ['Tech', 'Sports', 'Art'])
    public $counts = []; // Stores post counts for each community (e.g., [150, 80, 200])

    /**
     * Initializes the component, loading the chart data.
     */
    public function mount()
    {
        $this->loadChartData();
    }

    /**
     * Fetches community data and their post counts to populate chart properties.
     */
    public function loadChartData()
    {
        // Get all communities and eager load the count of their associated posts
        // Order by community name for consistent chart display
        $communities = Community::withCount('posts')->orderBy('name')->get();

        // Extract community names as labels for the chart
        $this->labels = $communities->pluck('name')->toArray();
        // Extract post counts as data for the chart
        $this->counts = $communities->pluck('posts_count')->toArray();
    }

    /**
     * Renders the Livewire component's view.
     */
    public function render()
    {
        // The view 'livewire.user-chart' should be changed to reflect the component's purpose,
        // e.g., 'livewire.posts-per-community-chart'. For now, we'll keep 'user-chart'
        // as per your provided code, but it's a good practice to match.
        return view('livewire.user-chart');
    }
}