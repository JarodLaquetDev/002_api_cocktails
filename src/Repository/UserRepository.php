<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{ 
    /**
     * Constructeur
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    /**
     * Méthode pour sauvegarder un utilisateur en bdd
     *
     * @param User $entity
     * @param boolean $flush
     * @return void
     */
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity); // ajouter

        if ($flush) {
            $this->getEntityManager()->flush(); // mettre à jour la bdd
        }
    }
    /**
     * Méthode pour supprimer un utilisateur en bdd
     *
     * @param User $entity
     * @param boolean $flush
     * @return void
     */
    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity); // supprimer

        if ($flush) {
            $this->getEntityManager()->flush(); // mettre à jour la bdd
        }
    }
    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }
    /**
     * Méthode pour sortir tous les users "on " avec une pagination
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
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
