<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.archive = false')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByUser(int $userID): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.teamList', 't')
            ->join('t.user', 'u')
            ->andWhere('u.id = :userID')
            ->setParameter('userID', $userID)
            ->getQuery()
            ->getResult();
    }
}
