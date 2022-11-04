<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recette>
 *
 * @method Recette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recette[]    findAll()
 * @method Recette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecetteRepository extends ServiceEntityRepository
{
    /**
     * Constructeur
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }
    /**
     * Méthode pour sauvegarder une Recette en bdd
     *
     * @param Recette $entity
     * @param boolean $flush
     * @return void
     */
    public function save(Recette $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity); // ajouter

        if ($flush) {
            $this->getEntityManager()->flush(); // mettre à jour la bdd
        }
    }
    /**
     * Méthode pour supprimer une recette en bdd
     *
     * @param Recette $entity
     * @param boolean $flush
     * @return void
     */
    public function remove(Recette $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity); // supprimer

        if ($flush) {
            $this->getEntityManager()->flush(); // mettre à jour la bdd
        }
    }
    /**
     * Méthode pour sortir toutes les recettes "on" avec une pagination
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
     * Méthode pour trouver une, ou plusieurs recettes, à partir d'un ingrédient
     *
     * @param [type] $nameIngredient
     * @return void
     */
    public function findRecetteByIngredient($nameIngredient)
    {
        foreach($this->recette_ingredient as $ingredient)
        {
            if($ingredient->getIngredientName() == $nameIngredient)
            {
                return $ingredient->getRecetteName();
            }
            //
        }
        // Utilisateur entre un nom, ou plusieurs noms d'ingrédients
        // On récupérer les id de ces derniers
        // On parcourt la table recette_ingredient pour avoir les id des recettes comprenants les différents ingrédients
        // SELECT recette_id FROM `recette_ingredient` where ingredient_id = nombre;
        // On récupérer les noms des recettes à partir des id
    }

//    /**
//     * @return Recette[] Returns an array of Recette objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Recette
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
