<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Confirm\Manual;

class Command
{
    /**
     * @param string $id
     */
    public function __construct(
        public string $id
    ) {}
}