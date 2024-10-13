<?php

namespace App\Tests\Factory;

use App\Entity\Project;
use DateTimeImmutable;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Project>
 */
final class ProjectFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Project::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'user' => UserFactory::new(),
            'title' => self::faker()->sentence(),
            'description' => self::faker()->paragraph(),
            'updatedAt' => DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'createdAt' => DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }
}
