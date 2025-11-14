<?php

namespace App\Jobs;

use App\Enums\ImportStatus;
use App\Events\ImportFailed;
use App\Models\Import;
use App\Models\User;
use App\Notifications\ImportFinishedNotification;
use App\Services\Import\ImportRunner;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ProcessImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $importId;

    public function __construct(int $importId)
    {
        $this->importId = $importId;
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        /** @var Import $import */
        $import = Import::find($this->importId);
        if (!$import) return;

        $import->markRunning();

        // All heavy logic moved to ImportRunner
        $runner = new ImportRunner($import);
        $errors = $runner->run();

        $this->handleResult($import, $errors);
    }

    protected function handleResult(Import $import, int $errors): void
    {
        $user = User::find($import->user_id);

        if ($errors > 0) {
            $message = "{$errors} rows failed validation";

            $import->markFailed($message);

            ImportFailed::dispatch($import, $message);

            Notification::send(
                $user,
                new ImportFinishedNotification(
                    $import->id,
                    $import->import_type,
                    $import->file_key,
                    ImportStatus::FAILED,
                    ImportStatus::ERROR_MESSAGE
                )
            );

            return;
        }

        // success
        $import->markCompleted();

        Notification::send(
            $user,
            new ImportFinishedNotification(
                $import->id,
                $import->import_type,
                $import->file_key,
                ImportStatus::COMPLETED,
                ImportStatus::SUCCESSFUL_MESSAGE
            )
        );
    }
}
