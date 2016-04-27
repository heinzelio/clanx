<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CountryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                ''=>'',
                'Schweiz'=>'Schweiz',
                'Deutschland'=>'Deutschland',
                'Österreich'=>'Österreich',
                'Fürstentum Lichtenstein'=>'Fürstentum Lichtenstein',
                'Frankreich'=>'Frankreich',
                'Italien'=>'Italien'
            )
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

 ?>
