<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use App\Enums\ProjectStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; ++$i) {
            $reference = sprintf('user-%d', rand(0, 5));

            $user = $this->getReference($reference, User::class);

            $project = (new Project())
                ->setUser($user)
                ->setTitle($faker->sentence())
                ->setDescription($faker->paragraph())
                ->setStatus($faker->randomElement(ProjectStatus::cases()))
                ->setDuration($faker->randomNumber(5));

            $manager->persist($project);

            $this->addReference('project-'.$i, $project);
        }

        $manager->flush();
    }

    /**
     * @return class-string[]
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
