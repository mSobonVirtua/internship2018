<?php
/**
 * VI-36 ImageEntity
 *
 * @category   Entity
 * @package    Virtua_EntityImage
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\File(
     *      maxSize = "250000",
     *      mimeTypes = {"image/jpeg", "image/png"},
     *      mimeTypesMessage = "Please upload jpg or png file"
     * )
     */
    private $path;

    /**
     * @Groups({"ProductCategoryShowAPI"})
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     * @Groups({"ProductCategoryShowAPI"})
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }

}
