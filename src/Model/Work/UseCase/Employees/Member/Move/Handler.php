<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Employees\Member\Move;

use App\Model\Flusher;
use App\Model\Work\Entity\Employees\Group\GroupRepository;
use App\Model\Work\Entity\Employees\Group\Id as GroupId;
use App\Model\Work\Entity\Employees\Member\Id;
use App\Model\Work\Entity\Employees\Member\MemberRepository;

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
     */
    public function handle(Command $command): void
    {
        $member = $this->members->get(new Id($command->id));
        $group = $this->groups->get(new GroupId($command->group));

        $member->move($group);

        $this->flusher->flush();
    }
}