<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Employees\Member;

use App\Model\Work\Entity\Employees\Member\Member;
use App\Model\Work\Entity\Employees\Member\Status;
use App\ReadModel\Work\Employees\Member\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class MemberFetcher
{
    /** @var Connection */
    private $connection;

    /** @var PaginatorInterface */
    private $paginator;

    /** @var \Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository */
    private $repository;

    /**
     * @param Connection $connection
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Member::class);
        $this->paginator = $paginator;
    }

    /**
     * @param string $id
     * @return Member|null
     */
    public function find(string $id): ?Member
    {
        return $this->repository->find($id);
    }

    /**
     * @param Filter $filter
     * @param int $page
     * @param int $size
     * @param string $sort
     * @param string $direction
     * @return PaginationInterface
     */
    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS name',
                'm.email',
                'g.name as group',
                'm.status'
            )
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_members_groups', 'g', 'm.group_id = g.id');

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(m.name_first, \' \', m.name_last))', ':name'));
            $qb->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(m.email)', ':email'));
            $qb->setParameter('email', '%' . mb_strtolower($filter->email) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('m.status = :status');
            $qb->setParameter('status', $filter->status);
        }

        if ($filter->group) {
            $qb->andWhere('m.group_id = :group');
            $qb->setParameter('group', $filter->group);
        }

        if (!\in_array($sort, ['name', 'email', 'group', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy('"' . $sort . '"', $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @param string $id
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function exists(string $id)
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (id)')
            ->from('work_members_members')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()->fetchOne() > 0;
    }

    public function activeGroupedList(): array
    {
        $res = $this->connection->createQueryBuilder()
            ->select([
                'm.id',
                'CONCAT(m.name_first, \' \', m.name_last) AS name',
                'g.name AS group'
            ])
            ->from('work_members_members', 'm')
            ->leftJoin('m', 'work_members_groups', 'g', 'g.id = m.group_id')
            ->andWhere('m.status = :status')
            ->setParameter('status', Status::ACTIVE)
            ->orderBy('g.name')->addOrderBy('name')
            ->executeQuery()->fetchAllAssociative();

        return $res;
    }
}