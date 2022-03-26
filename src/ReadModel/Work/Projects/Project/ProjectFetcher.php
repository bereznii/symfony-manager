<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project;

use App\ReadModel\Work\Projects\Project\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProjectFetcher
{
    /**
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator
    ) {}

    /**
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNextSort(): int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('MAX(p.sort) AS m')
            ->from('work_projects_projects', 'p')
            ->executeQuery()->fetchOne() + 1;
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
                'p.id',
                'p.name',
                'p.status'
            )
            ->from('work_projects_projects', 'p');

        if ($filter->member) {
            $qb->andWhere('EXISTS (
                SELECT ms.member_id FROM work_projects_project_memberships ms WHERE ms.project_id = p.id AND ms.member_id = :member
            )');
            $qb->setParameter('member', $filter->member);
        }

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like('p.name', ':name'));
            $qb->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('p.status = :status');
            $qb->setParameter('status', $filter->status);
        }

        if (!\in_array($sort, ['name', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}