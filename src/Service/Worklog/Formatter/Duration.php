<?php declare(strict_types=1);

namespace App\Service\Worklog\Formatter;

use Carbon\CarbonInterval;

class Duration
{
    public static function format($duration)
    {
        $interval = CarbonInterval::seconds($duration);

        return $interval->totalHours . 'h ('.$interval->totalMinutes.'m)';
    }
}