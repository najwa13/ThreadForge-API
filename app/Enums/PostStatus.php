<?php

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case ARCHIVED = 'archived';
    case POSTED = 'posted';
}