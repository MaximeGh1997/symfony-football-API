<?php

namespace App\Repository;

use App\Entity\Matchs;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Matchs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matchs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matchs[]    findAll()
 * @method Matchs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matchs::class);
    }

    public function findByGroup($group, $limit = null)
    {
        return $this->createQueryBuilder('m')
                    ->select('m')
                    ->where('m.groupName = :group')
                    ->setParameter('group', $group)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }

    public function findByTeam($team, $limit = null)
    {
        return $this->createQueryBuilder('m')
                    ->select('m')
                    ->where('m.team1 = :team')
                    ->orWhere('m.team2 = :team')
                    ->setParameter('team', $team)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }

    public function findByDate($order, $limit = null)
    {
        return $this->createQueryBuilder('m')
                    ->select('m, d')
                    ->join('m.date', 'd')
                    ->orderBy('d.date', $order)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }

    public function findNextsMatchs($now, $limit = null)
    {
        return $this->createQueryBuilder('m')
                    ->select('m, d')
                    ->join('m.date', 'd')
                    ->where('d.date > :now')
                    ->andWhere('m.isPlayed IS NULL')
                    ->orderBy('d.date', 'ASC')
                    ->setParameter('now', $now)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }

    public function findLastsResults($limit = null)
    {
        return $this->createQueryBuilder('m')
                    ->select('m, d')
                    ->where('m.isPlayed = true')
                    ->join('m.date', 'd')
                    ->orderBy('d.date', 'DESC')
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }

    public function findGroupMatchsPlayed($limit = null)
    {
        return $this->createQueryBuilder('m')
                    ->select('m')
                    ->where('m.isPlayed = true')
                    ->andWhere('m.groupName IS NOT NULL')
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }

    public function findStageMatchsPlayed($stage, $limit = null)
    {
        return $this->createQueryBuilder('m')
                    ->select('m')
                    ->where('m.isPlayed = true')
                    ->andWhere('m.stage = :stage')
                    ->setParameter('stage', $stage)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }

    // /**
    //  * @return Matchs[] Returns an array of Matchs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Matchs
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
