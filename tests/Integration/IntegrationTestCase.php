<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Faker\Factory;
use Faker\Generator;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class IntegrationTestCase extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected Generator $faker;
    protected TestHandler $testHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    protected function getLogger(): Logger
    {
        $this->testHandler = new TestHandler();

        return new Logger('test', [$this->testHandler]);
    }
}
