<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ImportStartedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $import_id,
        public string $import_type,
        public string $file_key
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Import Started',
            'message' => "Your import {$this->import_type}/{$this->file_key} has started.",
            'import_id' => $this->import_id,
            'level' => 'info'
        ];
    }
}
