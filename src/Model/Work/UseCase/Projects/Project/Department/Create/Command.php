<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Department\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /** @var string */
    #[Assert\NotBlank]
    public $project;

    /** @var string */
    #[Assert\NotBlank]
    public $name;

    /**
     * @param string $project
     */
    public function __construct(string $project)
    {
        $this->project = $project;
    }
}