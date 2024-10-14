<?php

namespace App\Tests\Application\Controller;

use App\Tests\Application\ApplicationTestCase;
use App\Tests\Factory\ProjectFactory;
use App\Tests\Factory\UserFactory;

final class ProjectControllerTest extends ApplicationTestCase
{
    protected const API_ROUTE_BASE = '/api/projects';
    protected const EXPECTED_RESPONSE_DATA = [
        'id' => '@integer@',
        'status' => '@string@',
        'title' => '@string@',
        'description' => '@string@',
        'duration' => '@integer@',
        'created_at' => '@datetime@',
        'updated_at' => '@datetime@',
    ];

    public function testProjectListReturnSuccess(): void
    {
        $user = UserFactory::findOrCreate(['apiKey' => self::API_KEY]);
        ProjectFactory::createMany(5, ['user' => $user]);

        $this->jsonRequestWithAuth('GET', self::API_ROUTE_BASE);

        $expectedJson = [
            'success' => 0,
            'data' => [
                self::EXPECTED_RESPONSE_DATA,
                '@...@',
            ],
        ];

        $response = $this->getJsonResponse();

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern($expectedJson, $response);
        $this->assertCount(5, $response['data']);
    }

    public function testProjectCreateReturnSuccess(): void
    {
        $title = 'Project title';
        $description = 'Project description';

        $this->jsonRequestWithAuth('POST', self::API_ROUTE_BASE, [
            'title' => $title,
            'description' => $description,
        ]);

        $expectedJson = [
            'success' => 0,
            'data' => [
                'id' => '@integer@',
                'status' => 'pending',
                'title' => $title,
                'description' => $description,
                'duration' => 0,
                'created_at' => '@datetime@',
                'updated_at' => '@datetime@',
            ],
        ];

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern($expectedJson, $this->getJsonResponse());
    }

    public function testProjectUpdateReturnSuccess(): void
    {
        $title = 'Project title';
        $description = 'Project description';

        $titleUpdate = 'Project title update';
        $descriptionUpdate = 'Project description update';

        $user = UserFactory::findOrCreate(['apiKey' => self::API_KEY]);
        $project = ProjectFactory::createOne(['user' => $user, 'title' => $title, 'description' => $description]);

        $this->jsonRequestWithAuth('PUT', self::API_ROUTE_BASE.'/'.$project->getId(), [
            'title' => $titleUpdate,
            'description' => $descriptionUpdate,
        ]);

        $expectedJson = [
            'success' => 0,
            'data' => [
                'id' => $project->getId(),
                'status' => 'pending',
                'title' => $titleUpdate,
                'description' => $descriptionUpdate,
                'duration' => 0,
                'created_at' => '@datetime@',
                'updated_at' => '@datetime@',
            ],
        ];

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern($expectedJson, $this->getJsonResponse());
    }

    public function testProjectDeleteReturnSuccess(): void
    {
        $user = UserFactory::findOrCreate(['apiKey' => self::API_KEY]);
        $project = ProjectFactory::createOne(['user' => $user]);

        $this->jsonRequestWithAuth('DELETE', self::API_ROUTE_BASE.'/'.$project->getId());

        $expectedJson = [
            'success' => 0,
            'data' => null,
        ];

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern($expectedJson, $this->getJsonResponse());

        $this->assertNotNull($project->getDeletedAt());
    }
}
