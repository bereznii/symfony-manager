<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->em->flush();
    }
}