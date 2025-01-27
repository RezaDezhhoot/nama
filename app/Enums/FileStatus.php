<?php

namespace App\Enums;

enum FileStatus: string
{
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case PROCESSING = 'processing';
    case ERROR = 'error';
}
