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
    public const REFERENCE_GROUP = 'work_group';

    /**
     *
     */
    public function __construct(
        private GroupRepository $groups,
        private Flusher $flusher
    ) {}

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (['Staff', 'Writers', 'Moderators', 'Marketers', 'Publicists'] as $key => $groupTitle) {
            $group = new Group(
                Id::next(),
                $groupTitle
            );

            if ($key === 0) {
                $savedGroup = clone $group;
                $this->setReference(GroupFixtures::REFERENCE_GROUP, $savedGroup);
                $this->groups->add($savedGroup);
            } else {
                $this->groups->add($group);
            }

        }

        $this->flusher->flush();
    }
}