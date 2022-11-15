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
     * Méthode pour obtenir toutes les recettes associées à un ingrédient (par le nom)
     *
     * @param string $name
     * @return void
     */
    public function findRecetteByIngredient(string $nameIngredient){
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = \'on\'')
            ->innerJoin('r.recetteIngredients', 'i')
            ->andWhere('i.ingredientName = :nameIngredient')
            ->setParameter('nameIngredient', $nameIngredient);

            return $qb->getQuery()->getResult();
    }

    /**
     * Méthode pour obtenir toutes les recettes associées à un ingrédient (par le nom)
     *
     * @param string $name
     * @return void
     */
    public function test($nameIngredient){
        
        $qb = $this->createQueryBuilder('r')
        ->innerJoin('r.recetteIngredients', 'i')
        ->where('r.status = \'on\'');

        // on admet une table recIng de relation entre ingredients et recette 
        //
        //SELECT * FROM recIng WHERE id IN (SELECT IdRecette FROM i AS ing1 WHERE ing1 = ?) AND (SELECT IdRecette FROM i AS ing2 WHERE ing2 = ?)
        for($i = 0; $i < count($nameIngredient); $i++)
        {
            $qb->andWhere('i.ingredientName = :nameIngredient')->setParameter('nameIngredient', $nameIngredient[$i]);
        }
        return $qb->getQuery()->getResult();
    }
}
