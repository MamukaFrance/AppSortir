<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


// src/Repository/SortieRepository.php
    public function findRecent(?int $siteId = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.dateHeureDebut >= :dateLimite')
            ->setParameter('dateLimite', new \DateTimeImmutable('-1 month'))
            ->andWhere('s.dateLimiteInscription >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('s.dateHeureDebut', 'ASC');

        if ($siteId) {
            $qb->andWhere('s.idSite = :siteId')
                ->setParameter('siteId', $siteId);
        }

        return $qb->getQuery()->getResult();
    }


    public function mesSorties($userID): array
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->andWhere('s.idOrganisateur = :idorganisateur');
        $queryBuilder->setParameter('idorganisateur', $userID);
        return $queryBuilder->getQuery()->getResult();
    }

    public function annulee(int $sortieId): void
    {
         $this->createQueryBuilder('s')
            ->setParameter('s.idEtat', 3)
            ->getQuery()
            ->getResult();
    }


}
