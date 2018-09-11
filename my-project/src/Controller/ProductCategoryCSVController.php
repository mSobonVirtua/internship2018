<?php
/**
 * VI-65 ProductCategoryCSVController
 *
 * @category  ProductCategoryApi
 * @package   Virtua_ProductCategoryApi
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Controller;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Services\ProductCategoryService;
use App\Services\SerializerService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/csv/product/category", name="product_category_export_all_csv_api", methods="GET")
 */
class ProductCategoryCSVController extends Controller
{
    /**
     * @Route("/", name="product_category_export_all_csv_api", methods="GET")
     */
    public function exportAllCSV(
        ProductCategoryRepository $productCategories,
        SerializerService $serializer,
        ProductCategoryService $productCategoryService
    ) {
        $data = $serializer->normalize(
            $productCategories->findAll(),
            'csv',
            [
            'groups' => ['ProductCategoryShowAPI']
            ]
        );
        $stringCsv = $serializer->serialize($data, 'csv');
        file_put_contents(
            'uploads/csv/productCategoryAll.csv',
            $stringCsv
        );
        $dataJson = json_encode(
            [
            'link' => 'uploads/csv/productCategoryAll.csv'
            ]
        );
        $dataJson = str_replace('\/', '/', $dataJson);
        return new Response(
            $dataJson,
            200
        );
    }

    /**
     * @Route("/{id}", name="product_category_export_csv_api", methods="GET")
     */
    public function exportCSV(ProductCategory $productCategory, SerializerService $serializer)
    {
        $data = $serializer->normalize(
            $productCategory,
            'csv',
            [
            'groups' => ['ProductCategoryShowAPI']
            ]
        );
        $stringCsv = $serializer->serialize($data, 'csv');
        file_put_contents(
            'uploads/csv/productCategory'.$productCategory->getId().'.csv',
            $stringCsv
        );
        $dataJson = json_encode(
            [
            'link' => 'uploads/csv/productCategory'.$productCategory->getId().'.csv'
            ]
        );
        $dataJson = str_replace('\/', '/', $dataJson);
        return new Response(
            $dataJson,
            200
        );
    }

    /**
     * @Route("/", name="product_category_import_csv_api", methods="POST")
     */
    public function importFromCSV(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ProductCategoryService $productCategoryService
    ) {
        /***
         * @var ProductCategory[] $productCategoriesArray
        */
        $productCategoriesArray = $serializer->decode(file_get_contents($request->get('csvFile')), 'csv');
        $numberOfImportedCategories = 0;
        $numberOfErrors = 0;
        $productCategoryNotImportedLog = [];
        if (count($productCategoriesArray) == 0) {
            return new JsonResponse(
                [
                'error' => 'CSV file is empty',
                'numberOfImportedCategories' => $numberOfImportedCategories
                ],
                500
            );
        }

        if ($this->isOnlyOneRowOfData($productCategoriesArray)) {
            $productCategoriesArray = [$productCategoriesArray];
        }
        $em = $this->getDoctrine()->getManager();

        /***
         * @var ProductCategory[] $tmpProductsCategories
        */
        $tmpProductsCategories = [];
        foreach ($productCategoriesArray as $productCategory) {
            $tmpProductCategory = $productCategoryService->createProductCategoryFromArray($productCategory);

            $imgPath = $tmpProductCategory->getMainImage();
            try {
                $tmpProductCategory->setMainImage(new File("uploads/images/" . $imgPath));
            } catch (\Exception $exception) {
                $productCategoryNotImportedLog[] = [
                    'name' => $tmpProductCategory->getName(),
                    'reason' => 'File not exist'
                ];
                $numberOfErrors++;
                continue;
            }

            if (count($validator->validate($tmpProductCategory)) != 0) {
                $productCategoryNotImportedLog[] = [
                    'name' => $tmpProductCategory->getName(),
                    'reason' => 'Data not valid'
                ];
                $numberOfErrors++;
                continue;
            }
            $tmpProductCategory->setMainImage($imgPath);
            $tmpProductsCategories[] = $tmpProductCategory;
        }
        foreach ($tmpProductsCategories as $productCategory) {
            try {
                $em->persist($productCategory);
                $em->flush();
                $numberOfImportedCategories++;
            } catch (\Exception $exception) {
                $numberOfErrors++;
                $productCategoryNotImportedLog[] = [
                    'name' => $productCategory->getName(),
                    'reason' => 'Data not valid'
                ];
                continue;
            }
        }
        if ($numberOfErrors == 0) {
            return new JsonResponse(
                [
                'message' => 'Successfully imported from csv',
                'numberOfImportedCategories' => $numberOfImportedCategories
                ],
                200
            );
        } else {
            if (count($productCategoryNotImportedLog) != 0) {
                $this->logIntoFile($productCategoryNotImportedLog);
            }
            return new JsonResponse(
                [
                'message' => 'Successfully imported only '.$numberOfImportedCategories.
                    ' from '.count($productCategoriesArray),
                'notImported' => $productCategoryNotImportedLog,
                'numberOfImportedCategories' => $numberOfImportedCategories,
                ],
                201
            );
        }
    }

    private function isOnlyOneRowOfData(array $productCategoriesArray) : bool
    {
        try {
            return $productCategoriesArray['name'] != null;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function logIntoFile(array $logs) : void
    {
        $fileSystem = new Filesystem();
        if (!$fileSystem->exists('logs/productCategoryImportFromCSVLogs.txt')) {
            $fileSystem->dumpFile('logs/productCategoryImportFromCSVLogs.txt', '');
        }
        foreach ($logs as $log) {
            $fileSystem->appendToFile(
                'logs/productCategoryImportFromCSVLogs.txt',
                "[LOG] \xA date=".date("Y-m-d H:i:s")." \xA nameNotImportedCategory=". $log['name'] .
                " \xA reason=". $log['reason'] . "\xA"
            );
        }
    }
}
