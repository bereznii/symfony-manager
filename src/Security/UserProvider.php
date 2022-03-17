<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Webmozart\Assert\Assert;

class UserProvider implements UserProviderInterface
{
    /**
     * @param UserFetcher $users
     */
    public function __construct(
        private UserFetcher $users
    ) {}

    /**
     * @param $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return $class === UserIdentity::class;
    }

    /**
     * @param UserInterface $identity
     * @return UserInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function refreshUser(UserInterface $identity): UserInterface
    {
        if (!$identity instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . \get_class($identity));
        }

        $user = $this->loadUser($identity->getUserIdentifier());
        return self::identityByUser($user, $user['email']);
    }

    /**
     * @param string $identifier
     * @return UserInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->loadUser($identifier);
        return self::identityByUser($user, $identifier);
    }

    /**
     * @param $identifier
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function loadUser($identifier): array
    {
        $chunks = explode(':', $identifier);

        if (count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->users->findForAuthByEmail($identifier)) {
            return $user;
        }

        throw new UserNotFoundException('');
    }

    /**
     * @param array $user
     * @param string $identifier
     * @return UserIdentity
     */
    private static function identityByUser(array $user, string $identifier): UserIdentity
    {
        return new UserIdentity(
            id: $user['id'],
            email: $identifier,
            status: $user['status'],
            password: $user['password_hash'],
            role: $user['role'],
        );
    }
}