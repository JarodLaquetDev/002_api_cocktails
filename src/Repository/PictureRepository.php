<?php

namespace App\Repository;

use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Picture>
 *
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    /**
     * Constructeur
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }
    /**
     * Méthode pour sauvegarder une image en bdd
     *
     * @param Picture $entity
     * @param boolean $flush
     * @return void
     */
    public function save(Picture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity); // ajouter

        if ($flush) {
            $this->getEntityManager()->flush(); // mettre à jour la bdd
        }
    }
    /**
     * Méthode pour supprimer une image en bdd
     *
     * @param Picture $entity
     * @param boolean $flush
     * @return void
     */
    public function remove(Picture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity); // supprimer

        if ($flush) {
            $this->getEntityManager()->flush(); // mettre à jour la bdd
        }
    }
    /**
     * Méthode pour sortir toutes les images"on" avec une pagination
     *
     * @param [type] $page
     * @param [type] $limit
     * @return void
     */
    public function findWithPagination($page, $limit){
        $qb = $this->createQueryBuilder('i');
        $qb->setFirstResult(($page - 1) * $limit);
        $qb->setMaxResults($limit);
        $qb->where('i.status = \'on\'');
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Picture[] Returns an array of Picture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Picture
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
