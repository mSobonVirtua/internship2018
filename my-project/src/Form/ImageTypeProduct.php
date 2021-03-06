<?php
/**
 * VI-44 - Add and Edit gallery
 *
 * @category   Form
 * @package    Gallery
 * @copyright  Copyright (c) Virtua
 * @author     Dawid Kruczek
 */
namespace App\Form;

use App\Entity\ImageProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ImageTypeProduct extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image',FileType::class,array(
                'label' 	    => 'Add Image',
                'required' 	    => true,
                'constraints'   => array(
                    new File()
                ),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => ImageProduct::class,
        ));
    }
}