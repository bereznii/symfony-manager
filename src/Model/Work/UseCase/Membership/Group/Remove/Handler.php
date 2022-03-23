<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Membership\Group\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Membership\Group\GroupRepository;
use App\Model\Work\Entity\Membership\Group\Id;
use App\Model\Work\Entity\Membership\Member\MemberRepository;

class Handler
{
    /**
     * @param GroupRepository $groups
     * @param MemberRepository $members
     * @param Flusher $flusher
     */
    public function __construct(
        private GroupRepository $groups,
        private MemberRepository $members,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $group = $this->groups->get(new Id($command->id));

        if ($this->members->hasByGroup($group->getId())) {
            throw new \DomainException('Group is not empty.');
        }

        $this->groups->remove($group);

        $this->flusher->flush();
    }
}