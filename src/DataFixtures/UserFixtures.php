<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $testUser = (new User())
            ->setUsername('test')
            ->setApiKey('qwerty');
        $manager->persist($testUser);

        $this->addReference('user-0', $testUser);

        for ($i = 1; $i < 10; $i++) {
            $user = (new User())
                ->setUsername($faker->unique()->userName())
                ->setApiKey($faker->randomLetter());

            $manager->persist($user);

            $this->addReference('user-' . $i, $user);
        }

        $manager->flush();
    }
}
