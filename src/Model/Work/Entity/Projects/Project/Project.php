<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Employees\Member\Member;
use App\Model\Work\Entity\Employees\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Role\Role;
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
        mappedBy: 'project', targetEntity: Department::class,
        cascade: ['all'], orphanRemoval: true
    )]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private $departments;

    /** @var ArrayCollection */
    #[ORM\OneToMany(
        mappedBy: 'project', targetEntity: Membership::class,
        cascade: ['all'], orphanRemoval: true
    )]
    private $memberships;

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
        $this->memberships = new ArrayCollection();
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
     * @param MemberId $id
     * @return bool
     */
    public function hasMember(MemberId $id): bool
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param MemberId $id
     * @param string $permission
     * @return bool
     */
    public function isMemberGranted(MemberId $id, string $permission): bool
    {
        foreach ($this->memberships as $membership) {
            /** @var Membership $membership */
            if ($membership->isForMember($id)) {
                return $membership->isGranted($permission);
            }
        }
        return false;
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
     * @param Member $member
     * @param DepartmentId[] $departmentIds
     * @param Role[] $roles
     * @throws \Exception
     */
    public function addMember(Member $member, array $departmentIds, array $roles): void
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($member->getId())) {
                throw new \DomainException('Member already exists.');
            }
        }
        $departments = array_map([$this, 'getDepartment'], $departmentIds);
        $this->memberships->add(new Membership($this, $member, $departments, $roles));
    }

    /**
     * @param MemberId $member
     * @param DepartmentId[] $departmentIds
     * @param Role[] $roles
     */
    public function editMember(MemberId $member, array $departmentIds, array $roles): void
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($member)) {
                $membership->changeDepartments(array_map([$this, 'getDepartment'], $departmentIds));
                $membership->changeRoles($roles);
                return;
            }
        }
        throw new \DomainException('Member is not found.');
    }

    /**
     * @param MemberId $member
     * @return void
     */
    public function removeMember(MemberId $member): void
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($member)) {
                $this->memberships->removeElement($membership);
                return;
            }
        }
        throw new \DomainException('Member is not found.');
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

    /**
     * @return array|mixed[]
     */
    public function getMemberships()
    {
        return $this->memberships->toArray();
    }

    /**
     * @param MemberId $id
     * @return Membership
     */
    public function getMembership(MemberId $id): Membership
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($id)) {
                return $membership;
            }
        }
        throw new \DomainException('Member is not found.');
    }
}