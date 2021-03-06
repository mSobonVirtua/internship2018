<?php
/**
 * VI15B - stworzenie nowej strony
 *
 * @category   Virtua
 * @package    Virtua_Module
 * @copyright  Copyright (c) Virtua
 * @author     msobon
 */
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    /**
     * @Route("/welcome/page-b", name="welbomeB")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'date' => date('Y:M:D')
        ]);
    }
}
