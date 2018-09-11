<?php
/**
 * VI-31 VI-36 ProductCategoryType
 *
 * @category  FormType
 * @package   Virtua_ProductCategoryType
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Form;

use App\Entity\ProductCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $options['data'];
        $builder
            ->add('name')
            ->add('description')
            ->add(
                'mainImage',
                FileType::class,
                [
                'label' => 'Main Image',
                'data_class' => null,
                'required' => $entity->getId() ? false : true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'data_class' => ProductCategory::class,
            ]
        );
    }
}
