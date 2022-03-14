<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Signup\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class Handler
{
    /**
     * @param UserRepository $users
     * @param PasswordHasher $hasher
     * @param ConfirmTokenizer $tokenizer
     * @param ConfirmTokenSender $sender
     * @param Flusher $flusher
     */
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private ConfirmTokenizer $tokenizer,
        private ConfirmTokenSender $sender,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $user = new User(
            id: Id::next(),
            created_at: new \DateTimeImmutable(),
            email: $email,
            passwordHash: $this->hasher->hash($command->password),
            token: $token = $this->tokenizer->generate(),
        );

        $this->users->add($user);
        $this->sender->send($email, $token);
        $this->flusher->flush();
    }
}