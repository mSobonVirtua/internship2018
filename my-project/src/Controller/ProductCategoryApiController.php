<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use App\Repository\ProductCategoryRepository;
use App\Services\FileUploaderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ProductCategoryApiController extends Controller
{
    /**
     * @Route("/api/product/category/", name="product_category_api", methods="GET")
     */
    public function index(ProductCategoryRepository $productCategoryRepository)
    {
        /**
         * @var ProductCategory[] $allCategories
         */
        $allCategories = $productCategoryRepository->findAll();
        $categoriesPreparedToSend = [];
        foreach ($allCategories as $category)
        {
            $categoriesPreparedToSend[] = [
                'id' => $category->getId(),
                'name' => $category->getName()
            ];
        }
        return new JsonResponse([
            'categories' => $categoriesPreparedToSend
        ], 200);
    }

    /**
     * @Route("/api/product/category/{id}", name="product_category_show_api", methods="GET")
     */
    public function show(ProductCategory $productCategory)
    {
        $productsPrepared = [];
        foreach ($productCategory->getProducts() as $product)
        {
            $productsPrepared[] = [
                'id' => $product->getId(),
                'name' => $product->getName()
            ];
        }

        //404 not Found TODO
        return new JsonResponse([
            'id' => $productCategory->getId(),
            'name' => $productCategory->getName(),
            'description' => $productCategory->getDescription(),
            'dateOfCreation' => $productCategory->getDateOfCreation(),
            'dateOfLastModification' => $productCategory->getDateOfLastModification(),
            'products' => $productsPrepared,
            'mainImage' => $productCategory->getMainImage(),
            'images' => $productCategory->getImages()
        ], 200);
    }

    /**
     * @Route("/api/product/category/", name="product_category_new_api", methods="POST")
     */
    public function new(Request $request, FileUploaderService $fileUploader)
    {
        $productCategory = new ProductCategory();
        $date = new \DateTime();
        $date->format("Y:M:D");
        $productCategory->setDateOfCreation($date);
        $productCategory->setDateOfLastModification($date);

        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $file */
            $file = $productCategory->getMainImage();
            $fileName = $fileUploader->upload($file);
            $productCategory->setMainImage($fileName);

            try
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($productCategory);
                $em->flush();
                return new JsonResponse([
                    'message' => 'Your category was added'
                ], 201);
            }
            catch(\Exception $exception)
            {
                return new JsonResponse([
                    'error' => 'Couldn`t add new categiry, please try later'
                ], 500);
            }
        }

        return new JsonResponse([
            'error' => 'Form is not valid'
        ], 500);
    }

    /**
     * @Route("/api/product/category/{id}", name="product_category_delete_api", methods="DELETE")
     */
    public function delete(ProductCategory $productCategory)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productCategory);
            $em->flush();

            return new JsonResponse([
                'message' => 'Category removed'
            ], 200);
        }
        catch(\Exception $exception)
        {
            return new JsonResponse([
                'error' => 'Cannot delete category, maybe have assigned products?'
            ], 500);
        }
    }
}
