<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
/**
 * @Vich\Uploadable()
 */
class Picture
{
    // Identifiant unique
    // Ex : 130
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom d'une image
    // Ex : vacances_130
    #[ORM\Column(length: 255)]
    #[Groups(["getPicture"])]
    private ?string $realName = null;

    // Chemin privée d'une image
    #[ORM\Column(length: 255)]
    #[Groups(["getPicture"])]
    private ?string $realPath = null;

    // Chemin public d'une image
    #[ORM\Column(length: 255)]
    #[Groups(["getPicture"])]
    private ?string $publicPath = null;

    #[ORM\Column(length: 50)]
    #[Groups(["getPicture"])]
    private ?string $mimeType = null;

    // Status d'une image
    // Ex : on
    #[ORM\Column(length: 20)]
    private ?string $status = null;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="pictures", fileNameProperty="realPath")
     */
    private ?File $file;

    // Cas d'utilisation : obtenir l'id d'une image
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : int
    public function getId(): ?int
    {
        return $this->id;
    }
    // Cas d'utilisation : obtenir le nom d'une image
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getRealName(): ?string
    {
        return $this->realName;
    }
    // Cas d'utilisation : donner un nom à une image
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setRealName(string $realName): self
    {
        $this->realName = $realName;

        return $this;
    }
    // Cas d'utilisation : obtenir le chemin privé d'une image
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getRealPath(): ?string
    {
        return $this->realPath;
    }
    // Cas d'utilisation : donner un chemin privé à une image
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setRealPath(string $realPath): self
    {
        $this->realPath = $realPath;

        return $this;
    }
    // Cas d'utilisation : obtenir une image
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : file
    public function getFile(): ?File
    {
        return $this->file;
    }
    // Cas d'utilisation : donner une image
    // Paramètre(s) d'entrée : file
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setFile(?File $file): ?Picture
    {
        $this->file = $file;

        return $this;
    }
    // Cas d'utilisation : obtenir le chemin public d'une image
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getPublicPath(): ?string
    {
        return $this->publicPath;
    }
    // Cas d'utilisation : donner le chemin public d'une image
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setPublicPath(string $publicPath): self
    {
        $this->publicPath = $publicPath;

        return $this;
    }
    // Cas d'utilisation : obtenir le type d'une image
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }
    // Cas d'utilisation : donner un type à une image
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }
    // Cas d'utilisation : obtenir le status d'une image
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getStatus(): ?string
    {
        return $this->status;
    }
    // Cas d'utilisation : donner un status à une image
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
