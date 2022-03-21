<?php

declare(strict_types=1);

namespace App\ReadModel\User\View;

class NetworkView
{
    /**
     * @param string $identity
     * @param string $network
     */
    public function __construct(
        public string $identity,
        public string $network,
    ) {}
}