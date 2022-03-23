<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Membership\Group\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Membership\Group\Group;
use App\Model\Work\Entity\Membership\Group\Id;
use App\Model\Work\Entity\Membership\Group\GroupRepository;

class Handler
{
    /**
     * @param GroupRepository $groups
     * @param Flusher $flusher
     */
    public function __construct(
        private GroupRepository $groups,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $group = new Group(
            Id::next(),
            $command->name
        );

        $this->groups->add($group);

        $this->flusher->flush();
    }
}