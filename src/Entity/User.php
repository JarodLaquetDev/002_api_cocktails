<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Identifiant unique
    // Ex : 130
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllUsers", "getUser"])]
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

    // Statut de l'utilisateur
    // Ex : on
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Un utilisateur doit avoir un statut")]
    #[Assert\NotNull()]
    #[Assert\Choice(choices: ['on','off'], message: "Veuillez entrer un statut")]
    private ?string $status = null;
    
    /**
     * Obtenir l'id d'un utilisateur
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Obtenir le nom d'un utilisateur
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }
    /**
     * Donner un nom à un utilisateur
     *
     * @param string $username
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    /**
     * Obtenir l'identifiant utilisateur
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }
    /**
     * Obtenir les rôles d'un utilisateur
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    /**
     * Donner un rôle à un utilisateur
     *
     * @param array $roles
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    /**
     * Obtenir le mot de passe d'un utilisateur
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    /**
     * Donner un mot de passe à un utilisateur
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * Obtenir le status d'un ingrédient
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    /**
     * Donner un status à un utilisateur
     *
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

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
