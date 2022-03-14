<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Signup\Confirm;

class Command
{
    /** @var string */
    public string $token;
}