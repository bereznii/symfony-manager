<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Membership;

use Doctrine\DBAL\Connection;

class GroupFetcher
{
    /**
     * @param Connection $connection
     */
    public function __construct(
        private Connection $connection
    ) {}

    /**
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function all(): array
    {
        $res = $this->connection->createQueryBuilder()
            ->select(
                'g.id',
                'g.name'
            )
            ->from('work_members_groups', 'g')
            ->orderBy('name')
            ->executeQuery()->fetchAllAssociative();

        return $res;
    }
}