<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\ProjectRequest;
use App\DTO\Response\ProjectResponse;
use App\Entity\Project;
use App\Entity\User;
use App\Http\ResponseFormatter;
use App\Services\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/projects')]
final class ProjectController extends AbstractController
{
    public function __construct(private readonly ProjectService $projectService)
    {
    }

    #[Route(methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        $projects = $this->projectService->getAll();

        $response = array_map(fn (Project $project) => ProjectResponse::fromEntity($project)->toArray(), $projects);

        return ResponseFormatter::success($response);
    }

    #[Route(methods: [Request::METHOD_POST])]
    public function create(ProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->saveFromDto($request);

        return ResponseFormatter::success(ProjectResponse::fromEntity($project)->toArray(), Response::HTTP_CREATED);
    }

    #[Route(path: '/{projectId}', requirements: ['projectId' => '\d+'], methods: [Request::METHOD_PUT])]
    public function update(ProjectRequest $request, int $projectId, #[CurrentUser] User $user): JsonResponse
    {
        $project = $this->projectService->getById($projectId);
        if (null === $project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($user->getId() !== $project->getUser()->getId()) {
            throw $this->createNotFoundException('Project not found');
        }

        $project = $this->projectService->saveFromDto($request, $project);

        return ResponseFormatter::success(ProjectResponse::fromEntity($project)->toArray());
    }

    #[Route(path: '/{projectId}', requirements: ['projectId' => '\d+'], methods: [Request::METHOD_DELETE])]
    public function delete(int $projectId, #[CurrentUser] User $user): JsonResponse
    {
        $project = $this->projectService->getById($projectId);
        if (null === $project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($user->getId() !== $project->getUser()->getId()) {
            throw $this->createNotFoundException('Project not found');
        }

        $this->projectService->delete($project);

        return ResponseFormatter::success();
    }
}
