<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Name;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /** @var string */
    #[Assert\NotBlank]
    public $id;

    /** @var string */
    #[Assert\NotBlank]
    public $firstName;

    /** @var string */
    #[Assert\NotBlank]
    public $lastName;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}