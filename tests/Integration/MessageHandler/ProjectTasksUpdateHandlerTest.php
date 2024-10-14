<?php

declare(strict_types=1);

namespace App\Tests\Integration\MessageHandler;

use App\Entity\Project;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Message\ProjectTasksUpdate;
use App\MessageHandler\ProjectTasksUpdateHandler;
use App\Repository\ProjectRepository;
use App\Services\ProjectService;
use App\Tests\Factory\ProjectFactory;
use App\Tests\Factory\TaskFactory;
use App\Tests\Integration\IntegrationTestCase;

final class ProjectTasksUpdateHandlerTest extends IntegrationTestCase
{
    private ProjectRepository $projectRepository;
    private ProjectTasksUpdateHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $logger = $this->getLogger();

        /** @var ProjectService $projectService */
        $projectService = self::getContainer()->get(ProjectService::class);

        /** @var ProjectRepository $projectRepository */
        $projectRepository = self::getContainer()->get(ProjectRepository::class);
        $this->projectRepository = $projectRepository;

        $this->handler = new ProjectTasksUpdateHandler(
            logger: $logger,
            projectService: $projectService,
            projectRepository: $projectRepository
        );
    }

    public function testHandlerNonExistProjectReturnCriticalLog(): void
    {
        $nonExistProjectId = 9999;
        $message = new ProjectTasksUpdate(projectId: $nonExistProjectId);

        ($this->handler)($message);

        $log = $this->testHandler->hasCritical([
            'message' => 'Project not found',
            'context' => ['projectId' => $nonExistProjectId],
        ]);

        $this->assertTrue($log);
    }

    public function testHandlerUpdateDurationReturnSuccess(): void
    {
        $project = ProjectFactory::createOne([
            'tasks' => TaskFactory::createMany(2, ['duration' => 3600]),
        ]);

        $message = new ProjectTasksUpdate(projectId: $project->getId());
        ($this->handler)($message);

        /** @var Project $result */
        $result = $this->projectRepository->find($project->getId());

        $this->assertSame(7200, $result->getDuration());
    }

    /**
     * @dataProvider projectStatusUpdateByTasksStatusDataProvider
     */
    public function testHandlerUpdateStatusReturnSuccess(ProjectFactory $factory, ProjectStatus $expectedStatus): void
    {
        $project = $factory->create();

        $message = new ProjectTasksUpdate(projectId: $project->getId());
        ($this->handler)($message);

        /** @var Project $result */
        $result = $this->projectRepository->find($project->getId());

        $this->assertSame($expectedStatus, $result->getStatus());
    }

    /**
     * @return iterable<array{ProjectFactory, ProjectStatus}>
     */
    public function projectStatusUpdateByTasksStatusDataProvider(): iterable
    {
        $projectPending = ProjectFactory::new([
            'tasks' => TaskFactory::new(['status' => TaskStatus::Pending])->many(2),
        ]);

        $projectProgress = ProjectFactory::new([
            'tasks' => TaskFactory::new()->sequence([
                ['status' => TaskStatus::Pending],
                ['status' => TaskStatus::InProgress],
                ['status' => TaskStatus::Completed],
            ]),
        ]);

        $projectComplete = ProjectFactory::new([
            'tasks' => TaskFactory::new(['status' => TaskStatus::Completed])->many(2),
        ]);

        yield 'project with all tasks in pending status' => [$projectPending, ProjectStatus::Pending];
        yield 'project have task in progress status' => [$projectProgress, ProjectStatus::InProgress];
        yield 'project with only completed task' => [$projectComplete, ProjectStatus::Completed];
    }
}
