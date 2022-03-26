<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Employees\Member;

use App\Model\EntityNotFoundException;
use App\Model\Work\Entity\Employees\Group\Id as GroupId;
use Doctrine\ORM\EntityManagerInterface;

class MemberRepository
{
    /** @var MemberRepository|\Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository */
    private $repo;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Member::class);
        $this->em = $em;
    }

    /**
     * @param Id $id
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function has(Id $id): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param GroupId $id
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasByGroup(GroupId $id): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.group = :group')
            ->setParameter('group', $id->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param Id $id
     * @return Member
     */
    public function get(Id $id): Member
    {
        /** @var Member $member */
        if (!$member = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Member is not found.');
        }
        return $member;
    }

    /**
     * @param Member $member
     * @return void
     */
    public function add(Member $member): void
    {
        $this->em->persist($member);
    }
}