<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Membership\Group;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_members_groups')]
class Group
{
    /** @var Id */
    #[ORM\Column(type: 'work_members_group_id')]
    #[ORM\Id]
    private $id;

    /** @var string */
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @param Id $id
     * @param string $name
     */
    public function __construct(Id $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function edit(string $name): void
    {
        $this->name = $name;
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
}