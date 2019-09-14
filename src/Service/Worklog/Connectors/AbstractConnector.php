<?php declare(strict_types=1);

namespace App\Service\Worklog\Connectors;

use App\Service\Worklog\DataSource\AbstractDataSource;
use App\Service\Worklog\DataSource\FileDataSource;
use App\Service\Worklog\Formatter\Duration;
use App\Service\Worklog\WorklogDto;
use App\Service\Worklog\WorklogItemDto;
use Carbon\CarbonInterval;

abstract class AbstractConnector
{
    /** @var AbstractDataSource */
    private $dataSource;

    public function setDataSource(FileDataSource $dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function getData(): WorklogDto
    {
        $data = $this->dataSource->getData();
        $worklog = new WorklogDto();
        $worklog->date = $data['date'];

        foreach ($data['items'] as $item) {
            $worklogItem = new WorklogItemDto();
            $worklogItem->name = $item['name'] ?? null;
            $worklogItem->duration = $item['duration'] ?? null;
            if (null !== $worklogItem->duration) {
                $worklogItem->durationFormatted = Duration::format($worklogItem->duration);
            }
            $worklogItem->content = $item['content'] ?? $this->getDefaultContent($worklogItem->name);
            $worklogItem->taskType = $item['task_type'] ?? $this->getStandardTaskType();
            $worklogItem->taskId = $item['task_id'] ?? null;
            $worklog->items[] = $worklogItem;
        }

        return $worklog;
    }

    public function getStandardTaskType()
    {
        return null;
    }

    public function getDefaultContent($sourceContent)
    {
        return $sourceContent;
    }
}