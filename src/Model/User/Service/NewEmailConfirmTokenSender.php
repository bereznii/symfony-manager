<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class NewEmailConfirmTokenSender
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
     * @param Email $email
     * @param string $token
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function send(Email $email, string $token): void
    {
        $message = (new \Symfony\Component\Mime\Email())
            ->from(new Address(...$this->from))
            ->to($email->getValue())
            ->subject('Email Confirmation')
            ->html($this->twig->render('mail/user/email.html.twig', [
                'token' => $token
            ]));

        try {
            $this->mailer->send($message);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to send email.');
        }
    }
}