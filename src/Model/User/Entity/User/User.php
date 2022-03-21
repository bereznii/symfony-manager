<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use DomainException;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'user_users')]
#[ORM\UniqueConstraint(columns: ["email"])]
#[ORM\UniqueConstraint(columns: ["reset_token_token"])]
class User
{
    public const STATUS_NEW = 'new';
    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /** @var Id */
    #[ORM\Column(type: 'user_user_id',)]
    #[ORM\Id]
    private Id $id;

    /** @var DateTimeImmutable */
    #[ORM\Column(type: 'datetime_immutable',)]
    private DateTimeImmutable $created_at;

    /** @var Email|null */
    #[ORM\Column(type: 'user_user_email', nullable: true,)]
    private $email;

    /** @var string|null */
    #[ORM\Column(name: 'password_hash', type: 'string', nullable: true,)]
    private ?string $passwordHash;

    /** @var string|null */
    #[ORM\Column(name: 'confirm_token', type: 'string', nullable: true,)]
    private ?string $confirmToken;

    /** @var Name */
    #[ORM\Embedded(class: Name::class)]
    private Name $name;

    /** @var Email|null */
    #[ORM\Column(name: 'new_email', type: 'user_user_email', nullable: true,)]
    private $newEmail;

    /** @var string|null */
    #[ORM\Column(name: 'new_email_token', type: 'string', nullable: true,)]
    private $newEmailToken;

    /** @var ResetToken|null */
    #[ORM\Embedded(class: ResetToken::class, columnPrefix: 'reset_token_',)]
    private $resetToken;

    /** @var string|null */
    #[ORM\Column(type: 'string', length: 16,)]
    private ?string $status;

    /** @var Role */
    #[ORM\Column(type: 'user_user_role', length: 16,)]
    private Role $role;

    /** @var PersistentCollection */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'Network', cascade: ['persist'], orphanRemoval: true)]
    private $networks;

    /**
     * @param Id $id
     * @param DateTimeImmutable $created_at
     * @param Name $name
     */
    private function __construct(Id $id, \DateTimeImmutable $created_at, Name $name)
    {
        $this->id = $id;
        $this->created_at = $created_at;
        $this->role = Role::user();
        $this->name = $name;
        $this->networks = new ArrayCollection();
    }

    /**
     * @param Id $id
     * @param DateTimeImmutable $date
     * @param Name $name
     * @param Email $email
     * @param string $hash
     * @return static
     */
    public static function create(Id $id, \DateTimeImmutable $created_at, Name $name, Email $email, string $hash): self
    {
        $user = new self($id, $created_at, $name);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    /**
     * @param Id $id
     * @param DateTimeImmutable $created_at
     * @param Email $email
     * @param string $hash
     * @param string $confirmToken
     * @param Name $name
     * @return static
     */
    public static function signUpByEmail(Id $id, DateTimeImmutable $created_at, Email $email, string $hash, string $confirmToken, Name $name): self
    {
        $user = new self($id, $created_at, $name);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $confirmToken;
        $user->status = self::STATUS_WAIT;
        return $user;
    }

    /**
     * @param Id $id
     * @param DateTimeImmutable $created_at
     * @param string $network
     * @param string $identity
     * @param Name $name
     * @return static
     */
    public static function signUpByNetwork(Id $id, \DateTimeImmutable $created_at, string $network, string $identity, Name $name): self
    {
        $user = new self($id, $created_at, $name);
        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    /**
     * @param string $network
     * @param string $identity
     * @return void
     */
    public function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }

    /**
     * @return void
     */
    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    /**
     * @param ResetToken $token
     * @param DateTimeImmutable $date
     * @return void
     */
    public function requestPasswordReset(ResetToken $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if (!$this->email) {
            throw new \DomainException('Email is not specified.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requested.');
        }
        $this->resetToken = $token;
    }

    /**
     * @param DateTimeImmutable $date
     * @param string $hash
     * @return void
     */
    public function passwordReset(DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }
        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }
        $this->passwordHash = $hash;
        $this->resetToken = null;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return string|null
     */
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return Email|null
     */
    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    /**
     * @return string|null
     */
    public function getNewEmailToken(): ?string
    {
        return $this->newEmailToken;
    }

    /**
     * @return ResetToken|null
     */
    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    /**
     * @return void
     */
    #[ORM\PostLoad]
    public function checkEmbeds(): void
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }

    /**
     * @param Role $role
     * @return void
     */
    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already set.');
        }
        $this->role = $role;
    }

    /**
     * @return void
     */
    public function activate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('User is already active.');
        }
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return void
     */
    public function block(): void
    {
        if ($this->isBlocked()) {
            throw new \DomainException('User is already blocked.');
        }
        $this->status = self::STATUS_BLOCKED;
    }

    /**
     * @param Email $email
     * @param string $token
     * @return void
     */
    public function requestEmailChanging(Email $email, string $token): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if ($this->email && $this->email->isEqual($email)) {
            throw new \DomainException('Email is already same.');
        }
        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    /**
     * @param string $token
     * @return void
     */
    public function confirmEmailChanging(string $token): void
    {
        if (!$this->newEmailToken) {
            throw new \DomainException('Changing is not requested.');
        }
        if ($this->newEmailToken !== $token) {
            throw new \DomainException('Incorrect changing token.');
        }
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    /**
     * @param Name $name
     * @return void
     */
    public function changeName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @param Email $email
     * @param Name $name
     * @return void
     */
    public function edit(Email $email, Name $name): void
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @param string $network
     * @param string $identity
     * @return void
     */
    public function detachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isFor($network, $identity)) {
                if (empty($this->passwordHash) || (!$this->email && $this->networks->count() === 1)) {
                    throw new \DomainException('Unable to detach the last identity.');
                }
                $this->networks->removeElement($existing);
                return;
            }
        }
        throw new \DomainException('Network is not attached.');
    }
}