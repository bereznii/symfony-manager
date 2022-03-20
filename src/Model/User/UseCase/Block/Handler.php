<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Block;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
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

        $user->block();

        $this->flusher->flush();
    }
}