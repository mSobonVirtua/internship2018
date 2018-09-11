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

class SerializerService
{
    private $classMetaDataFactory;
    private $normalizer;
    private $serializer;

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

    public function normalize($object, $format, $options)
    {
        return $this->serializer->normalize($object, $format, $options);
    }

    public function serialize($data, $format)
    {
        return $this->serializer->serialize($data, $format);
    }

    public function encode($data, $format)
    {
        return $this->serializer->encode($data, $format);
    }

    public function decode($data, $format)
    {
        return $this->serializer->decode($data, $format);
    }
}
