<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ShirtSizeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'XS' => 'XS',
                'S' => 'S',
                'M' => 'M',
                'L' => 'L',
                'XL' => 'XL',
                'XXL' => 'XXL',
            )
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

 ?>
