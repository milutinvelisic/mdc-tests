<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ImportFinishedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $import_id,
        public string $import_type,
        public string $file_key,
        public string $status,
        public ?string $message
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->status === 'completed'
                ? 'Import Completed'
                : 'Import Failed',

            'message' => $this->status === 'completed'
                ? "Import {$this->import_type}/{$this->file_key} is finished."
                : "Import {$this->import_type}/{$this->file_key} failed: {$this->message}",

            'status' => $this->status,
            'import_id' => $this->import_id,
            'level' => $this->status === 'completed' ? 'success' : 'error'
        ];
    }
}
