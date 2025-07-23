<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewReportNotification;

class ReportButton extends Component
{
    public $reportableId;
    public $reportableType;
    public $reason = '';
    public $isOkay = false;

    public function mount($reportableId, $reportableType)
    {
        $this->reportableId = $reportableId;
        $this->reportableType = $reportableType;
    }

    public function openReport()
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to report.');
            $this->isOkay = false;
            return redirect()->route('login');
        }
        $this->isOkay = true;
    }

    public function closeReport()
    {
        $this->isOkay = false;
        $this->reset('reason');
    }

    public function submitReport()
    {
        $this->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to report.');
            $this->isOkay = false;
            return redirect()->route('login');
        }

        try {
            $reportableModel = $this->reportableType::find($this->reportableId);
            if (!$reportableModel) {
                session()->flash('error', 'The item to report was not found.');
                $this->closeReport();
                return;
            }

            $existingReport = Report::where('user_id', Auth::id())
                                    ->where('reportable_type', $this->reportableType)
                                    ->where('reportable_id', $this->reportableId)
                                    ->first();

            if ($existingReport) {
                session()->flash('error', 'You have already reported this item.');
                $this->closeReport();
                return;
            }

            $report = Report::create([
                'user_id' => Auth::id(),
                'reportable_type' => $this->reportableType,
                'reportable_id' => $this->reportableId,
                'reason' => $this->reason,
                'status' => 'pending',
            ]);

            // Notifier les administrateurs qu'un nouveau signalement a été soumis
            $admins = User::where('role', 'admin')
                          ->orWhere('role', 'super-admin')
                          ->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewReportNotification($report));
            }

            session()->flash('success', 'Your report has been submitted successfully.');
            $this->closeReport();
        } catch (\Exception $e) {
            session()->flash('error', 'Error submitting report: ' . $e->getMessage());
            \Log::error('Report error: ' . $e->getMessage());
            $this->isOkay = false;
        }
    }

    public function render()
    {
        return view('livewire.report-button');
    }
}
