<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Membership\Member;

use App\Model\Work\Entity\Membership\Group\Group;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_members_members')]
class Member
{
    /** @var Id */
    #[ORM\Column(type: 'work_members_member_id')]
    #[ORM\Id]
    private $id;

    /** @var Group */
    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private $group;

    /** @var Name */
    #[ORM\Embedded(class: Name::class)]
    private $name;

    /** @var Email */
    #[ORM\Column(type: 'work_members_member_email')]
    private $email;

    /** @var string */
    #[ORM\Column(type: 'work_members_member_status', length: 16)]
    private $status;

    /**
     * @param Id $id
     * @param Group $group
     * @param Name $name
     * @param Email $email
     */
    public function __construct(Id $id, Group $group, Name $name, Email $email)
    {
        $this->id = $id;
        $this->group = $group;
        $this->name = $name;
        $this->email = $email;
        $this->status = Status::active();
    }

    /**
     * @param Name $name
     * @param Email $email
     * @return void
     */
    public function edit(Name $name, Email $email): void
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @param Group $group
     * @return void
     */
    public function move(Group $group): void
    {
        $this->group = $group;
    }

    /**
     * @return void
     */
    public function archive(): void
    {
        if ($this->status->isArchived()) {
            throw new \DomainException('Member is already archived.');
        }
        $this->status = Status::archived();
    }

    /**
     * @return void
     */
    public function reinstate(): void
    {
        if ($this->status->isActive()) {
            throw new \DomainException('Member is already active.');
        }
        $this->status = Status::active();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}