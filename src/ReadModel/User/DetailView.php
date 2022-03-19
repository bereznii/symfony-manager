<?php

declare(strict_types=1);

namespace App\ReadModel\User;

class DetailView
{
    /**
     * @param string $id
     * @param string $created_at
     * @param string|null $email
     * @param string $role
     * @param string $status
     * @param string $first_name
     * @param string $last_name
     * @param array $networks
     */
    public function __construct(
        public string $id,
        public string $created_at,
        public ?string $email,
        public string $role,
        public string $status,
        public string $first_name,
        public string $last_name,
        public array $networks = [],
    ) {}
}