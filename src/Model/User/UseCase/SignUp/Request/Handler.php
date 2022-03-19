<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\RegisterConfirmTokenSender;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\RegisterConfirmTokenizer;

class Handler
{
    /**
     * @param UserRepository $users
     * @param PasswordHasher $hasher
     * @param RegisterConfirmTokenizer $tokenizer
     * @param RegisterConfirmTokenSender $sender
     * @param Flusher $flusher
     */
    public function __construct(
        private UserRepository             $users,
        private PasswordHasher             $hasher,
        private RegisterConfirmTokenizer   $tokenizer,
        private RegisterConfirmTokenSender $sender,
        private Flusher                    $flusher
    ) {}

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $user = User::signUpByEmail(
            id: Id::next(),
            created_at: new \DateTimeImmutable(),
            email: $email,
            hash: $this->hasher->hash($command->password),
            confirmToken: $token = $this->tokenizer->generate(),
            name: new Name(
                $command->firstName,
                $command->lastName,
            )
        );

        $this->users->add($user);
        $this->sender->send($email, $token);
        $this->flusher->flush();
    }
}