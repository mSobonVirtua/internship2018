<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 11.06.2018
 * Time: 10:25
 */

namespace App\Services;


use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\EntityManager;

class CategoryService
{
    private $pcr;

    public function __construct(ProductCategoryRepository $pcr)
    {
        $this->pcr = $pcr;
    }

    public function getAllCategory()
    {
        return $this->pcr->findAll();
    }
}