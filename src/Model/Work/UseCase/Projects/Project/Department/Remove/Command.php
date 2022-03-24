<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Department\Remove;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @param string $project
     * @param string $id
     */
    public function __construct(
        #[Assert\NotBlank] public string $project,
        #[Assert\NotBlank] public string $id,
    ) {}
}
