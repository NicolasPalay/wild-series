<?php

namespace App\DataFixtures;

use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        $user = new User();
        $user->setEmail($faker->email());
        $user->setRoles(['ROLE_CONTRIBUTOR']);
        $plaintextPassword = '123456';

        // hash the password (based on the security.yaml config for the $user class)

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail($faker->email());
        $user2->setRoles(['ROLE_ADMIN']);
        $plaintextPassword = '123456';

        // hash the password (based on the security.yaml config for the $user class)

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user2,
            $plaintextPassword
        );
        $user2->setPassword($hashedPassword);
        $manager->persist($user2);


        $manager->flush();
    }
}
