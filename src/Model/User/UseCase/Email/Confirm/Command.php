<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Confirm;

class Command
{
    /**
     * @param string $id
     * @param string $token
     */
    public function __construct(
        public string $id,
        public string $token
    ) {}
}