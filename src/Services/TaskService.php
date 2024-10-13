<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Request\TaskRequest;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Enums\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

final class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getById(User $user, Project $project, int $id): ?Task
    {
        return $this->taskRepository->findByUserProjectAndId($user, $project, $id);
    }

    /**
     * @return array<int, Task>
     */
    public function getAll(User $user, Project $project): array
    {
        return $this->taskRepository->findByProjectAndUser($user, $project);
    }

    public function save(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function saveFromDto(TaskRequest $request, Project $project, ?Task $task = null): Task
    {
        $status = TaskStatus::from($request->getStatus());

        $task ??= new Task();
        $task->setProject($project);
        $task->setStatus($status);
        $task->setTitle($request->getTitle());
        $task->setDescription($request->getDescription());
        $task->setDuration($request->getDuration());

        $this->save($task);

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->setDeletedAt(new \DateTimeImmutable());

        $this->save($task);
    }
}
