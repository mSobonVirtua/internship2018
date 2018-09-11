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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * $var string $info
     * @ORM\Column(type="string", length=255)
     */
    private $info;

    /**
     * @var /DateTime $createdDate
     * @ORM\Column(type="date")
     */
    private $createdDate;

    /**
     * @var /DateTime $createdDate
     * @ORM\Column(type="date")
     */
    private $modifiedDate;

    /**
     * @var ProductCategory $category
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductCategory", inversedBy="products")
     * @ORM\JoinColumn()
     */
    private $category;

    /**
     * @var File $picture
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\File(
     *     maxSize = "1M",
     *     mimeTypes = {"image/jpeg", "image/jpg","image/png"},
     *     mimeTypesMessage = "Niepoprawne rozszerzenie!",
     *     maxSizeMessage="Niepoprawny rozmiar!"
     * )
     */
    private $picture;

    /**
     * @var ArrayCollection $images
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\ImageProduct",
     *     mappedBy="product",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    private $images;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * @Groups({"ProductCategoryShowAPI"})
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @Groups({"ProductCategoryShowAPI"})
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getInfo(): ?string
    {
        return $this->info;
    }

    /**
     * @param string $info
     * @return Product
     */
    public function setInfo(string $info): self
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTimeInterface $createdDate
     * @return Product
     */
    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getModifiedDate(): ?\DateTimeInterface
    {
        return $this->modifiedDate;
    }

    /**
     * @param \DateTimeInterface $modifiedDate
     * @return Product
     */
    public function setModifiedDate(\DateTimeInterface $modifiedDate): self
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * @return ProductCategory
     */
    public function getCategory(): ?ProductCategory
    {
        return $this->category;
    }


    /**
     * @param ProductCategory $category
     * @return Product
     */
    public function setCategory(?ProductCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     * @return Product
     */
    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection|ImageProduct[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @param ImageProduct $image
     * @return Product
     */
    public function addImage(ImageProduct $image): self
    {

        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    /**
     * @param ImageProduct $image
     * @return Product
     */
    public function removeImage(ImageProduct $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }


    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
