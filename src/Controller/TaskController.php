<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\TaskRequest;
use App\DTO\Response\TaskResponse;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Http\ResponseFormatter;
use App\Services\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/projects/{project}/tasks')]
final class TaskController extends AbstractController
{
    public function __construct(private readonly TaskService $taskService)
    {
    }

    #[Route(methods: [Request::METHOD_GET])]
    public function index(Project $project): JsonResponse
    {
        $tasks = $this->taskService->getAll($project);

        $response = array_map(fn (Task $task) => TaskResponse::fromEntity($task)->toArray(), $tasks);

        return ResponseFormatter::success($response);
    }

    #[Route(methods: [Request::METHOD_POST])]
    public function create(TaskRequest $request, Project $project): JsonResponse
    {
        $task = $this->taskService->saveFromDto($request, $project);

        return ResponseFormatter::success(TaskResponse::fromEntity($task)->toArray());
    }

    #[Route(path: '/{task}', requirements: ['task' => '\d+'], methods: [Request::METHOD_PUT])]
    public function update(TaskRequest $request, Project $project, Task $task, #[CurrentUser] User $user): JsonResponse
    {
        if ($user->getId() !== $project->getUser()->getId()) {
            throw $this->createNotFoundException('Task not found');
        }

        $task = $this->taskService->saveFromDto($request, $project, $task);

        return ResponseFormatter::success(TaskResponse::fromEntity($task)->toArray());
    }

    #[Route(path: '/{task}', requirements: ['task' => '\d+'], methods: [Request::METHOD_DELETE])]
    public function delete(Project $project, Task $task, #[CurrentUser] User $user): JsonResponse
    {
        if ($user->getId() !== $project->getUser()->getId()) {
            throw $this->createNotFoundException('Task not found');
        }

        $this->taskService->delete($task);

        return ResponseFormatter::success();
    }
}
