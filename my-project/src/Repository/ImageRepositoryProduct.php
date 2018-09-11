<?php
/**
 * VI-44 - Add and Edit gallery
 *
 * @category  Repository
 * @package   Gallery
 * @copyright Copyright (c) Virtua
 * @author    Dawid Kruczek
 */
namespace App\Repository;

use App\Entity\ImageProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ImageProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageProduct[]    findAll()
 * @method ImageProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepositoryProduct extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImageProduct::class);
    }
}
