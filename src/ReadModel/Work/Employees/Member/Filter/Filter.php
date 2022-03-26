<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Employees\Member\Filter;

use App\Model\Work\Entity\Employees\Member\Status;

class Filter
{
    public $name;
    public $email;
    public $group;
    public $status = Status::ACTIVE;
}