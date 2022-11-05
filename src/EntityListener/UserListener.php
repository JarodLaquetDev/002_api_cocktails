<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener{

    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Méthode appelée au moment d'un persist
     *
     * @param User $user
     * @return void
     */
    public function prePersist(User $user)
    {
        // Hashage du mot de passe de l'utilisateur
        $this->hashPassword($user);
    }

    /**
     * Méthode appelée au moment d'une mise à jour
     *
     * @param User $user
     * @return void
     */
    public function preUpdate(User $user)
    {
        // Hashage du mot de passe de l'utilisateur
        $this->hashPassword($user);
    }

    /**
     * Méthode pour hasher un mot de passe
     *
     * @param User $user
     * @return void
     */
    public function hashPassword(User $user)
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
    }
}