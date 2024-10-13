<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enums\TaskStatus;
use App\Repository\TaskRepository;
use App\Traits\SoftDeletable;
use App\Traits\Timestamp;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'tasks')]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    use Timestamp;
    use SoftDeletable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int $id;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    protected Project $project;

    #[ORM\Column]
    protected string $status;

    #[ORM\Column]
    protected string $title;

    #[ORM\Column(type: Types::TEXT)]
    protected string $description;

    #[ORM\Column(type: Types::INTEGER)]
    protected int $duration = 0;

    public function __construct()
    {
        $this->status = TaskStatus::Pending->value;

        $this->updatedAt = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): TaskStatus
    {
        return TaskStatus::from($this->status);
    }

    public function setStatus(TaskStatus $status): static
    {
        $this->status = $status->value;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
