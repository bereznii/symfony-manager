<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Role;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_roles')]
class Role
{
    /** @var Id */
    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_role_id')]
    private $id;

    /** @var string */
    #[ORM\Column(type: 'string', unique: true)]
    private $name;

    /** @var array */
    #[ORM\Column(type: 'work_projects_role_permissions')]
    private $permissions;

    /**
     * Role constructor.
     * @param Id $id
     * @param string $name
     * @param string[] $permissions
     */
    public function __construct(Id $id, string $name, array $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->setPermissions($permissions);
    }

    /**
     * @param string $name
     * @param string[] $permissions
     */
    public function edit(string $name, array $permissions): void
    {
        $this->name = $name;
        $this->setPermissions($permissions);
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->exists(function ($key, Permission $current) use ($permission) {
            return $current->isNameEqual($permission);
        });
    }

    /**
     * @param Id $id
     * @param string $name
     * @return $this
     */
    public function clone(Id $id, string $name): self
    {
        return new self($id, $name, array_map(static function (Permission $permission) {
            return $permission->getName();
        }, $this->permissions->toArray()));
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions->toArray();
    }

    /**
     * @param array $names
     * @return void
     */
    public function setPermissions(array $names): void
    {
        $this->permissions = new ArrayCollection(array_map(static function (string $name) {
            return new Permission($name);
        }, array_unique($names)));
    }
}