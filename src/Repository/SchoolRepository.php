<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\University;

/**
 * @method School|null find($id, $lockMode = null, $lockVersion = null)
 * @method School|null findOneBy(array $criteria, array $orderBy = null)
 * @method School[]    findAll()
 * @method School[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    // /**
    //  * Get all active schools
    //  */
    
    public function getAllWithQueryBuilder()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;


    }
    public function getAllSchoolsOfWithQueryBuider(University $parentUniversity)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.parentUniversity = :parentUniversity')
            ->setParameter('parentUniversity', $parentUniversity)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllActivesSchoolsOfWithQueryBuider(University $parentUniversity)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.parentUniversity = :parentUniversity')
            ->setParameter('parentUniversity', $parentUniversity)
            ->andWhere('s.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    

    // /**
    //  * Get all exams from school
    //  */


    // /**
    //  * @return School[] Returns an array of School objects
    //  */
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
    public function findOneBySomeField($value): ?School
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