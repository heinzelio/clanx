<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('forename', null, array(
                'label'=>'Vorname'
            ))
            ->add('surname', null, array(
                'label'=>'Nachname'
            ))
            ->add('gender', GenderType::class, array(
                'label'=>'Geschlecht'
            ))
            ->add('dateOfBirth', BirthdayType::class,array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy', // sync with datepicker.js
                'html5' => false,
                'attr' => array('class'=>'datepicker birthday'),
                'label' => 'Geburtsdatum',
                'required' => false,
            ))
            ->add('street', null, array(
                'label'=>'Adresse',
                'required' => false,
            ))
            ->add('zip', null, array(
                'label'=>'PLZ',
                'required' => false,
            ))
            ->add('city', null, array(
                'label'=>'Ort',
                'required' => false,
            ))
            ->add('country', CountryType::class, array(
                'label'=>'Land',
                'required' => false,
            ))
            ->add('phone', null, array(
                'label'=>'Telefonnummer'
            ))
            ->add('occupation', null, array(
                'label'=>'Beruf / Fachbereich',
                'required' => false,
            ))
        ;
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
