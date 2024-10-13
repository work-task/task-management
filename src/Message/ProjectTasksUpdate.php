<?php

namespace App\Message;

final class ProjectTasksUpdate
{
    public function __construct(private readonly int $projectId)
    {
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }
}
