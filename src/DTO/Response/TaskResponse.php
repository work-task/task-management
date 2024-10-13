<?php

declare(strict_types=1);

namespace App\DTO\Response;

use App\Entity\Task;
use App\Enums\TaskStatus;

final class TaskResponse
{
    public function __construct(
        private readonly int $id,
        private readonly TaskStatus $status,
        private readonly string $title,
        private readonly string $description,
        private readonly int $duration,
        private readonly \DateTimeImmutable $createdAt,
        private readonly \DateTimeImmutable $updatedAt,
    ) {
    }

    public static function fromEntity(Task $task): self
    {
        return new self(
            id: $task->getId(),
            status: $task->getStatus(),
            title: $task->getTitle(),
            description: $task->getDescription(),
            duration: $task->getDuration(),
            createdAt: $task->getCreatedAt(),
            updatedAt: $task->getUpdatedAt(),
        );
    }

    /**
     * @return array{
     *     id: int,
     *     status: string,
     *     title: string,
     *     description: string,
     *     duration: int,
     *     created_at: string,
     *     updated_at: string,
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'duration' => $this->getDuration(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format(\DateTimeInterface::ATOM);
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt->format(\DateTimeInterface::ATOM);
    }
}
