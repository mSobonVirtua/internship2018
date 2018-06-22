<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 22.06.2018
 * Time: 14:47
 */

namespace App\Services;


use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerService
{
    private $_classMetaDataFactory;
    private $_normalizer;
    private $_serializer;

     public function __construct()
     {
         $this->_classMetaDataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
         $this->_normalizer = $normalizer = new PropertyNormalizer($this->_classMetaDataFactory);
         $normalizer->setCircularReferenceHandler(function($product){
             return $product->getName();
         });
         $this->_serializer = new Serializer([$normalizer, new DateTimeNormalizer()], [new JsonEncoder()]);
     }

     public function normalize($object, $format, $options)
     {
        return $this->_serializer->normalize($object, $format, $options);
     }
}