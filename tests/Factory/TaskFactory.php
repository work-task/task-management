<?php

namespace App\Tests\Factory;

use App\Entity\Task;
use App\Enums\TaskStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Task::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'project' => ProjectFactory::new(),
            'title' => self::faker()->sentence(),
            'description' => self::faker()->text(),
            'duration' => self::faker()->randomNumber(),
            'status' => self::faker()->randomElement(TaskStatus::cases()),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }
}
