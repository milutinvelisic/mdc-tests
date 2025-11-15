<?php

namespace App\Enums;

enum ImportStatus
{
    public const COMPLETED = 'completed';
    public const FAILED = 'failed';
    public const PENDING = 'pending';

    public const SUCCESSFUL_MESSAGE = 'Successful import';
    public const ERROR_MESSAGE = 'Unsuccessful import';
    public const PENDING_MESSAGE = 'Pending import';
}
