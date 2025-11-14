<?php

namespace App\Listeners;

use App\Events\ImportFailed;
use App\Mail\ImportFailedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendImportFailedEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ImportFailed $event)
    {
        $import = $event->import;
        // find user email if available
        if ($import->user_id) {
            $user = \App\Models\User::find($import->user_id);
            if ($user && $user->email) {
                Mail::to($user->email)->send(new ImportFailedMail($import));
            }
        }
    }
}
