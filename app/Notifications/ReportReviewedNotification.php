<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReviewedNotification extends Notification
{
    use Queueable;

    public $report;
    public $status;

    public function __construct($report, $status)
    {
        $this->report = $report;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $message = ($this->status === 'approved')
            ? 'Votre signalement pour l\'élément ID ' . $this->report->reportable_id . ' a été approuvé.'
            : 'Votre signalement pour l\'élément ID ' . $this->report->reportable_id . ' a été rejeté.';

        $icon = ($this->status === 'approved') ? 'check-circle' : 'close-circle';
        $color = ($this->status === 'approved') ? 'green' : 'red';

        return [
            'report_id' => $this->report->id,
            'reportable_id' => $this->report->reportable_id,
            'reportable_type' => $this->report->reportable_type,
            'message' => $message,
            'status' => $this->status,
            'icon' => $icon,
            'color' => $color,
            'link' => '/admin/reports/' . $this->report->id,
        ];
    }
}