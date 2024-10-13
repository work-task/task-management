<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\Enums\ProjectStatus;
use App\Http\RequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ProjectRequest implements RequestInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [ProjectStatus::class, 'values'])]
        public readonly string $status = '',
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $title = '',
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 65532)]
        public readonly string $description = '',
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $duration = 0,
    ) {
    }

    public function getStatus(): string
    {
        return $this->status;
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
        return (int) $this->duration;
    }
}
