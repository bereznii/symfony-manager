<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ResetTokenizer;
use App\Model\User\Service\ResetTokenSender;

class Handler
{
    /**
     * @param UserRepository $users
     * @param ResetTokenizer $tokenizer
     * @param Flusher $flusher
     * @param ResetTokenSender $sender
     */
    public function __construct(
        private UserRepository $users,
        private ResetTokenizer $tokenizer,
        private Flusher $flusher,
        private ResetTokenSender $sender
    ) {}

    /**
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));

        $user->requestPasswordReset(
            $this->tokenizer->generate(),
            new \DateTimeImmutable()
        );

        $this->flusher->flush();
        $this->sender->send($user->getEmail(), $user->getResetToken());
    }
}