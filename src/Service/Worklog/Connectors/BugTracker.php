<?php declare(strict_types=1);

namespace App\Service\Worklog\Connectors;

use App\Service\Worklog\WorklogDto;
use App\Service\Worklog\WorklogItemDto;

class BugTracker extends AbstractConnector
{
    public function getData(): WorklogDto
    {
        $worklog = parent::getData();
        /** @var WorklogItemDto $worklogItem */
        foreach ($worklog->items as $key => $worklogItem) {
            $worklogItem->name = $worklogItem->taskId.' '.$worklogItem->name;
        }

        return $worklog;
    }

}