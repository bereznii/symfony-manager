<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

class Command
{
    /**
     * @param string $network
     * @param string $identity
     */
    public function __construct(
        public string $network,
        public string $identity
    ) {}
}