<?php

namespace App\Repository;

use App\Entity\BookAuthors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BookAuthors|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookAuthors|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookAuthors[]    findAll()
 * @method BookAuthors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookAuthorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookAuthors::class);
    }

    // /**
    //  * @return BookAuthors[] Returns an array of BookAuthors objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookAuthors
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
