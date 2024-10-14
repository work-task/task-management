<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Tests\Factory\UserFactory;
use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class ApplicationTestCase extends WebTestCase
{
    use Factories;
    use ResetDatabase;
    use PHPMatcherAssertions;

    protected const API_KEY = 'qwerty';

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        UserFactory::createOne(['apiKey' => self::API_KEY]);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $server
     */
    protected function jsonRequestWithAuth(
        string $method,
        string $uri,
        array $parameters = [],
        array $server = [],
        bool $changeHistory = true,
    ): Crawler {
        $server['HTTP_X-Api-Key'] = self::API_KEY;

        return $this->client->jsonRequest(
            method: $method,
            uri: $uri,
            parameters: $parameters,
            server: $server,
            changeHistory: $changeHistory,
        );
    }

    /**
     * @return array<mixed>
     */
    protected function getJsonResponse(): array
    {
        /** @var string $content */
        $content = $this->client->getResponse()->getContent();

        return json_decode($content, true);
    }
}
