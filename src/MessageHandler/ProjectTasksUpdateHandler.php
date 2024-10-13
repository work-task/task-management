<?php

namespace App\MessageHandler;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Message\ProjectTasksUpdate;
use App\Repository\ProjectRepository;
use App\Services\ProjectService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProjectTasksUpdateHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ProjectService $projectService,
        private readonly ProjectRepository $projectRepository,
    ) {
    }

    public function __invoke(ProjectTasksUpdate $message): void
    {
        $project = $this->projectRepository->find($message->getProjectId());
        if (null === $project) {
            $this->logger->critical('Project not found', [
                'projectId' => $message->getProjectId(),
            ]);

            return;
        }

        $duration = 0;
        $statuses = [];

        $tasks = $project->getTasks()->toArray();

        foreach ($tasks as $task) {
            $duration += $task->getDuration();
            $statuses[$task->getStatus()->value] = $task->getStatus();
        }

        $hasTaskInPending = in_array(TaskStatus::Pending, $statuses, true);
        $hasTaskInProgress = in_array(TaskStatus::InProgress, $statuses, true);
        $hasTaskInCompleted = in_array(TaskStatus::Completed, $statuses, true);

        $status = match (true) {
            $hasTaskInCompleted && !$hasTaskInPending && !$hasTaskInProgress => ProjectStatus::Completed,
            $hasTaskInProgress => ProjectStatus::InProgress,
            default => ProjectStatus::Pending,
        };

        $project
            ->setDuration($duration)
            ->setStatus($status);

        $this->logger->debug('Project status and duration updated', [
            'projectId' => $message->getProjectId(),
            'hasTaskInProgress' => $hasTaskInProgress,
            'hasTaskInCompleted' => $hasTaskInCompleted,
            'statuses' => $statuses,
            'status' => $status,
            'duration' => $duration,
        ]);

        $this->projectService->save($project);
    }
}
