<?php

namespace App\DataFixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * @param PasswordHasher $hasher
     */
    public function __construct(
        private PasswordHasher $hasher,
        private UserPasswordHasherInterface $defaultPasswordHasher
    ) {}

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('password');

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Email('admin@app.test'),
            $hash,
            'token'
        );

        $user->confirmSignUp();

        $user->changeRole(Role::admin());

        $manager->persist($user);

        $manager->flush();
    }
}