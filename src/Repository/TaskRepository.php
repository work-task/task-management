<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByUserProjectAndId(User $user, Project $project, int $taskId): ?Task
    {
        return $this->createQueryBuilder('t')
            ->join('t.project', 'p')
            ->andWhere('t.id = :taskId')
            ->andWhere('t.project = :project')
            ->andWhere('p.user = :user')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('taskId', $taskId)
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, Task>
     */
    public function findByProjectAndUser(User $user, Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.project', 'p')
            ->andWhere('t.project = :project')
            ->andWhere('p.user = :user')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->addOrderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
