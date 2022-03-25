<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Copy;

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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(Command $command): void
    {
        $current = $this->roles->get(new Id($command->id));

        if ($this->roles->hasByName($command->name)) {
            throw new \DomainException('Role already exists.');
        }

        $role = $current->clone(
            Id::next(),
            $command->name
        );

        $this->roles->add($role);

        $this->flusher->flush();
    }
}