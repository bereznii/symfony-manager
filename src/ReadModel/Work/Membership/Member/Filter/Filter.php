<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Membership\Member\Filter;

use App\Model\Work\Entity\Membership\Member\Status;

class Filter
{
    public $name;
    public $email;
    public $group;
    public $status = Status::ACTIVE;
}