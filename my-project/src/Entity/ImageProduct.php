<?php
/**
 * VI-44 - Add and Edit gallery
 *
 * @category  Entity
 * @package   Gallery
 * @copyright Copyright (c) Virtua
 * @author    Dawid Kruczek
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImagesRepository")
 */
class ImageProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="images", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    private $image;

    public function getImage()
    {

        return $this->image;
    }

    public function setImage(UploadedFile $image = null)
    {
        $imagepath=$this->generateUniqueFileName().'.'.$image->guessExtension();
        $this->setName($imagepath);
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
