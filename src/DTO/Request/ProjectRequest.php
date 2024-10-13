<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\Http\RequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ProjectRequest implements RequestInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $title = '',
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 65532)]
        public readonly string $description = '',
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
