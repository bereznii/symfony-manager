<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use App\Model\User\Entity\User\Email as UserEmail;
use Twig\Environment;

class RegisterConfirmTokenSender
{
    /**
     * @param MailerInterface $mailer
     * @param Environment $twig
     * @param array $from
     */
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private array $from
    ) {}

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function send(UserEmail $email, string $token): void
    {
        $message = (new Email())
            ->from(new Address(...$this->from))
            ->to($email->getValue())
            ->subject('Sign Up Confirmation')
            ->html($this->twig->render('mail/user/signup.html.twig', [
                'token' => $token
            ]));

        try {
            $this->mailer->send($message);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to send email.');
        }
    }
}