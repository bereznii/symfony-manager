<?php

namespace App\DataFixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private const LIMIT_USERS = 25;

    /**
     * @param PasswordHasher $hasher
     * @param UserPasswordHasherInterface $defaultPasswordHasher
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
        $faker = \Faker\Factory::create();
        $hash = $this->hasher->hash('password');

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Email('admin@app.test'),
            $hash,
            'token',
            new Name('John','Doe')
        );
        $user->confirmSignUp();
        $user->changeRole(Role::admin());
        $manager->persist($user);

        for ($i = 0; $i < self::LIMIT_USERS; $i++) {
            $user = User::signUpByEmail(
                id: Id::next(),
                created_at: new \DateTimeImmutable(),
                email: new Email($faker->email()),
                hash: $hash,
                confirmToken: Uuid::uuid4()->toString(),
                name: new Name(
                    $faker->firstName(),
                    $faker->lastName()
                )
            );

            $user->confirmSignUp();
            $manager->persist($user);
        }

        $manager->flush();
    }
}