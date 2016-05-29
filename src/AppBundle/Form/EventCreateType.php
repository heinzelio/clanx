<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventCreateType extends EventType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array(
                'label' => 'Bezeichnung (kurz)',
            ))
            ->add('description',TextareaType::class, array(
                    'label' => 'Beschreibung (Ausführlich)',
                    'attr' => array(
                        'rows' => 5,
                        'cols' => 80
                    ),
            ))
            ->add('date', DateType::class,array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy', // sync with datepicker.js
                'html5' => false,
                'attr' => array('class'=>'datepicker regular'),
                'label' => 'Startdatum',
            ))
            ->add('sticky', CheckboxType::class, array(
                    'label'    => 'Klebt der event in der Titelzeile?',
                    'required' => false,
                ))
            ->add('departments', TextareaType::class,array(
                'label' => 'Ressorts (1 pro Zeile)',
                'required' => false,
                'mapped' => false,
                'attr' => array(
                    'rows' => 7,
                    'cols' => 80
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Event'
        ));
    }
}
