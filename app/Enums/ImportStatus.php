<?php

namespace App\Enums;

enum ImportStatus
{
    public const COMPLETED = 'completed';
    public const FAILED = 'failed';

    public const SUCCESSFUL_MESSAGE = 'Successful import';
    public const ERROR_MESSAGE = 'Unsuccessful import';
}
