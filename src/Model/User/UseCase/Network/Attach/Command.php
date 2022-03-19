<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;

class Command
{
    /**
     * @param string $user
     * @param string $network
     * @param string $identity
     */
    public function __construct(
        public string $user,
        public string $network,
        public string $identity
    ) {}
}