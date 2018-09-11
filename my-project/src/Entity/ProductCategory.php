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

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductCategoryRepository")
 */
class ProductCategory
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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $dateOfCreation;

    /**
     * @ORM\Column(type="date")
     */
    private $dateOfLastModification;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
     */
    private $products;

    /**
     * @ORM\Column(type="string")
     * @Assert\File(
     *      maxSize = "250000",
     *      mimeTypes = {"image/jpeg", "image/png"}
     * )
     */
    private $mainImage;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     * @ORM\JoinTable(name="category_images",
     *                  joinColumns={@ORM\JoinColumn(name="product_category_id", referencedColumnName="id")},
     *                  inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")}
     *                  )
     */
    private $images;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryIndexAPI", "ProductCategoryExport"})
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return                            mixed
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryIndexAPI", "ProductCategoryExport"})
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return                            mixed
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return                            mixed
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getDateOfCreation()
    {
        return $this->dateOfCreation;
    }

    /**
     * @param mixed $dateOfCreation
     */
    public function setDateOfCreation($dateOfCreation): void
    {
        $this->dateOfCreation = $dateOfCreation;
    }

    /**
     * @return                            mixed
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getDateOfLastModification()
    {
        return $this->dateOfLastModification;
    }

    /**
     * @param mixed $dateOfLastModification
     */
    public function setDateOfLastModification($dateOfLastModification): void
    {
        $this->dateOfLastModification = $dateOfLastModification;
    }

    /**
     * @return                            Collection|Product[]
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products): void
    {
        $this->products = $products;
    }

    public function __toString()
    {
        return $this->name;
    }
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

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
     * @return                            mixed
     * @Groups({"ProductCategoryShowAPI", "ProductCategoryExport"})
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     * @param mixed $mainImage
     */
    public function setMainImage($mainImage): void
    {
        $this->mainImage = $mainImage;
    }

    /**
     * @return                             ArrayCollection
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

    public function setData($key, $value)
    {
        $this->{$key} = $value;
    }
}
