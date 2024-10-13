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
use Symfony\Bundle\SecurityBundle\Security;

final class TaskService
{
    private User $user;

    public function __construct(
        private readonly Security $security,
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        /** @var User $user */
        $user = $this->security->getUser();

        $this->user = $user;
    }

    public function getAll(Project $project): array
    {
        return $this->taskRepository->findByProjectAndUser($project, $this->user);
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
