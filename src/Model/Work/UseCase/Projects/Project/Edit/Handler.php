<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
    /**
     * @param ProjectRepository $projects
     * @param Flusher $flusher
     */
    public function __construct(
        private ProjectRepository $projects,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->id));

        $project->edit(
            $command->name,
            $command->sort
        );

        $this->flusher->flush();
    }
}