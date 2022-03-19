<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\NewEmailConfirmTokenizer;
use App\Model\User\Service\NewEmailConfirmTokenSender;

class Handler
{
    /**
     * @param UserRepository $users
     * @param NewEmailConfirmTokenizer $tokenizer
     * @param NewEmailConfirmTokenSender $sender
     * @param Flusher $flusher
     */
    public function __construct(
        private UserRepository $users,
        private NewEmailConfirmTokenizer $tokenizer,
        private NewEmailConfirmTokenSender $sender,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('Email is already in use.');
        }

        $user->requestEmailChanging(
            $email,
            $token = $this->tokenizer->generate()
        );

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}