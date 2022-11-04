<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Identifiant unique
    // Ex : 130
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom d'un utilisateur
    // Ex : Jarod
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["getAllUsers", "getUser"])]
    private ?string $username = null;

    #[Groups(["getAllUsers", "getUser"])]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
    
    // Cas d'utilisation : obtenir l'id d'un utilisateur
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : int
    public function getId(): ?int
    {
        return $this->id;
    }
    // Cas d'utilisation : obtenir le nom d'un utilisateur
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getUsername(): ?string
    {
        return $this->username;
    }
    // Cas d'utilisation : donner un nom à un utilisateur
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    // Cas d'utilisation : donner un rôle à un utilisateur
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    // Cas d'utilisation : obtenir le mdp d'un utilisateur
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    // Cas d'utilisation : donner un mdp à un utilisateur
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
