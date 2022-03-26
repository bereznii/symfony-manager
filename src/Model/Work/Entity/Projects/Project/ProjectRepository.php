<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\EntityNotFoundException;
use App\Model\Work\Entity\Projects\Role\Id as RoleId;
use Doctrine\ORM\EntityManagerInterface;

class ProjectRepository
{
    /** @var ProjectRepository|\Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository */
    private $repo;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Project::class);
        $this->em = $em;
    }

    /**
     * @param RoleId $id
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasMembersWithRole(RoleId $id): bool
    {
        return $this->repo->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->innerJoin('p.memberships', 'ms')
                ->innerJoin('ms.roles', 'r')
                ->andWhere('r.id = :role')
                ->setParameter('role', $id->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param Id $id
     * @return Project
     */
    public function get(Id $id): Project
    {
        /** @var Project $project */
        if (!$project = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Project is not found.');
        }
        return $project;
    }

    /**
     * @param Project $project
     * @return void
     */
    public function add(Project $project): void
    {
        $this->em->persist($project);
    }

    /**
     * @param Project $project
     * @return void
     */
    public function remove(Project $project): void
    {
        $this->em->remove($project);
    }
}