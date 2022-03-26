<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Employees\Member\MemberRepository;
use App\Model\Work\Entity\Employees\Member\Id as MemberId;

class Handler
{
    /**
     * @param ProjectRepository $projects
     * @param MemberRepository $members
     * @param Flusher $flusher
     */
    public function __construct(
        private ProjectRepository $projects,
        private MemberRepository $members,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->project));
        $member = $this->members->get(new MemberId($command->member));

        $project->removeMember($member->getId());

        $this->flusher->flush();
    }
}

