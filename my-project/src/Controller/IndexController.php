<?php
/**
 * VI15B - stworzenie nowej strony
 *
 * @category  Virtua
 * @package   Virtua_Module
 * @copyright Copyright (c) Virtua
 * @author    msobon
 */
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class IndexController
 */
class IndexController extends Controller
{
    /**
     * @Route("/welcome/page-b", name="welbomeB")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render(
            'index/index.html.twig',
            [
            'date' => date('Y:M:D')
            ]
        );
    }
}
