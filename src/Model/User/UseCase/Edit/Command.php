<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Edit;

use App\Model\User\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /** @var string */
    #[Assert\NotBlank]
    public $id;

    #[Assert\NotBlank]
    #[Assert\Email]
    public $email;

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

    /**
     * @param User $user
     * @return static
     */
    public static function fromUser(User $user): self
    {
        $command = new self($user->getId()->getValue());
        $command->email = $user->getEmail()?->getValue();
        $command->firstName = $user->getName()->getFirst();
        $command->lastName = $user->getName()->getLast();
        return $command;
    }
}