<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 22.06.2018
 * Time: 15:32
 */

namespace App\Services;


use App\Entity\ProductCategory;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductCategoryService
{
    public function __construct()
    {
        //TODO Pozamieniać upload files w tu i w conrtollerze na zmienną
    }
    /**
     * @param string $json
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
        if($mainImageUrl != null)
        {
            $fileName = "uploads/images/".basename($mainImageUrl);
            file_put_contents($fileName, file_get_contents($mainImageUrl));
            $file = new File($fileName);
        }
        $productCategory->setMainImage($file);
        return $productCategory;
    }
}