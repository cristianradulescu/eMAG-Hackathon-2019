<?php declare(strict_types=1);

namespace App\Service\Worklog\Connectors;

use App\Service\Worklog\DataSource\AbstractDataSource;
use App\Service\Worklog\DataSource\FileDataSource;
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
                $worklogItem->durationFormatted = $this->formatWorklogItemDuration($worklogItem);
            }
            $worklogItem->content = $item['content'] ?? $this->getDefaultContent($worklogItem->name);
            $worklogItem->taskType = $item['task_type'] ?? $this->getStandardTaskType();
            $worklog->items[] = $worklogItem;
        }

        return $worklog;
    }

    private function formatWorklogItemDuration(WorklogItemDto $worklogItem): string
    {
        $interval = CarbonInterval::seconds($worklogItem->duration);
        $formattedDate = ($interval->totalDays >= 1 ? $interval->totalDays : 0).'d';
        $formattedDate .= ' '.($interval->totalHours >= 1 ? $interval->totalHours : 0).'h';
        $formattedDate .= ' '.($interval->totalMinutes >= 1 ? $interval->totalMinutes : 0).'m';

        return $formattedDate;
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