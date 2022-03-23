<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Membership\Member\Move;

use App\Model\Flusher;
use App\Model\Work\Entity\Membership\Group\GroupRepository;
use App\Model\Work\Entity\Membership\Group\Id as GroupId;
use App\Model\Work\Entity\Membership\Member\Id;
use App\Model\Work\Entity\Membership\Member\MemberRepository;

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