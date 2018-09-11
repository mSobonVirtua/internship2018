<?php
/**
 * VI-40 WishListController
 *
 * @category  WishList
 * @package   Virtua_WishList
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $this->checkIfWishListIsInSession($session);
        /***
         * @var array $wishList
        */
        $wishList = $session->get('wishList');
        $productsFromWishList = [];
        foreach ($wishList as $wish) {
            $product = $productRepository->find($wish);
            array_push($productsFromWishList, $product);
        }

        return $this->render(
            'wish_list/index.html.twig',
            [
            'wish_list' => $productsFromWishList
            ]
        );
    }

    /**
     * @Route("/new/{productId}", name="WishListNew", methods="GET|POST")
     */
    public function addToWishList(Request $request, SessionInterface $session)
    {
        $this->checkIfWishListIsInSession($session);
        /***
         * @var array $wishList
        */
        $wishList = $session->get("wishList");
        $productId = $request->get("productId");
        if (count($wishList) == 5) {
            $this->addFlash(
                'error',
                'You cannot have more than 5 wishes.'
            );
            return new RedirectResponse($request->headers->get('referer'));
        }
        array_push($wishList, $productId);
        $session->set("wishList", $wishList);
        $this->addFlash(
            'notice',
            'Your wish is added to list'
        );
        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/delete", name="WishListDeleteAll", methods="GET|POST")
     */
    public function removeAllWishes(Request $request, SessionInterface $session)
    {
        $this->checkIfWishListIsInSession($session);
        $session->set("wishList", []);
        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/delete/{id}", name="WishListDelete", methods="GET|POST")
     */
    public function removeWish(Request $request, SessionInterface $session)
    {
        $this->checkIfWishListIsInSession($session);
        /***
         * @var array $wishList
        */
        $wishList = $session->get("wishList");
        $id = $request->get("id");
        for ($i = 0; count($wishList); $i++) {
            if ($wishList[$i] == $id) {
                array_splice($wishList, $i, 1);
                break;
            }
        }
        $session->set("wishList", $wishList);
        return new RedirectResponse($request->headers->get('referer'));
    }

    private function checkIfWishListIsInSession(SessionInterface $session)
    {
        if (!$session->isStarted()) {
            $session->start();
        }
        if (!$session->has("wishList")) {
            $session->set("wishList", []);
        }
    }
}
