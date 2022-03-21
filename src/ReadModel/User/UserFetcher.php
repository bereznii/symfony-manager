<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\ReadModel\User\Filter\Filter;
use App\ReadModel\User\View\DetailView;
use App\ReadModel\User\View\NetworkView;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserFetcher
{
    /**
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator,
    ) {}

    /**
     * @param string $token
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function existsByResetToken(string $token): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_users')
            ->where('reset_token_token = :token')
            ->setParameter('token', $token)
            ->executeQuery()->fetchFirstColumn() > 0;
    }

    /**
     * @param string $email
     * @return array|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function findForAuth(string $email): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'password_hash',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    /**
     * @param string $email
     * @return array|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function findForAuthByEmail(string $email): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'password_hash',
                'role',
                'status',
                'TRIM(CONCAT(name_first, \' \', name_last)) as name',
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    /**
     * @param string $network
     * @param string $identity
     * @return array|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function findForAuthByNetwork(string $network, string $identity): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'u.id',
                'CONCAT(n.network, \':\', n.identity) AS email',
                'u.password_hash',
                'u.role',
                'u.status',
                'TRIM(CONCAT(name_first, \' \', name_last)) as name',
            )
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameter('network', $network)
            ->setParameter('identity', $identity)
            ->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    /**
     * @param string $email
     * @return array|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function findByEmail(string $email): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    /**
     * @param string $token
     * @return array|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function findByRegisterConfirmToken(string $token): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('confirm_token = :confirm_token')
            ->setParameter('confirm_token', $token)
            ->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    /**
     * @param string $id
     * @return DetailView
     * @throws \Doctrine\DBAL\Exception
     */
    public function findDetail(string $id): DetailView
    {
        $user = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'created_at',
                'name_first as first_name',
                'name_last as last_name',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()->fetchAssociative();

        $detailView = new DetailView(...$user);

        $networks = $this->connection->createQueryBuilder()
            ->select('network', 'identity')
            ->from('user_user_networks')
            ->where('user_id = :id')
            ->setParameter('id', $id)
            ->executeQuery()->fetchAllAssociative();

        foreach ($networks as $network) {
            $detailView->networks[] = new NetworkView(...$network);
        }

        return $detailView;
    }

    /**
     * @param string $id
     * @return DetailView
     * @throws \Doctrine\DBAL\Exception
     */
    public function getDetail(string $id): DetailView
    {
        if (!$detail = $this->findDetail($id)) {
            throw new \LogicException('User is not found');
        }
        return $detail;
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
                'id',
                'created_at',
                'TRIM(CONCAT(name_first, \' \', name_last)) AS name',
                'email',
                'role',
                'status'
            )
            ->from('user_users');

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(name_first, \' \', name_last))', ':name'));
            $qb->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(email)', ':email'));
            $qb->setParameter('email', '%' . mb_strtolower($filter->email) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter('status', $filter->status);
        }

        if ($filter->role) {
            $qb->andWhere('role = :role');
            $qb->setParameter('role', $filter->role);
        }

        if (!\in_array($sort, ['created_at', 'name', 'email', 'role', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}