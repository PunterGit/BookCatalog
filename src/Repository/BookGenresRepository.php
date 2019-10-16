<?php

namespace App\Repository;

use App\Entity\BookGenres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BookGenres|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookGenres|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookGenres[]    findAll()
 * @method BookGenres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookGenresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookGenres::class);
    }

    // /**
    //  * @return BookGenres[] Returns an array of BookGenres objects
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
    public function findOneBySomeField($value): ?BookGenres
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
