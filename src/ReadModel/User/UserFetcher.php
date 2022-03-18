<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;

class UserFetcher
{
    /**
     * @param Connection $connection
     */
    public function __construct(
        private Connection $connection
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
                'status'
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
                'u.status'
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
}