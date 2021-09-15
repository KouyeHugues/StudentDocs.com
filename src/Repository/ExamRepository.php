<?php

namespace App\Repository;

use App\Entity\Exam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\School;

/**
 * @method Exam|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exam|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exam[]    findAll()
 * @method Exam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exam::class);
    }

    // /**
    //  * Get all active exams
    //  */
    
    public function getAllWithQueryBuilder()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('e.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllExamsOfWithQueryBuider(School $parentSchool)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.parentSchool = :parentSchool')
            ->setParameter('parentSchool', $parentSchool)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllActiveExamsOfWithQueryBuider(School $parentSchool)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.parentSchool = :parentSchool')
            ->setParameter('parentSchool', $parentSchool)
            ->andWhere('s.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Exam[] Returns an array of Exam objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Exam
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}