<?php

declare(strict_types=1);

namespace App\Model\User\Service;

class PasswordHasher
{
    /**
     * @param string $password
     * @return string
     */
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }
}