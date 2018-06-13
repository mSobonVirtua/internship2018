<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductCategory;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;

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
                }
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
