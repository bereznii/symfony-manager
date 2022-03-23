<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Membership\Member\Move;

use App\Model\Work\Entity\Membership\Member\Member;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /** @var string */
    #[Assert\NotBlank]
    public $id;

    /** @var string */
    #[Assert\NotBlank]
    public $group;

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
        $command->group = $member->getGroup()->getId()->getValue();
        return $command;
    }
}