<?php declare(strict_types=1);

namespace App\Service\Worklog;

class WorklogItemDto
{
    public $name;
    public $duration;
    public $durationFormatted;
    public $content;
    public $taskType;
}