<?php
/**
 * VI-36 ImageEntity
 *
 * @category   Entity
 * @package    Virtua_ImageEntity
 * @package    Virtua_AddCategoryImage
 * @package    Virtua_ProductCategoryEdit
 * @package    Virtua_ProductCategoryNew
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
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
