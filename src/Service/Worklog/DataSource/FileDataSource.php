<?php declare(strict_types=1);

namespace App\Service\Worklog\DataSource;

class FileDataSource extends AbstractDataSource
{
    public function getData(): array
    {
        return \json_decode(\file_get_contents($this->getSource()), true);
    }
}