<?php
/**
 * VI-65 ProductCategoryCSVController
 *
 * @category   ProductCategoryApi
 * @package    Virtua_ProductCategoryApi
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Controller;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Services\ProductCategoryService;
use App\Services\SerializerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/csv/product/category", name="product_category_export_all_csv_api", methods="GET")
 */
class ProductCategoryCSVController extends Controller
{
    /**
     * @Route("/", name="product_category_export_all_csv_api", methods="GET")
     */
    public function exportAllCSV(ProductCategoryRepository $productCategories, SerializerService $serializer,
                                 ProductCategoryService $productCategoryService)
    {
        $data = $serializer->normalize($productCategories->findAll(), 'csv', [
            'groups' => ['ProductCategoryShowAPI']
        ]);
        for($i = 0; $i < count($data); $i++)
        {
            $data[$i]['mainImage'] = "upload/images/".$data[$i]['mainImage'];
        }
        $stringCsv = $serializer->serialize($data, 'csv');
        file_put_contents(
            'uploads/csv/productCategoryAll.csv',
            $stringCsv
        );
        $dataJson = json_encode([
            'link' => 'uploads/csv/productCategoryAll.csv'
        ]);
        $dataJson = str_replace('\/', '/', $dataJson);
        return new Response(
            $dataJson
            ,200);
    }

    /**
     * @Route("/{id}", name="product_category_export_csv_api", methods="GET")
     */
    public function exportCSV(ProductCategory $productCategory, SerializerService $serializer)
    {
        $data = $serializer->normalize($productCategory, 'csv', [
            'groups' => ['ProductCategoryShowAPI']
        ]);
        $data['mainImage'] = "uploads/images/".$data['mainImage'];
        $stringCsv = $serializer->serialize($data, 'csv');
        file_put_contents(
            'uploads/csv/productCategory'.$productCategory->getId().'.csv',
            $stringCsv
        );
        $dataJson = json_encode([
            'link' => 'uploads/csv/productCategory'.$productCategory->getId().'.csv'
        ]);
        $dataJson = str_replace('\/', '/', $dataJson);
        return new Response(
            $dataJson
            ,200);
    }
}
