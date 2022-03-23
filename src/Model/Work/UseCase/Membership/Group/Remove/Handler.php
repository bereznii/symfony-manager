<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Membership\Group\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Membership\Group\GroupRepository;
use App\Model\Work\Entity\Membership\Group\Id;

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
        $group = $this->groups->get(new Id($command->id));

        $this->groups->remove($group);

        $this->flusher->flush();
    }
}