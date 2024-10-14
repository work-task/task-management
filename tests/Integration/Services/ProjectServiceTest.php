<?php

declare(strict_types=1);

namespace App\Tests\Integration\Services;

use App\DTO\Request\ProjectRequest;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Services\ProjectService;
use App\Tests\Factory\ProjectFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Integration\IntegrationTestCase;

final class ProjectServiceTest extends IntegrationTestCase
{
    private ProjectService $projectService;
    private ProjectRepository $projectRepository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ProjectService $projectService */
        $projectService = self::getContainer()->get(ProjectService::class);
        $this->projectService = $projectService;

        /** @var ProjectRepository $projectRepository */
        $projectRepository = self::getContainer()->get(ProjectRepository::class);
        $this->projectRepository = $projectRepository;
    }

    public function testFindByUserAndIdReturnResult(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();

        $result = $this->projectService->getById($user, $project->getId());

        $this->assertInstanceOf(Project::class, $result);
    }

    public function testFindByUserAndIdReturnNull(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();

        $result = $this->projectService->getById($user, 9999);

        $this->assertNull($result);
    }

    public function testFindByUserAndIdAndDeletedReturnNull(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user, 'deletedAt' => new \DateTimeImmutable()])->_real();

        $result = $this->projectService->getById($user, $project->getId());

        $this->assertNull($result);
    }

    public function testSaveFromDbReturnSuccess(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();

        $title = 'save-project-from-dto';
        $description = 'project description';

        $request = new ProjectRequest(title: $title, description: $description);

        $project = $this->projectService->saveFromDto($user, $request);

        $result = $this->projectRepository->findOneBy(['title' => $title]);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertNotNull($project->getId());

        $this->assertInstanceOf(Project::class, $result);
        $this->assertSame($project->getId(), $result->getId());
    }

    public function testSaveFromDbWithAlreadyPersistProjectReturnSuccess(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();

        $request = new ProjectRequest(title: $this->faker->sentence(), description: $this->faker->paragraph());

        $project = $this->projectService->saveFromDto($user, $request, $project);

        $result = $this->projectRepository->find($project->getId());

        $this->assertInstanceOf(Project::class, $project);
        $this->assertInstanceOf(Project::class, $result);

        $this->assertSame($project->getId(), $result->getId());
    }

    public function testDeleteProjectReturnSuccess(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne()->_real();
        /** @var Project $project */
        $project = ProjectFactory::createOne(['user' => $user])->_real();

        $this->assertNull($project->getDeletedAt());

        $this->projectService->delete($project);

        $this->assertNotNull($project->getDeletedAt());
    }
}
