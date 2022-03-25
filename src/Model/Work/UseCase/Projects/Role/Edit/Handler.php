<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

class Handler
{
    /**
     * @param RoleRepository $roles
     * @param Flusher $flusher
     */
    public function __construct(
        private RoleRepository $roles,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $role = $this->roles->get(new Id($command->id));

        $role->edit($command->name, $command->permissions);

        $this->flusher->flush();
    }
}