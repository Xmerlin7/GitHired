<?php

namespace App\Enums;

enum WorkType: string
{
    case FULL_TIME = 'full-time';
    case PART_TIME = 'part-time';
    case REMOTE = 'remote';
    case HYBRID = 'hybrid';
    case INTERNSHIP = 'internship';
}
