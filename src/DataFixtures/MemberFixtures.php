<?php

namespace App\DataFixtures;

use App\Model\Flusher;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Membership\Group\Group;
use App\Model\Work\Entity\Membership\Member\Email;
use App\Model\Work\Entity\Membership\Member\Id;
use App\Model\Work\Entity\Membership\Member\Member;
use App\Model\Work\Entity\Membership\Member\MemberRepository;
use App\Model\Work\Entity\Membership\Member\Name;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixtures extends Fixture implements DependentFixtureInterface
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
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $savedAdmin */
        $savedAdmin = $this->getReference(UserFixtures::REFERENCE_ADMIN);
        /** @var User $savedUser */
        $savedUser = $this->getReference(UserFixtures::REFERENCE_USER);
        /** @var Group $savedGroup */
        $savedGroup = $this->getReference(GroupFixtures::REFERENCE_GROUP);

        $member = new Member(
            new Id($savedAdmin->getId()->getValue()),
            $savedGroup,
            new Name(
                $savedAdmin->getName()->getFirst(),
                $savedAdmin->getName()->getLast()
            ),
            new Email('member.' . $savedAdmin->getEmail()->getValue())
        );
        $this->members->add($member);

        $member = new Member(
            new Id($savedUser->getId()->getValue()),
            $savedGroup,
            new Name(
                $savedUser->getName()->getFirst(),
                $savedUser->getName()->getLast()
            ),
            new Email('member.' . $savedUser->getEmail()->getValue())
        );
        $this->members->add($member);

        $this->flusher->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            GroupFixtures::class,
        ];
    }
}