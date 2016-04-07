<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ctYear = date("Y");
        $builder
            ->add('forename')
            ->add('surname')
            ->add('sex')
            // ->add('dateOfBirth',DateType::class, array(
            //         'widget' => 'choice',
            //         'years' => range(1900,$ctYear)))
            ->add('dateOfBirth',DateType::class,array('widget' => 'single_text')) // this makes a html5 date picker. Awesom!
            ->add('street')
            ->add('zip')
            ->add('city')
            ->add('country')
            ->add('phone')
            ->add('mail')
            ->add('occupation')
            ->add('passwordHash')
            ->add('verified')
            ->add('isAdmin')
        ;
		//Symfony\Component\Debug\Debug::debug($builder->get('verified'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
}
