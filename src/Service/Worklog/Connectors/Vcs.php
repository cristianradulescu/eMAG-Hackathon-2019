<?php declare(strict_types=1);

namespace App\Service\Worklog\Connectors;

use App\Service\Worklog\TaskType;
use App\Service\Worklog\WorklogDto;
use App\Service\Worklog\WorklogItemDto;

class Vcs extends AbstractConnector
{
    public function getStandardTaskType()
    {
        return TaskType::TYPE_DEVELOPMENT;
    }

    public function getDefaultContent($sourceContent): string
    {
        return trim(\preg_replace('/(\w{3}-\d)\w+/', '', $sourceContent));
    }

    public function getData(): WorklogDto
    {
        $storiesIds = [];
        /** @var WorklogDto $worklog */
        $worklog = parent::getData();
        /** @var WorklogItemDto $worklogItem */
        foreach ($worklog->items as $key => $worklogItem) {
            preg_match('/(\w{3}-\d)\w+/', $worklogItem->name, $matches);
            $storyId = trim($matches[0]);
            $content = trim(\preg_replace('/(\w{3}-\d)\w+/', '', $worklogItem->name));
            if (\array_key_exists($storyId, $storiesIds)) {
                $storiesIds[$storyId]->content .= '; '.$content;
                unset($worklog->items[$key]);
            } else {
                $storiesIds[$storyId] = $worklogItem;
            }
            $storiesIds[$storyId]->name = $storyId.' (Story)';
        }

        return $worklog;
    }
}