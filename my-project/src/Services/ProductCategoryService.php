<?php /**
       * @noinspection PhpCSValidationInspection
       */
/**
 * @noinspection ALL
 */

/**
 * VI-61 ProductCategoryService
 *
 * @category  ProductCategoryApi
 * @package   Virtua_ProductCategoryApi
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */

namespace App\Services;

use App\Entity\ProductCategory;
use Symfony\Component\HttpFoundation\File\File;

class ProductCategoryService
{
    private $targetDirectory;
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param  string $json
     * @return ProductCategory
     */
    public function convertJsonToProductCategory($json)
    {
        /**
         * @var array $productCategoryParameters
        */
        $productCategory = new ProductCategory();
        $productCategoryParameters = json_decode($json, true);
        $productCategory->setName($productCategoryParameters['name']);
        $productCategory->setDescription($productCategoryParameters['description']);
        $mainImageUrl = $productCategoryParameters['mainImage'];
        $fileName = "";
        $file = null;
        if ($mainImageUrl != null) {
            $fileName = $this->targetDirectory.basename($mainImageUrl);
            file_put_contents($fileName, file_get_contents($mainImageUrl));
            $file = new File($fileName);
        }
        $productCategory->setMainImage($file);
        return $productCategory;
    }

    public function createProductCategoryFromArray(array $productCategory) : ProductCategory
    {
        $tmpProductCategory = new ProductCategory();
        $tmpProductCategory->setName($productCategory['name']);
        $tmpProductCategory->setDescription($productCategory['description']);
        $tmpProductCategory->setMainImage($productCategory['mainImage']);
        $tmpProductCategory->setDateOfCreation(new \DateTime($productCategory['dateOfCreation']));
        $tmpProductCategory->setDateOfLastModification(new \DateTime($productCategory['dateOfLastModification']));

        return $tmpProductCategory;
    }

    public function createProductCategoryFromArrayByKeyValue(array $productCategory)
    {
        $newProductCategory = new ProductCategory();
        foreach ($productCategory as $key => $value) {
            if (strpos($key, "date") !== false) {
                $value = new \DateTime($value);
            }
            $newProductCategory->setData($key, $value);
        }
        return $newProductCategory;
    }
}
