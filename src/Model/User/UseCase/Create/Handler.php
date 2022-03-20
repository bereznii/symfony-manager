<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordGenerator;
use App\Model\User\Service\PasswordHasher;

class Handler
{
    /**
     * @param UserRepository $users
     * @param PasswordHasher $hasher
     * @param PasswordGenerator $generator
     * @param Flusher $flusher
     */
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private PasswordGenerator $generator,
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
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $user = User::create(
            id: Id::next(),
            created_at: new \DateTimeImmutable(),
            name: new Name(
                $command->firstName,
                $command->lastName
            ),
            email: $email,
            hash: $this->hasher->hash($this->generator->generate())
        );

        $this->users->add($user);

        $this->flusher->flush();
    }
}