<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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
        return self::identityByUser($user);
    }

    /**
     * @param string $username
     * @return UserInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadUserByIdentifier(string $username): UserInterface
    {
        $user = $this->loadUser($username);
        return self::identityByUser($user);
    }

    /**
     * @param $username
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function loadUser($username): array
    {
        $chunks = explode(':', $username);

        //TODO: load for network
//        if (count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
//            return $user;
//        }

        if ($user = $this->users->findForAuthByEmail($username)) {
            return $user;
        }

        throw new UsernameNotFoundException('');
    }

    /**
     * @param array $user
     * @return UserIdentity
     */
    private static function identityByUser(array $user): UserIdentity
    {
        return new UserIdentity(
            $user['id'],
            $user['email'],
            $user['status'],
            $user['password_hash'],
            $user['role'],
        );
    }
}