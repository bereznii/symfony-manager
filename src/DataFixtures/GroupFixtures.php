<?php

namespace App\DataFixtures;

use App\Model\Flusher;
use App\Model\Work\Entity\Employees\Group\Group;
use App\Model\Work\Entity\Employees\Group\GroupRepository;
use App\Model\Work\Entity\Employees\Group\Id;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public const REFERENCE_STAFF = 'work_member_group_staff';
    public const REFERENCE_CUSTOMERS = 'work_member_group_customers';

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $staff = new Group(
            Id::next(),
            'Our Staff'
        );
        $manager->persist($staff);
        $this->setReference(self::REFERENCE_STAFF, $staff);

        $customers = new Group(
            Id::next(),
            'Customers'
        );

        $manager->persist($customers);
        $this->setReference(self::REFERENCE_CUSTOMERS, $customers);

        $manager->flush();
    }
}