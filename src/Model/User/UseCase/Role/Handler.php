<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    /**
     * @param UserRepository $users
     * @param Flusher $flusher
     */
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $user->changeRole(new Role($command->role));

        $this->flusher->flush();
    }
}