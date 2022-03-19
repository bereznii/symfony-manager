<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_user_networks')]
#[ORM\UniqueConstraint(columns: ['network', 'identity'])]
class Network
{
    /** @var string */
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    private string $id;

    /** @var User */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'networks')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    /** @var string */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private string $network;

    /** @var string */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private string $identity;

    /**
     * @param User $user
     * @param string $network
     * @param string $identity
     */
    public function __construct(User $user, string $network, string $identity)
    {
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;

        $this->id = Uuid::uuid4()->toString();
    }

    /**
     * @param string $network
     * @return bool
     */
    public function isForNetwork(string $network): bool
    {
        return $this->network === $network;
    }

    /**
     * @param string $network
     * @param string $identity
     * @return bool
     */
    public function isFor(string $network, string $identity): bool
    {
        return $this->network === $network && $this->identity === $identity;
    }

    /**
     * @return string
     */
    public function getNetwork(): string
    {
        return $this->network;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }
}