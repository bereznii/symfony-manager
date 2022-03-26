<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Add;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Employees\Member\MemberRepository;
use App\Model\Work\Entity\Employees\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Role\Id as RoleId;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

class Handler
{
    /**
     * @param ProjectRepository $projects
     * @param MemberRepository $members
     * @param RoleRepository $roles
     * @param Flusher $flusher
     */
    public function __construct(
        private ProjectRepository $projects,
        private MemberRepository $members,
        private RoleRepository $roles,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->project));
        $member = $this->members->get(new MemberId($command->member));

        $departments = array_map(static function (string $id): DepartmentId {
            return new DepartmentId($id);
        }, $command->departments);

        $roles = array_map(function (string $id): Role {
            return $this->roles->get(new RoleId($id));
        }, $command->roles);

        $project->addMember($member, $departments, $roles);

        $this->flusher->flush();
    }
}

