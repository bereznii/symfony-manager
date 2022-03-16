<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class ResetTokenSender
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
     * @param ResetToken $token
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function send(Email $email, ResetToken $token): void
    {
        $message = (new \Symfony\Component\Mime\Email())
            ->from(new Address(...$this->from))
            ->to($email->getValue())
            ->subject('Password resetting')
            ->html($this->twig->render('mail/user/reset.html.twig', [
                'token' => $token->getToken()
            ]));

        try {
            $this->mailer->send($message);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to send email.');
        }
    }
}