<?php
/**
 * VI-40 WishListController
 *
 * @category   WishList
 * @package    Virtua_WishList
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WishListController extends Controller
{
    /**
     * @Route("/wish/list", name="wish_list")
     */
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        if(!$session->isStarted()) $session->start();
        if(!$session->has("wishList"))
        {
            $session->set("wishList", []);
        }
        /*** @var array $wishList */
        $wishList = $session->get('wishList');
        $productsFromWishList = [];
        foreach($wishList as $wish)
        {
            $product = $productRepository->find($wish);
            array_push($productsFromWishList , $product);
        }

        return $this->render('wish_list/index.html.twig', [
            'wish_list' => $productsFromWishList
        ]);
    }

    /**
     * @Route("/new/{productId}", name="WishListNew", methods="GET|POST")
     */
    public function addToWishList(Request $request, SessionInterface $session)
    {
        if(!$session->isStarted()) $session->start();
        if(!$session->has("wishList"))
        {
            $session->set("wishList", []);
        }

        /*** @var array $wishList */
        $wishList = $session->get("wishList");
        $productId = $request->get("productId");
        array_push($wishList, $productId);
        $session->set("wishList", $wishList);
        return new RedirectResponse($request->headers->get('referer'));
    }

}
