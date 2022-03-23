<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Membership\Member\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Membership\Member\Email;
use App\Model\Work\Entity\Membership\Member\Id;
use App\Model\Work\Entity\Membership\Member\MemberRepository;
use App\Model\Work\Entity\Membership\Member\Name;

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

        $member->edit(
            new Name(
                $command->firstName,
                $command->lastName
            ),
            new Email($command->email)
        );

        $this->flusher->flush();
    }
}