<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function filtres($param1,$param2,$param3,$param4,$param5,$param6){

        $qb=$this->createQueryBuilder('s');

        $qb->where('s.site = :param6')
            ->setParameter('param6',$param6);


        if($param3 != null){
            $qb->andWhere(
               $qb-> expr()->like('s.nom',':param3')
            )
                ->setParameter('param3', '%'.$param3.'%');

        }

        if($param4 != null) {

            $qb->andWhere('s.organisateur = :param4')
                ->setParameter('param4',$param4);
        }

        if($param5 != null){
            $qb->andWhere('s.inscrits = :param5')
                ->setParameter('param5',$param5);

        }







//         if ($param1 != null) {
//             $qb->andWhere('s.dateHeureDebut > :param1')
//                 ->setParameter('param1',$param1);
//         }
//        if ($param2 != null) {
//            $qb->andWhere('s.dateHeureDebut < :param2')
//                ->setParameter('param2',$param2);
//        }



         $query=$qb->getQuery();
         return $query->getResult();

    }



    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
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
    public function findOneBySomeField($value): ?Sortie
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
