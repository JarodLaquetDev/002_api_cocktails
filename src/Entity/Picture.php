<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use App\Repository\RecetteRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

 /**
 * @Hateoas\Relation(
 *      "self",
 *      href=@Hateoas\Route(
 *          "picture.get",
 *          parameters = {
 *              "idPicture" = "expr(object.getId())"
 *          },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getAllPictures")
 * )
 */
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
    #[Groups(["getPicture", "getAllPictures"])]
    private ?int $id = null;

    // Nom d'une image
    // Ex : vacances_130
    #[ORM\Column(length: 255)]
    #[Groups(["getPicture", "getAllPictures", "getIngredient", "getRecette"])]
    private ?string $realName = null;

    // Chemin privée d'une image
    #[ORM\Column(length: 255)]
    #[Groups(["getPicture", "getAllPictures", "getIngredient", "getRecette"])]
    private ?string $realPath = null;

    // Chemin public d'une image
    #[ORM\Column(length: 255)]
    #[Groups(["getPicture", "getAllPictures", "getIngredient", "getRecette"])]
    private ?string $publicPath = null;

    #[ORM\Column(length: 50)]
    #[Groups(["getPicture", "getAllPictures", "getIngredient", "getRecette"])]
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

    /**
     * Obtenir l'id d'une image
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Obtenir le nom d'une image
     *
     * @return string|null
     */
    public function getRealName(): ?string
    {
        return $this->realName;
    }
    /**
     * Donner un nom à une image
     *
     * @param string $realName
     * @return self
     */
    public function setRealName(string $realName): self
    {
        $this->realName = $realName;

        return $this;
    }
    /**
     * Obtenir le chemin privé d'une image
     *
     * @return string|null
     */
    public function getRealPath(): ?string
    {
        return $this->realPath;
    }
    /**
     * Donner le chemin privé d'une image
     *
     * @param string $realPath
     * @return self
     */
    public function setRealPath(string $realPath): self
    {
        $this->realPath = $realPath;

        return $this;
    }
    /**
     * Obtenir une image
     *
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }
    /**
     * Enregistrer une image
     *
     * @param File|null $file
     * @return Picture|null
     */
    public function setFile(?File $file): ?Picture
    {
        $this->file = $file;

        return $this;
    }
    /**
     * Obtenir le chemin publique d'une image
     *
     * @return string|null
     */
    public function getPublicPath(): ?string
    {
        return $this->publicPath;
    }
    /**
     * Donner un chemin publique à une image
     *
     * @param string $publicPath
     * @return self
     */
    public function setPublicPath(string $publicPath): self
    {
        $this->publicPath = $publicPath;

        return $this;
    }
    /**
     * Obtenir le mime type d'une image
     *
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }
    /**
     * Donner un mime type à une image
     *
     * @param string $mimeType
     * @return self
     */
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }
    /**
     * Obtenir le status d'une image
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    /**
     * Donner un status à une image
     *
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
