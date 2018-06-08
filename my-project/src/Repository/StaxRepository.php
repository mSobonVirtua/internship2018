<?php

namespace App\Repository;

use App\Entity\Stax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Stax|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stax|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stax[]    findAll()
 * @method Stax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaxRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Stax::class);
    }

//    /**
//     * @return Stax[] Returns an array of Stax objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Stax
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
