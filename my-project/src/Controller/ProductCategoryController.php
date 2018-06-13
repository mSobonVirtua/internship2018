<?php
/**
 * VI-31
 *
 * @category   Virtua
 * @package    Virtua_Module
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        return $this->render('product_category/index.html.twig', ['product_categories' => $productCategoryRepository->findAll()]);
    }

    /**
     * @Route("/new", name="product_category_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $productCategory = new ProductCategory();
        $date = new \DateTime();
        $date->format("Y:M:D");
        $productCategory->setDateOfCreation($date);
        $productCategory->setDateOfLastModification($date);
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productCategory);
            $em->flush();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($productCategory);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'Your category was added'
                );
            } catch (\Exception $exception) {
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
    public function show(ProductCategory $productCategory): Response
    {
        return $this->render('product_category/show.html.twig', ['product_category' => $productCategory]);
    }

    /**
     * @Route("/{id}/edit", name="product_category_edit", methods="GET|POST")
     */
    public function edit(Request $request, ProductCategory $productCategory): Response
    {
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            if ($form->isSubmitted() && $form->isValid()) {

                $date = new \DateTime();
                $date->format("Y:M:D");
                $productCategory->setDateOfLastModification($date);

                try {
                    $this->getDoctrine()->getManager()->flush();
                } catch (\Exception $exception) {
                    $this->_addDatabaseErrorFlash();
                }

                $this->addFlash(
                    'notice',
                    'Your category was updated'
                );

                return $this->redirectToRoute('product_category_edit', ['id' => $productCategory->getId()]);
            }

            return $this->render('product_category/edit.html.twig', [
                'product_category' => $productCategory,
                'form' => $form->createView()
            ]);
        }
    }

        /**
         * @Route("/{id}", name="product_category_delete", methods="DELETE")
         */
        public
        function delete(Request $request, ProductCategory $productCategory): Response
        {
            if ($this->isCsrfTokenValid('delete' . $productCategory->getId(), $request->request->get('_token'))) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($productCategory);
                $em->flush();
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($productCategory);
                    $em->flush();
                    $this->addFlash(
                        'notice',
                        'Your category was deleted'
                    );
                } catch (\Exception $exception) {
                    $this->_addDatabaseErrorFlash();
                }
            } else {
                $this->addFlash(
                    'error',
                    'Operation failed'
                );
            }

            return $this->redirectToRoute('product_category_index');
        }

        private
        function _addDatabaseErrorFlash()
        {
            $this->addFlash(
                'error',
                'Problem with the database, please try later'
            );
        }
    }

