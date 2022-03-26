<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Employees\Group\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Employees\Group\Id;
use App\Model\Work\Entity\Employees\Group\GroupRepository;

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

        $group->edit($command->name);

        $this->flusher->flush();
    }
}