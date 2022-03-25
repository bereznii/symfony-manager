<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class RoleFetcher
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
                'r.id',
                'r.name',
                'r.permissions'
            )
            ->from('work_projects_roles', 'r')
            ->orderBy('name')
            ->executeQuery()->fetchAllAssociative();

        return array_map(static function (array $role) {
            return array_replace($role, [
                'permissions' => json_decode($role['permissions'], true)
            ]);
        }, $res);
    }
}

