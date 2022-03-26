<?php

namespace App\DataFixtures;

use App\Model\Flusher;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Employees\Group\Group;
use App\Model\Work\Entity\Employees\Member\Email;
use App\Model\Work\Entity\Employees\Member\Id;
use App\Model\Work\Entity\Employees\Member\Member;
use App\Model\Work\Entity\Employees\Member\MemberRepository;
use App\Model\Work\Entity\Employees\Member\Name;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_ADMIN = 'work_member_admin';

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

        /** @var Group $staff */
        $staff = $this->getReference(GroupFixtures::REFERENCE_STAFF);
        /** @var Group $customers */
        $customers = $this->getReference(GroupFixtures::REFERENCE_CUSTOMERS);

        $adminMember = new Member(
            new Id($savedAdmin->getId()->getValue()),
            $staff,
            new Name(
                $savedAdmin->getName()->getFirst(),
                $savedAdmin->getName()->getLast()
            ),
            new Email('member.' . $savedAdmin->getEmail()->getValue())
        );
        $manager->persist($adminMember);
        $this->setReference(self::REFERENCE_ADMIN, $adminMember);

        $member = new Member(
            new Id($savedUser->getId()->getValue()),
            $customers,
            new Name(
                $savedUser->getName()->getFirst(),
                $savedUser->getName()->getLast()
            ),
            new Email('member.' . $savedUser->getEmail()->getValue())
        );
        $manager->persist($member);

        $manager->flush();
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