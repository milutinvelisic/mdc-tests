<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = [
        'import_type',
        'file_key',
        'original_file_name',
        'stored_file_path',
        'status',
        'message',
        'user_id',
    ];

    public function markRunning(): void
    {
        $this->update([
            'status' => 'running',
            'updated_at' => now(),
        ]);
    }

    public function markFailed(string $message): void
    {
        $this->update([
            'status' => 'failed',
            'message' => $message,
            'updated_at' => now(),
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'message' => 'Import completed',
            'updated_at' => now(),
        ]);
    }
}
