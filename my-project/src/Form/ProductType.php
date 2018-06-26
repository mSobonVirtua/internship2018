<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Image;
use App\Entity\ProductCategory;
use Doctrine\ORM\Mapping\Entity;
use function Sodium\add;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('info')
            ->add('category',EntityType::class, array(
                'class'=>ProductCategory::class,
                'choice_label'=>function($category)
                {
                    return $category->getName();
                }))
            ->add('picture',FileType::class, array('label'=>'Dodaj obrazek glowny','data_class'=>null))
            ->add('images',CollectionType::class, array(
                'by_reference'=>false,
                'prototype'=>true,
                'entry_type'=>EntityType::class,
                'entry_options'=>
                    array(
                        'class'=>Image::class
                    )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
