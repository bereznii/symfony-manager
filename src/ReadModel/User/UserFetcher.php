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
}