<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
  private \Faker\Generator $faker;

  public function __construct(
    private UserPasswordHasherInterface $passwordHasher,
  ) {
    $this->faker = \Faker\Factory::create('nl_BE');
  }

  public function load(ObjectManager $manager): void
  {
    // Create default user
    $user = new User();
    $user->setFirstName('Tim');
    $user->setLastName('Timmers');
    $user->setRoles(['ROLE_USER, ROLE_ADMIN']);
    $user->setEmail('tim@example.com');
    $user->setEmailVerified(true);
    $user->setPassword($this->passwordHasher->hashPassword($user, 'artevelde'));

    $manager->persist($user);
    $manager->flush();

    // Create random users
    for ($i = 0; $i < 25; $i++) {
      // Set first name, last name and email
      $firstName = $this->faker->firstName();
      $lastName = $this->faker->lastName();

      $firstNameForEmail = strtolower(str_replace(' ', '', iconv('UTF-8', 'ASCII//TRANSLIT', $firstName)));
      $lastNameForEmail = strtolower(str_replace(' ', '', iconv('UTF-8', 'ASCII//TRANSLIT', $lastName)));
      $email = $firstNameForEmail . '.' . $lastNameForEmail . '@example.be';

      // Check if the user already exists
      $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);

      if ($user) {
        continue;
      }

      // Create user
      $user = new User();
      $user->setFirstName($firstName);
      $user->setLastName($lastName);
      $user->setEmail($email);
      $user->setPassword($this->passwordHasher->hashPassword($user, 'artevelde'));
      $user->setRoles(['ROLE_USER']);
      $manager->persist($user);
    }
    $manager->flush();
  }
}
