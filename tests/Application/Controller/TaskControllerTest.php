<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Enums\TaskStatus;
use App\Message\ProjectTasksUpdate;
use App\Tests\Application\ApplicationTestCase;
use App\Tests\Factory\ProjectFactory;
use App\Tests\Factory\TaskFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

final class TaskControllerTest extends ApplicationTestCase
{
    private const API_ROUTE_BASE = '/api/projects/{id}/tasks';

    protected const EXPECTED_RESPONSE_DATA = [
        'id' => '@integer@',
        'status' => '@string@',
        'title' => '@string@',
        'description' => '@string@',
        'duration' => '@integer@',
        'created_at' => '@datetime@',
        'updated_at' => '@datetime@',
    ];

    private User $user;
    private Project $project;

    public function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = UserFactory::findOrCreate(['apiKey' => self::API_KEY]);
        $this->user = $user;

        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $this->user]);
        $this->project = $project;
    }

    public function testTaskListReturnSuccess(): void
    {
        TaskFactory::createMany(5, ['project' => $this->project]);

        $route = strtr(self::API_ROUTE_BASE, ['{id}' => $this->project->getId()]);
        $this->jsonRequestWithAuth('GET', $route);

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

    public function testTaskCreateReturnSuccess(): void
    {
        $duration = 3600;
        $status = 'in-progress';
        $title = 'Task title';
        $description = 'Task description';

        $route = strtr(self::API_ROUTE_BASE, ['{id}' => $this->project->getId()]);
        $this->jsonRequestWithAuth('POST', $route, [
            'duration' => $duration,
            'status' => $status,
            'title' => $title,
            'description' => $description,
        ]);

        $expectedJson = [
            'success' => 0,
            'data' => [
                'id' => '@integer@',
                'status' => $status,
                'title' => $title,
                'description' => $description,
                'duration' => $duration,
                'created_at' => '@datetime@',
                'updated_at' => '@datetime@',
            ],
        ];

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern($expectedJson, $this->getJsonResponse());

        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');
        $this->assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();
        $this->assertInstanceOf(ProjectTasksUpdate::class, $message);
    }

    public function testTaskUpdateReturnSuccess(): void
    {
        $duration = 3600;
        $status = 'in-progress';
        $title = 'Task title';
        $description = 'Task description';

        $durationUpdate = 1800;
        $statusUpdate = 'completed';
        $titleUpdate = 'Task title update';
        $descriptionUpdate = 'Task description update';

        $project = TaskFactory::createOne([
            'project' => $this->project,
            'status' => TaskStatus::from($status),
            'duration' => $duration,
            'title' => $title,
            'description' => $description,
        ]);

        $route = strtr(self::API_ROUTE_BASE, ['{id}' => $this->project->getId()]);
        $this->jsonRequestWithAuth('PUT', $route.'/'.$project->getId(), [
            'status' => $statusUpdate,
            'duration' => $durationUpdate,
            'title' => $titleUpdate,
            'description' => $descriptionUpdate,
        ]);

        $expectedJson = [
            'success' => 0,
            'data' => [
                'id' => $project->getId(),
                'status' => $statusUpdate,
                'title' => $titleUpdate,
                'description' => $descriptionUpdate,
                'duration' => $durationUpdate,
                'created_at' => '@datetime@',
                'updated_at' => '@datetime@',
            ],
        ];

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern($expectedJson, $this->getJsonResponse());

        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');
        $this->assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();
        $this->assertInstanceOf(ProjectTasksUpdate::class, $message);
    }

    public function testTaskDeleteReturnSuccess(): void
    {
        $task = TaskFactory::createOne(['project' => $this->project]);

        $route = strtr(self::API_ROUTE_BASE, ['{id}' => $this->project->getId()]);
        $this->jsonRequestWithAuth('DELETE', $route.'/'.$task->getId());

        $expectedJson = [
            'success' => 0,
            'data' => null,
        ];

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern($expectedJson, $this->getJsonResponse());

        $this->assertNotNull($task->getDeletedAt());

        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');
        $this->assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();
        $this->assertInstanceOf(ProjectTasksUpdate::class, $message);
    }
}
