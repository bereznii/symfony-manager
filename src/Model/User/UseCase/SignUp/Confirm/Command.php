<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Confirm;

class Command
{
    /**
     * @param string $token
     */
    public function __construct(
        public string $token
    ) {}
}