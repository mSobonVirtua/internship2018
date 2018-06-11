<?php
/**
 * VI-31
 *
 * @category   Virtua
 * @package    Virtua_Module
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
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