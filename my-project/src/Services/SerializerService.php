<?php
/**
 * VI-61 SerializerService
 *
 * @category   Serializer
 * @package    Virtua_SerializerService
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */

namespace App\Services;


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
         $this->_serializer = new Serializer([$normalizer, new DateTimeNormalizer()], [new JsonEncoder(), new CsvEncoder()]);
     }

     public function normalize($object, $format, $options)
     {
        return $this->_serializer->normalize($object, $format, $options);
     }

     public function serialize($data, $format){
         return $this->_serializer->serialize($data, $format);
     }
}