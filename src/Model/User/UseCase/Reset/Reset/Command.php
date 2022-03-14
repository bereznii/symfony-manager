<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Reset;

class Command
{
    /**
     * @var string
     */
    public string $password;

    /**
     * @param string $token
     */
    public function __construct(
        public string $token
    ) {}
}