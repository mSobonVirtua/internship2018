<?php
/**
 * VI-61 ProductCategoryControllerApi
 *
 * @category  ProductCategoryApi
 * @package   Virtua_ProductCategoryApi
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Controller;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Services\FileUploaderService;
use App\Services\ProductCategoryService;
use App\Services\SerializerService;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ProductCategoryApiController
 */
class ProductCategoryApiController extends Controller
{
    /**
     * @var string $targetDir
     */
    private $targetDir;

    /**
     * ProductCategoryApiController constructor.
     * @param string $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDir = $targetDirectory;
    }

    /**
     * @Route("/api/product/category/", name="product_category_api", methods="GET")
     * @param ProductCategoryRepository $productCategoryRepository
     * @param SerializerService $serializer
     * @return JsonResponse
     */
    public function indexAction(ProductCategoryRepository $productCategoryRepository, SerializerService $serializer)
    {
        /**
         * @var ProductCategory[] $allCategories
         */
        $allCategories = $productCategoryRepository->findAll();
        $data = $serializer->normalize(
            $allCategories,
            'json',
            [
            'groups' => ['ProductCategoryIndexAPI']
            ]
        );
        return new JsonResponse(
            $data,
            200
        );
    }

    /**
     * @Route("/api/product/category/{id}", name="product_category_show_api", methods="GET")
     * @param ProductCategory $productCategory
     * @param SerializerService $serializer
     * @return Response
     */
    public function showAction(ProductCategory $productCategory, SerializerService $serializer)
    {
        $data = $serializer->normalize(
            $productCategory,
            'json',
            [
            'groups' => ['ProductCategoryShowAPI']
            ]
        );
        $data['mainImage'] = '/uploads/images'."/".$data['mainImage'];
        for ($i = 0; $i < count($data['images']); $i++) {
            $data['images'][$i]['path'] = $this->targetDir."/".$data['images'][$i]['path'];
        }
        $dataJson = json_encode($data);
        $dataJson = str_replace('\/', '/', $dataJson);
        return new Response(
            $dataJson,
            200
        );
    }

    /**
     * @Route("/api/product/category/", name="product_category_new_api", methods="POST")
     * @param Request $request
     * @param FileUploaderService $fileUploader
     * @param ValidatorInterface $validator
     * @param ProductCategoryService $productCategoryService
     * @return JsonResponse
     */
    public function newAction(
        Request $request,
        FileUploaderService $fileUploader,
        ValidatorInterface $validator,
        ProductCategoryService $productCategoryService
    ) {
        $productCategory = $productCategoryService->convertJsonToProductCategory($request->getContent());
        $date = new \DateTime();
        $date->format("Y:M:D");
        $productCategory->setDateOfCreation($date);
        $productCategory->setDateOfLastModification($date);

        if (count($validator->validate($productCategory)) == 0) {
            /**
             * @var File $file
            */
            if ($productCategory->getMainImage() == null) {
                return new JsonResponse(
                    [
                    'error' => 'Image not found'
                    ],
                    404
                );
            }
            $file = $productCategory->getMainImage();
            $fileName = $fileUploader->uploadFile($file);
            $productCategory->setMainImage($fileName);

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($productCategory);
                $em->flush();
                return new JsonResponse(
                    [
                    'message' => 'Your category was added'
                    ],
                    200
                );
            } catch (\Exception $exception) {
                return new JsonResponse(
                    [
                    'error' => 'Couldn`t add new category, please try later'
                    ],
                    500
                );
            }
        }
    }

    /**
     * @Route("/api/product/category/{id}", name="product_category_delete_api", methods="DELETE")
     * @param ProductCategory $productCategory
     * @return JsonResponse
     */
    public function deleteAction(ProductCategory $productCategory)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productCategory);
            $em->flush();

            return new JsonResponse(
                [
                'message' => 'Category removed'
                ],
                200
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                'error' => 'Cannot delete category, maybe have assigned products?'
                ],
                500
            );
        }
    }

    /**
     * @Route("/api/product/category/{id}/edit", name="product_category_edit_api", methods="PUT")
     * @param Request $request
     * @param ProductCategory $productCategory
     * @param ValidatorInterface $validator,
     * @param FileUploaderService $fileUploader,
     * @param ProductCategoryService $productCategoryService
     * @return JsonResponse
     */
    public function editAction(
        Request $request,
        ProductCategory $productCategory,
        FileUploaderService $fileUploader,
        ValidatorInterface $validator,
        ProductCategoryService $productCategoryService
    ) {
        $prevMainImage = $productCategory->getMainImage();
        $newProductCategory = $productCategoryService->convertJsonToProductCategory($request->getContent());

        if (count($validator->validate($newProductCategory)) == 0) {
            $productCategory->setName($newProductCategory->getName());
            $productCategory->setDescription($newProductCategory->getDescription());
            $date = new \DateTime();
            $date->format("Y:M:D");
            $productCategory->setDateOfLastModification($date);

            if ($newProductCategory->getMainImage() != null) {
                /**
                 * @var File $file
                */
                $file = $newProductCategory->getMainImage();
                $fileName = $fileUploader->uploadFile($file);
                $productCategory->setMainImage($fileName);
            } else {
                $productCategory->setMainImage($prevMainImage);
            }

            try {
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(
                    [
                    'message' => 'Your category was edited'
                    ],
                    200
                );
            } catch (\Exception $exception) {
                return new JsonResponse(
                    [
                    'error' => 'Cannot edit category, please try later'
                    ],
                    500
                );
            }
        }

        return new JsonResponse(
            [
            'error' => 'Data are not valid'
            ],
            500
        );
    }
}
