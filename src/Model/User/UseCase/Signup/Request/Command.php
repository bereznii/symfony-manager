<?php

namespace App\Model\User\UseCase\Signup\Request;

class Command
{
    /** @var string */
    public $email;

    /** @var string */
    public $password;
}