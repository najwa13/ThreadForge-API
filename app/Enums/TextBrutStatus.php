<?php

namespace App\Enums;

enum TextBrutStatus: string
{
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case FAILED = 'failed';
}