<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ingredient>
 *
 * @method Ingredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingredient[]    findAll()
 * @method Ingredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngredientRepository extends ServiceEntityRepository
{
    /**
     * Constructeur
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }
    /**
     * Méthode pour sauvegarder un ingrédient en BDD
     *
     * @param Ingredient $entity
     * @param boolean $flush
     * @return void
     */
    public function save(Ingredient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity); // ajouter

        if ($flush) {
            $this->getEntityManager()->flush(); // mettre à jour la bdd
        }
    }
    /**
     * Méthode pour supprimer un ingrédient de la BDD
     *
     * @param Ingredient $entity
     * @param boolean $flush
     * @return void
     */
    public function remove(Ingredient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity); // supprimer

        if ($flush) {
            $this->getEntityManager()->flush(); // mettre à jour la bdd
        }
    }
    /**
     * Méthode pour sortir tous les ingrédients "on" avec une pagination
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
    /**
     * Méthode pour sortir tous les ingrédients en fonction de leur statut
     *
     * @param [type] $status
     * @return void
     */
    public function findWithStatus($status)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->where('i.status = :status')->setParameter('status', $status);
        return $qb->getQuery()->getResult();
        // test 2
    }

}
