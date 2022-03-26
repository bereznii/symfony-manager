<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project\Filter;

use App\Model\Work\Entity\Projects\Project\Status;

class Filter
{
    /** @var string|null */
    public $member;

    /** @var string */
    public $name;

    /** @var string */
    public $status = Status::ACTIVE;

    /**
     * @param string|null $member
     */
    private function __construct(?string $member = null)
    {
        $this->member = $member;
    }

    /**
     * @return static
     */
    public static function all(): self
    {
        return new self();
    }

    /**
     * @param string $id
     * @return static
     */
    public static function forMember(string $id): self
    {
        return new self($id);
    }
}