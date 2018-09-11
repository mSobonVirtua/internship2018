<?php
/**
 * VI-61 SerializerService
 *
 * @category  Serializer
 * @package   Virtua_SerializerService
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */

namespace App\Services;

use App\Entity\Product;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class SerializerService
 */
class SerializerService
{
    /**
     * @var ClassMetadataFactory
     */
    private $classMetaDataFactory;

    /**
     * @var PropertyNormalizer
     */
    private $normalizer;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * SerializerService constructor.
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct()
    {
        $this->classMetaDataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->normalizer = $normalizer = new PropertyNormalizer($this->classMetaDataFactory);
        $normalizer->setCircularReferenceHandler(
            function ($product) {
                /**
                * @var Product $product
                */
                return $product->getName();
            }
        );
        $this->serializer = new Serializer(
            [$normalizer, new DateTimeNormalizer()],
            [new JsonEncoder(), new CsvEncoder()]
        );
    }

    /**
     * @param mixed $object
     * @param string $format
     * @param array $options
     * @return array
     */
    public function normalize($object, string $format, array $options)
    {
        return $this->serializer->normalize($object, $format, $options);
    }

    /**
     * @param array $data
     * @param string $format
     * @return string
     */
    public function serialize(array $data, string $format)
    {
        return $this->serializer->serialize($data, $format);
    }

    /**
     * @param array $data
     * @param string $format
     * @return string
     */
    public function encode(array $data, string $format)
    {
        return $this->serializer->encode($data, $format);
    }

    /**
     * @param string $data
     * @param string $format
     * @return array
     */
    public function decode(string $data, string $format)
    {
        return $this->serializer->decode($data, $format);
    }
}
