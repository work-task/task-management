<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Enums\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 500; ++$i) {
            $reference = sprintf('project-%d', rand(0, 10));

            $project = $this->getReference($reference, Project::class);

            $task = (new Task())
                ->setProject($project)
                ->setTitle($faker->sentence())
                ->setDescription($faker->paragraph())
                ->setStatus($faker->randomElement(TaskStatus::cases()))
                ->setDuration($faker->randomNumber(3));

            $manager->persist($task);
        }

        $manager->flush();
    }

    /**
     * @return class-string[]
     */
    public function getDependencies(): array
    {
        return [ProjectFixtures::class];
    }
}
