<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Employees\Member\Edit;

use App\Model\Work\Entity\Employees\Member\Member;
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

    /** @var string */
    #[Assert\NotBlank]
    public $email;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param Member $member
     * @return static
     */
    public static function fromMember(Member $member): self
    {
        $command = new self($member->getId()->getValue());
        $command->firstName = $member->getName()->getFirst();
        $command->lastName = $member->getName()->getLast();
        $command->email = $member->getEmail()->getValue();
        return $command;
    }
}