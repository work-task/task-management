<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Request\ProjectRequest;
use App\Entity\Project;
use App\Entity\User;
use App\Enums\ProjectStatus;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class ProjectService
{
    public function __construct(
        private readonly Security $security,
        private readonly ProjectRepository $projectRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<int, Project>
     */
    public function getAll(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->projectRepository->findByUser($user);
    }

    public function save(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function saveFromDto(ProjectRequest $request, ?Project $project = null): Project
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $status = ProjectStatus::from($request->getStatus());

        $project ??= new Project();
        $project->setUser($user);
        $project->setStatus($status);
        $project->setTitle($request->getTitle());
        $project->setDescription($request->getDescription());
        $project->setDuration($request->getDuration());

        $this->save($project);

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->setDeletedAt(new \DateTimeImmutable());

        $this->save($project);
    }
}
