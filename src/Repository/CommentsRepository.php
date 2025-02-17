<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    public function findByUserAndDate($user, $limit = null)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.author = :user')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByMatchAndDate($match, $limit = null)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.matchNbr = :match')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('match', $match)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByRatings($match, $limit = null)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.matchNbr = :match')
            ->andWhere('c.rating IS NOT NULL')
            ->setParameter('match', $match)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByAuthorAndRatings($match, $author, $limit = null)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.matchNbr = :match')
            ->andWhere('c.author = :author')
            ->andWhere('c.rating IS NOT NULL')
            ->setParameter('match', $match)
            ->setParameter('author', $author)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Comments[] Returns an array of Comments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comments
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
