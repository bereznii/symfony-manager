<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_projects')]
class Project
{
    /** @var Id */
    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_project_id')]
    private $id;

    /** @var string */
    #[ORM\Column(type: 'string')]
    private $name;

    /** @var int */
    #[ORM\Column(type: 'integer')]
    private $sort;

    /** @var Status */
    #[ORM\Column(type: 'work_projects_project_status', length: 16)]
    private $status;

    /** @var Department[] */
    #[ORM\OneToMany(
        mappedBy: 'project', targetEntity: 'App\Model\Work\Entity\Projects\Project\Department\Department',
        cascade: ['all'], orphanRemoval: true
    )]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private $departments;

    /**
     * @param Id $id
     * @param string $name
     * @param int $sort
     */
    public function __construct(Id $id, string $name, int $sort)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
        $this->status = Status::active();
        $this->departments = new ArrayCollection();
    }

    /**
     * @param string $name
     * @param int $sort
     * @return void
     */
    public function edit(string $name, int $sort): void
    {
        $this->name = $name;
        $this->sort = $sort;
    }

    /**
     * @return void
     */
    public function archive(): void
    {
        if ($this->isArchived()) {
            throw new \DomainException('Project is already archived.');
        }
        $this->status = Status::archived();
    }

    /**
     * @return void
     */
    public function reinstate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Project is already active.');
        }
        $this->status = Status::active();
    }

    /**
     * @param DepartmentId $id
     * @param string $name
     * @return void
     */
    public function addDepartment(DepartmentId $id, string $name): void
    {
        foreach ($this->departments as $department) {
            if ($department->isNameEqual($name)) {
                throw new \DomainException('Department already exists.');
            }
        }
        $this->departments->add(new Department($this, $id, $name));
    }

    /**
     * @param DepartmentId $id
     * @param string $name
     * @return void
     */
    public function editDepartment(DepartmentId $id, string $name): void
    {
        foreach ($this->departments as $current) {
            if ($current->getId()->isEqual($id)) {
                $current->edit($name);
                return;
            }
        }
        throw new \DomainException('Department is not found.');
    }

    /**
     * @param DepartmentId $id
     * @return void
     */
    public function removeDepartment(DepartmentId $id): void
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                $this->departments->removeElement($department);
                return;
            }
        }
        throw new \DomainException('Department is not found.');
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
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
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getDepartments()
    {
        return $this->departments->toArray();
    }

    /**
     * @param DepartmentId $id
     * @return Department
     */
    public function getDepartment(DepartmentId $id): Department
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                return $department;
            }
        }
        throw new \DomainException('Department is not found.');
    }
}