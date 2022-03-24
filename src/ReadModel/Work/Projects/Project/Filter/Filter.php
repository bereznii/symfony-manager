<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project\Filter;

use App\Model\Work\Entity\Projects\Project\Status;

class Filter
{
    /** @var string */
    public $name;

    /** @var string */
    public $status = Status::ACTIVE;
}