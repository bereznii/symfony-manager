<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Flusher;
use App\Model\Work\Entity\Employees\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    /**
     * @param RoleRepository $members
     * @param Flusher $flusher
     */
    public function __construct(
        private RoleRepository $roles,
        private Flusher $flusher
    ) {}

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $guest = $this->createRole('Guest', []);
        $this->roles->add($guest);

        $manage = $this->createRole('Manager', [Permission::MANAGE_PROJECT_MEMBERS,]);
        $this->roles->add($manage);

        $manager->flush();
    }

    /**
     * @param string $name
     * @param array $permissions
     * @return Role
     */
    private function createRole(string $name, array $permissions): Role
    {
        return new Role(
            Id::next(),
            $name,
            $permissions
        );
    }
}