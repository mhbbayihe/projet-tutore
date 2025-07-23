<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Report;

class NewReportNotification extends Notification
{
    use Queueable;

    public $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'report_id' => $this->report->id,
            'reportable_id' => $this->report->reportable_id,
            'reportable_type' => $this->report->reportable_type,
            'message' => 'New report for a ' . class_basename($this->report->reportable_type) . ' (ID: ' . $this->report->user->name . ')',
            'icon' => 'alert-circle',
            'color' => 'orange',
            'link' => '/admin/reports/' . $this->report->id,
        ];
    }
}