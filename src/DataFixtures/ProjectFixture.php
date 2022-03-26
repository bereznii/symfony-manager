<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Work\Entity\Employees\Member\Member;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Member $admin */
        $admin = $this->getReference(MemberFixtures::REFERENCE_ADMIN);

        /** @var Role $manage */
        $manage = $this->getReference(RoleFixtures::REFERENCE_MANAGER);

        $active = $this->createProject('Rozetka', 1);
        $active->addDepartment($development = DepartmentId::next(), 'Development');
        $active->addDepartment(DepartmentId::next(), 'Marketing');
        $active->addMember($admin, [$development], [$manage]);
        $manager->persist($active);

        $activeSecond = $this->createProject('Nova Poshta', 2);
        $manager->persist($activeSecond);

        $archived = $this->createArchivedProject('Fintech Band', 3);
        $manager->persist($archived);

        $manager->flush();
    }

    /**
     * @param string $name
     * @param int $sort
     * @return Project
     */
    private function createArchivedProject(string $name, int $sort): Project
    {
        $project = $this->createProject($name, $sort);
        $project->archive();
        return $project;
    }

    /**
     * @param string $name
     * @param int $sort
     * @return Project
     */
    private function createProject(string $name, int $sort): Project
    {
        return new Project(
            Id::next(),
            $name,
            $sort
        );
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            MemberFixtures::class,
            RoleFixtures::class,
        ];
    }
}
