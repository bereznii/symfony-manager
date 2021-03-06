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
    public const REFERENCE_USER = 'user_user';
    public const REFERENCE_ADMIN = 'user_admin';

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

        $savedAdmin = clone $user;
        $this->setReference(self::REFERENCE_ADMIN, $savedAdmin);

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

            if (random_int(0, 2)) { // 2/3 chance to be confirmed
                $user->confirmSignUp();

                if (!boolval(random_int(0, 2))) { // 1/3 chance to be blocked
                    $user->block();
                }
            }
            if (random_int(0, 1)) { // 1/2 chance to be admin
                $user->changeRole(Role::admin());
            }

            if ($i === 0) {
                $savedUser = clone $user;
                $this->setReference(self::REFERENCE_USER, $savedUser);
                $manager->persist($savedUser);
            } else {
                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}