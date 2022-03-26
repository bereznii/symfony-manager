<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Employees\Member\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Employees\Group\GroupRepository;
use App\Model\Work\Entity\Employees\Group\Id as GroupId;
use App\Model\Work\Entity\Employees\Member\Email;
use App\Model\Work\Entity\Employees\Member\Member;
use App\Model\Work\Entity\Employees\Member\Id;
use App\Model\Work\Entity\Employees\Member\MemberRepository;
use App\Model\Work\Entity\Employees\Member\Name;

class Handler
{
    /**
     * @param MemberRepository $members
     * @param GroupRepository $groups
     * @param Flusher $flusher
     */
    public function __construct(
        private MemberRepository $members,
        private GroupRepository $groups,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(Command $command): void
    {
        $id = new Id($command->id);

        if ($this->members->has($id)) {
            throw new \DomainException('Member already exists.');
        }

        $group = $this->groups->get(new GroupId($command->group));

        $member = new Member(
            $id,
            $group,
            new Name(
                $command->firstName,
                $command->lastName
            ),
            new Email($command->email)
        );

        $this->members->add($member);

        $this->flusher->flush();
    }
}
