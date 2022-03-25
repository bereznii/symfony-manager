<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Role;

use Webmozart\Assert\Assert;

class Permission
{
    public const MANAGE_PROJECT_MEMBERS = 'manage_project_members';

    /** @var string */
    private $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf($name, self::names());
        $this->name = $name;
    }

    /**
     * @return string[]
     */
    public static function names(): array
    {
        return [
            self::MANAGE_PROJECT_MEMBERS,
        ];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isNameEqual(string $name): bool
    {
        return $this->name === $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}