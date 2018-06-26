<?php
/**
 * VI-31 ProductCategoryController
 *
 * @category   ProductCategory
 * @package    Virtua_ProductCategoryController
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Controller;

use App\Entity\Image;
use App\Entity\ProductCategory;
use App\Form\ImageType;
use App\Form\ProductCategoryType;
use App\Repository\ImageRepository;
use App\Repository\ProductCategoryRepository;
use App\Services\FileUploaderService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * @Route("/product/category")
 */
class ProductCategoryController extends Controller
{
    /**
     * @Route("/", name="product_category_index", methods="GET")
     */
    public function index(ProductCategoryRepository $productCategoryRepository): Response
    {
        $AllCategories = $productCategoryRepository->findAll();
        $filesystem = new Filesystem();
        $imagesFolderPath = "uploads/images/";
        /** @var ProductCategory $category */
        foreach ($AllCategories as $category)
        {
            /** @var string $mainImagePath */
            $mainImagePath = $imagesFolderPath.$category->getMainImage();
            if(!$filesystem->exists($mainImagePath))
            {
                $imagesGallery = $category->getImages();
                if($imagesGallery[0] != null && $filesystem->exists($imagesFolderPath.$imagesGallery[0]->getPath()))
                {
                    $category->setMainImage($imagesGallery[0]->getPath());

                }else
                {
                   $category->setMainImage("categoryPlaceholder.jpg");
                }
                $this->getDoctrine()->getManager()->flush();
            }
        }
        return $this->render('product_category/index.html.twig', ['product_categories' => $AllCategories]);
    }

    /**
     * @Route("/new", name="product_category_new", methods="GET|POST")
     */
    public function new(Request $request, FileUploaderService $fileUploader): Response
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

                $this->addFlash(
                    'notice',
                    'Your category was added'
                );
            }
            catch(\Exception $exception)
            {
                $this->_addDatabaseErrorFlash();
            }

            return $this->redirectToRoute('product_category_index');
        }

        return $this->render('product_category/new.html.twig', [
            'product_category' => $productCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_category_show", methods="GET")
     */
    public function show(Request $request, ProductCategory $productCategory): Response
    {
        $viewType = $request->query->get("viewType");
        if(!$viewType) $viewType = "list";
        return $this->render('product_category/show.html.twig', [
            'product_category' => $productCategory,
            'viewType' => $viewType
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_category_edit", methods="GET|POST")
     */
    public function edit(Request $request, ProductCategory $productCategory, FileUploaderService $fileUploader): Response
    {
        $prevMainImage = $productCategory->getMainImage();
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $date = new \DateTime();
            $date->format("Y:M:D");
            $productCategory->setDateOfLastModification($date);

            if($form->get('mainImage')->getData() != null){
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
                $this->addFlash(
                    'notice',
                    'Your category was updated'
                );
            }
            catch(\Exception $exception)
            {
               $this->_addDatabaseErrorFlash();
            }

            return $this->redirectToRoute('product_category_edit', ['id' => $productCategory->getId()]);
        }

        $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image);

        return $this->render('product_category/edit.html.twig', [
            'product_category' => $productCategory,
            'form' => $form->createView(),
            'imageForm' => $imageForm->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="product_category_delete", methods="DELETE")
     */
    public function delete(Request $request, ProductCategory $productCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productCategory->getId(), $request->request->get('_token'))) {
            try
            {
                $em = $this->getDoctrine()->getManager();
                $em->remove($productCategory);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Your category was deleted'
                );
            }
            catch(\Exception $exception)
            {
                $this->_addDatabaseErrorFlash();
            }
        }
        else{
            $this->addFlash(
                'error',
                'Operation failed'
            );
        }

        return $this->redirectToRoute('product_category_index');
    }


    /**
     * @Route("/{id}/edit/AddImage", name="UploadAndAddImageToCategory", methods="POST")
     */
    public function UploadAndAddImageToCategory(Request $request, ProductCategory $productCategory, FileUploaderService $fileUploader)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->get('path')->isValid())
        {
            /** @var UploadedFile $file */
            $file = $form->get('path')->getData();
            $fileName = $fileUploader->upload($file);
            $image->setPath($fileName);
            $productCategory->getImages()->add($image);
            try
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($image);
                $em->flush();
            }
            catch(\Exception $exception)
            {
                return new JsonResponse([
                    'error' => 'Couldn`t add image, please try later'
                ], 500);
            }
        }

        return new JsonResponse([
            'message' => 'Image uploaded'
        ], 200);
    }

    /**
     * @Route("/{image}/{category}", name="remove_image_from_category", methods="DELETE")
     */
    public function removeImageFromCategory(Request $request, Image $image, ProductCategory $category): Response
    {

            try
            {
                $em = $this->getDoctrine()->getManager();
                $category->getImages()->removeElement($image);
                $em->flush();
            }
            catch(\Exception $exception)
            {
                return new JsonResponse([
                    'error' => 'Couldn`t remove image, please try later'
                ], 500);
            }

        return new JsonResponse([
            'message' => 'Image removed'
        ], 200);
    }



    private function _addDatabaseErrorFlash()
    {
        $this->addFlash(
            'error',
            'Problem with the database, please try later'
        );
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

}
