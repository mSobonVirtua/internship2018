<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use App\Repository\ProductCategoryRepository;
use App\Services\FileUploaderService;
use App\Services\SerializerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductCategoryApiController extends Controller
{
    /**
     * @Route("/api/product/category/", name="product_category_api", methods="GET")
     */
    public function index(ProductCategoryRepository $productCategoryRepository, SerializerService $serializer)
    {
        /**
         * @var ProductCategory[] $allCategories
         */
        $allCategories = $productCategoryRepository->findAll();
        $data = $serializer->normalize($allCategories, 'json', [
            'groups' => ['ProductCategoryIndexAPI']
        ]);
        return new JsonResponse([
            $data
        ], 200);
    }

    /**
     * @Route("/api/product/category/{id}", name="product_category_show_api", methods="GET")
     */
    public function show(ProductCategory $productCategory, SerializerService $serializer)
    {
        $data = $serializer->normalize($productCategory, 'json', [
            'groups' => ['ProductCategoryShowAPI']
        ]);
        $data['mainImage'] = '/uploads/images'.$data['mainImage'];
        for($i = 0; $i < count($data['images']); $i++)
        {
            $data['images'][$i]['path'] = "/uploads/images/".$data['images'][$i]['path'];
        }
        $dataJson = json_encode($data);
        $dataJson = str_replace('\/', '/', $dataJson);
        return new Response(
            $dataJson
        ,200);
    }

    /**
     * @Route("/api/product/category/", name="product_category_new_api", methods="POST")
     */
    public function new(Request $request, FileUploaderService $fileUploader, ValidatorInterface $validator)
    {
        $productCategoryParameters = [];
        if($categoryJson = $request->getContent())
        {
            $productCategoryParameters = json_decode($categoryJson, true);
        }

        $productCategory = new ProductCategory();
        $productCategory->setName($productCategoryParameters['name']);
        $productCategory->setDescription($productCategoryParameters['description']);
        $productCategory->setMainImage($productCategoryParameters['mainImage']);
        $date = new \DateTime();
        $date->format("Y:M:D");
        $productCategory->setDateOfCreation($date);
        $productCategory->setDateOfLastModification($date);

        return new JsonResponse([
            'error' => (string)$validator->validate($productCategory)
        ], 200);

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
                    'error' => 'Couldn`t add new category, please try later'
                ], 500);
            }
        }
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

    /**
     * @Route("/api/product/category/{id}/edit", name="product_category_edit_api", methods="PUT")
     */
    public function edit(Request $request, ProductCategory $productCategory, FileUploaderService $fileUploader, ValidatorInterface $validator)
    {
        $productCategoryParameters = [];
        if($categoryJson = $request->getContent())
        {
            $productCategoryParameters = json_decode($categoryJson, true);
        }

        $productCategory->setName($productCategoryParameters['name']);
        $productCategory->setDescription($productCategoryParameters['description']);
        $productCategory->setMainImage($productCategoryParameters['mainImage']);
        $date = new \DateTime();
        $date->format("Y:M:D");
        $productCategory->setDateOfCreation($date);
        $productCategory->setDateOfLastModification($date);

        $prevMainImage = $productCategory->getMainImage();

        return new JsonResponse([
            'error' => (string)$validator->validate($productCategory)
        ], 500);

        if (count($validator->validate($productCategory)) == 0)
        {

            $date = new \DateTime();
            $date->format("Y:M:D");
            $productCategory->setDateOfLastModification($date);

            if($productCategoryParameters['mainImage'] != null){
                /** @var UploadedFile $file */
                $file = $productCategory->getMainImage();
                $fileName = $fileUploader->upload($file);
                $productCategory->setMainImage($fileName);
            }else{
                /** @var ProductCategory $tmpProdCategory */
                $productCategory->setMainImage($prevMainImage);
            }

            try
            {
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse([
                    'message' => 'Your category was edited'
                ], 200);
            }
            catch(\Exception $exception)
            {
                return new JsonResponse([
                    'error' => 'Form is not valid'
                ], 500);
            }
        }

        return new JsonResponse([
            'error' => 'Data are not valid'
        ], 500);
    }
}
