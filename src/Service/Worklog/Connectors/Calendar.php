<?php declare(strict_types=1);

namespace App\Service\Worklog\Connectors;

use App\Service\Worklog\TaskType;

class Calendar extends AbstractConnector
{
    public function getStandardTaskType ()
    {
        return TaskType::TYPE_MEETING;
    }
}