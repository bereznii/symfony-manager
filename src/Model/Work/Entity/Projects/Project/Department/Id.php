<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project\Department;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
    /** @var string */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param Id $another
     * @return bool
     */
    public function isEqual(self $another): bool
    {
        return $this->getValue() === $another->getValue();
    }
}