<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class ProductType
 */
class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('info')
            ->add(
                'category',
                EntityType::class,
                array(
                'class'=>ProductCategory::class,
                'choice_label'=>function ($category) {
                    /** @var ProductCategory $category */
                    return $category->getName();
                })
            )
            ->add('picture', FileType::class, array('label'=>'Add Main Image','data_class'=>null))
            ->add(
                'images',
                CollectionType::class,
                array(
                'entry_type'        =>ImageTypeProduct::class,
                'prototype'            => true,
                'allow_add'            => true,
                'allow_delete'        => true,
                'by_reference'         => false,
                'required'            => false,
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'data_class' => Product::class,
            ]
        );
    }
}
