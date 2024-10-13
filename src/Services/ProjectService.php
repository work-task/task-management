<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Request\ProjectRequest;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ProjectService
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getById(User $user, int $projectId): ?Project
    {
        return $this->projectRepository->findByUserAndId($user, $projectId);
    }

    /**
     * @return array<int, Project>
     */
    public function getAll(User $user): array
    {
        return $this->projectRepository->findAllByUser($user);
    }

    public function save(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function saveFromDto(User $user, ProjectRequest $request, ?Project $project = null): Project
    {
        $project ??= new Project();
        $project->setUser($user);
        $project->setTitle($request->getTitle());
        $project->setDescription($request->getDescription());

        $this->save($project);

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->setDeletedAt(new \DateTimeImmutable());

        $this->save($project);
    }
}
