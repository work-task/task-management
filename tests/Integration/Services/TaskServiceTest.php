<?php

declare(strict_types=1);

namespace App\Tests\Integration\Services;

use App\DTO\Request\TaskRequest;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Enums\TaskStatus;
use App\Repository\TaskRepository;
use App\Services\TaskService;
use App\Tests\Factory\ProjectFactory;
use App\Tests\Factory\TaskFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Integration\IntegrationTestCase;

final class TaskServiceTest extends IntegrationTestCase
{
    private TaskService $taskService;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var TaskService $taskService */
        $taskService = self::getContainer()->get(TaskService::class);
        $this->taskService = $taskService;

        /** @var TaskRepository $taskRepository */
        $taskRepository = self::getContainer()->get(TaskRepository::class);
        $this->taskRepository = $taskRepository;
    }

    public function testFindByUserAndIdReturnResult(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();
        /** @var Task $task */
        $task = TaskFactory::createOne(['project' => $project])->_real();

        $result = $this->taskService->getById($user, $project, $task->getId());

        $this->assertInstanceOf(Task::class, $result);
    }

    public function testFindByUserAndIdReturnNull(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();

        $result = $this->taskService->getById($user, $project, 9999);

        $this->assertNull($result);
    }

    public function testFindByUserAndIdAndDeletedReturnNull(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();
        /** @var Task $task */
        $task = TaskFactory::createOne(['project' => $project, 'deletedAt' => new \DateTimeImmutable()])->_real();

        $result = $this->taskService->getById($user, $project, $task->getId());

        $this->assertNull($result);
    }

    public function testSaveFromDbReturnSuccess(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();

        $duration = 3600;
        $status = 'in-progress';
        $title = 'save-task-from-dto';
        $description = 'task description';

        $request = new TaskRequest(
            status: $status,
            title: $title,
            description: $description,
            duration: $duration,
        );

        $task = $this->taskService->saveFromDto($request, $project);

        $result = $this->taskRepository->findOneBy(['title' => $title]);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertNotNull($task->getId());

        $this->assertInstanceOf(Task::class, $result);
        $this->assertSame($task->getId(), $result->getId());
    }

    public function testSaveFromDbWithAlreadyPersistProjectReturnSuccess(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();
        /** @var Task $task */
        $task = TaskFactory::createOne(['project' => $project])->_real();

        $request = new TaskRequest(
            status: $this->faker->randomElement(TaskStatus::cases())->value,
            title: $this->faker->sentence(),
            description: $this->faker->paragraph(),
            duration: $this->faker->randomDigit(),
        );

        $task = $this->taskService->saveFromDto($request, $project, $task);

        $result = $this->taskRepository->find($task->getId());

        $this->assertInstanceOf(Task::class, $task);
        $this->assertInstanceOf(Task::class, $result);

        $this->assertSame($task->getId(), $result->getId());
    }

    public function testDeleteTaskReturnSuccess(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();
        /** @var Task $task */
        $task = TaskFactory::createOne(['project' => $project])->_real();

        $this->assertNull($task->getDeletedAt());

        $this->taskService->delete($task);

        $this->assertNotNull($task->getDeletedAt());
    }
}
