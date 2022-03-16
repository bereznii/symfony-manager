<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;

class PasswordHasher
{
    use CheckPasswordLengthTrait;

    /**
     * @param string $plainPassword
     * @return string
     */
    public function hash(string $plainPassword): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException();
        }

        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }
}