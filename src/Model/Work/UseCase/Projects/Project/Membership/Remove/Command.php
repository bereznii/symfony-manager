<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Remove;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @param string $project
     * @param string $member
     */
    public function __construct(
        #[Assert\NotBlank] public string $project,
        #[Assert\NotBlank] public string $member
    ) {}
}
