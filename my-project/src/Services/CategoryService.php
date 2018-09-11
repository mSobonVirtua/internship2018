<?php
/**
 * VI-31
 *
 * @category  Virtua
 * @package   Virtua_Module
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */

namespace App\Services;

use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\EntityManager;

/**
 * Class CategoryService
 */
class CategoryService
{
    /**
     * @var ProductCategoryRepository
     */
    private $pcr;

    /**
     * CategoryService constructor.
     * @param ProductCategoryRepository $pcr
     */
    public function __construct(ProductCategoryRepository $pcr)
    {
        $this->pcr = $pcr;
    }

    /**
     * @return array
     */
    public function getAllCategory()
    {
        return $this->pcr->findAll();
    }
}
