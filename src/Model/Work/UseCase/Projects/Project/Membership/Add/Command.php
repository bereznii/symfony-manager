<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Add;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /** @var string */
    #[Assert\NotBlank]
    public $project;

    /** @var mixed */
    #[Assert\NotBlank]
    public $member;

    /** @var mixed */
    #[Assert\NotBlank]
    public $departments;

    /** @var mixed */
    #[Assert\NotBlank]
    public $roles;

    /**
     * @param string $project
     */
    public function __construct(string $project)
    {
        $this->project = $project;
    }
}
