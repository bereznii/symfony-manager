<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;

class ConfirmTokenSender
{
    /**
     * @param Email $email
     * @param string $token
     * @return void
     */
    public function send(Email $email, string $token): void
    {
        //TODO: fill
    }
}