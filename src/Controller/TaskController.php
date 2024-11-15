<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\TaskRequest;
use App\DTO\Response\TaskResponse;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Http\ResponseFormatter;
use App\Message\ProjectTasksUpdate;
use App\Services\ProjectService;
use App\Services\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/projects/{projectId}/tasks', requirements: ['projectId' => '\d+'])]
final class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskService $taskService,
        private readonly ProjectService $projectService,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @return array<Project|Task>
     */
    private function fetchProjectAndTask(User $user, int $projectId, int $taskId): array
    {
        $project = $this->projectService->getById($user, $projectId);
        if (null === $project) {
            throw $this->createNotFoundException('Project not found');
        }

        $task = $this->taskService->getById($user, $project, $taskId);
        if (null === $task) {
            throw $this->createNotFoundException('Task not found');
        }

        if ($user->getId() !== $project->getUser()->getId()) {
            throw $this->createNotFoundException('Task not found');
        }

        return [$project, $task];
    }

    #[Route(methods: [Request::METHOD_GET])]
    public function index(#[CurrentUser] User $user, int $projectId): JsonResponse
    {
        $project = $this->projectService->getById($user, $projectId);
        if (null === $project) {
            throw $this->createNotFoundException('Project not found');
        }

        $tasks = $this->taskService->getAll($user, $project);

        $response = array_map(fn (Task $task) => TaskResponse::fromEntity($task)->toArray(), $tasks);

        return ResponseFormatter::success($response);
    }

    #[Route(methods: [Request::METHOD_POST])]
    public function create(#[CurrentUser] User $user, TaskRequest $request, int $projectId): JsonResponse
    {
        $project = $this->projectService->getById($user, $projectId);
        if (null === $project) {
            throw $this->createNotFoundException('Project not found');
        }

        $task = $this->taskService->saveFromDto($request, $project);

        $this->messageBus->dispatch(
            new ProjectTasksUpdate(
                projectId: $project->getId(),
            )
        );

        return ResponseFormatter::success(TaskResponse::fromEntity($task)->toArray(), Response::HTTP_CREATED);
    }

    #[Route(path: '/{taskId}', requirements: ['taskId' => '\d+'], methods: [Request::METHOD_PUT])]
    public function update(#[CurrentUser] User $user, TaskRequest $request, int $projectId, int $taskId): JsonResponse
    {
        /** @var Task $task */
        /** @var Project $project */
        list($project, $task) = $this->fetchProjectAndTask($user, $projectId, $taskId);

        $task = $this->taskService->saveFromDto($request, $project, $task);

        $this->messageBus->dispatch(
            new ProjectTasksUpdate(
                projectId: $project->getId(),
            )
        );

        return ResponseFormatter::success(TaskResponse::fromEntity($task)->toArray());
    }

    #[Route(path: '/{taskId}', requirements: ['taskId' => '\d+'], methods: [Request::METHOD_DELETE])]
    public function delete(#[CurrentUser] User $user, int $projectId, int $taskId): JsonResponse
    {
        /** @var Task $task */
        /** @var Project $project */
        list($project, $task) = $this->fetchProjectAndTask($user, $projectId, $taskId);

        $this->taskService->delete($task);

        $this->messageBus->dispatch(
            new ProjectTasksUpdate(
                projectId: $project->getId(),
            )
        );

        return ResponseFormatter::success();
    }
}
