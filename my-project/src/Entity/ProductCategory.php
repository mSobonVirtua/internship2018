<?php
/**
 * VI-31 ProductCategoryEntity
 *
 * @category  Entity
 * @package   Virtua_ProductCategoryEntity
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductCategoryRepository")
 */
class ProductCategory
{
    /**
     * @var integer $id
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
     * @var  string $description
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var /DateTime $dateOfCreation
     * @ORM\Column(type="date")
     */
    private $dateOfCreation;

    /**
     * @var /DateTime $dateOfLastModification
     * @ORM\Column(type="date")
     */
    private $dateOfLastModification;

    /**
     * @var ArrayCollection $products
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
     */
    private $products;

    /**
     * @var File $mainImage
     * @ORM\Column(type="string")
     * @Assert\File(
     *      maxSize = "250000",
     *      mimeTypes = {"image/jpeg", "image/png"}
     * )
     */
    private $mainImage;

    /**
     * @var ArrayCollection $images
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     * @ORM\JoinTable(name="category_images",
     *                  joinColumns={@ORM\JoinColumn(name="product_category_id", referencedColumnName="id")},
     *                  inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")}
     *                  )
     */
    private $images;

    /**
     * ProductCategory constructor.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryIndexAPI", "ProductCategoryExport"})
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryIndexAPI", "ProductCategoryExport"})
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     * @return \DateTime
     */
    public function getDateOfCreation()
    {
        return $this->dateOfCreation;
    }

    /**
     * @param \DateTime $dateOfCreation
     */
    public function setDateOfCreation($dateOfCreation): void
    {
        $this->dateOfCreation = $dateOfCreation;
    }

    /**
     * @return \DateTime
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getDateOfLastModification()
    {
        return $this->dateOfLastModification;
    }

    /**
     * @param \DateTime $dateOfLastModification
     */
    public function setDateOfLastModification($dateOfLastModification): void
    {
        $this->dateOfLastModification = $dateOfLastModification;
    }

    /**
     * @return  ArrayCollection|Product[]
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param ArrayCollection|[] $products
     */
    public function setProducts($products): void
    {
        $this->products = $products;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param Product $product
     * @return ProductCategory
     */
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    /**
     * @param Product $product
     * @return ProductCategory
     */
    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return  string|File
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     * @param string|File $mainImage
     */
    public function setMainImage($mainImage): void
    {
        $this->mainImage = $mainImage;
    }

    /**
     * @return  ArrayCollection
     * @Groups({"ProductCategoryShowAPI"})
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setData($key, $value)
    {
        $this->{$key} = $value;
    }
}
