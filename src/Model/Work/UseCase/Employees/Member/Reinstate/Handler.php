<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Employees\Member\Reinstate;

use App\Model\Flusher;
use App\Model\Work\Entity\Employees\Member\Id;
use App\Model\Work\Entity\Employees\Member\MemberRepository;

class Handler
{
    /**
     * @param MemberRepository $members
     * @param Flusher $flusher
     */
    public function __construct(
        private MemberRepository $members,
        private Flusher $flusher
    ) {}

    /**
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $member = $this->members->get(new Id($command->id));

        $member->reinstate();

        $this->flusher->flush();
    }
}