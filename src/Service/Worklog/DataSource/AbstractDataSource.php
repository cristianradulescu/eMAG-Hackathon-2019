<?php declare(strict_types=1);

namespace App\Service\Worklog\DataSource;

abstract class AbstractDataSource
{
    protected $source;

    public function __construct ($source)
    {
        $this->source = $source;
    }

    public function getSource ()
    {
        return $this->source;
    }

    abstract public function getData();
}