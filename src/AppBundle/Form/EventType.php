<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null, array(
                'label'=>'Name',
            ))
            ->add('isForAssociationMembers',CheckboxType::class, array(
                    'label' => 'Für Vereinsmitglieder. Nur Benutzer die Mitglied des Vereins sind können diesen Event sehen.',
                    'required' => false,
            ))
            ->add('description',TextareaType::class, array(
                    'label' => 'Beschreibung',
                    'attr' => array(
                        'rows' => 5,
                        'cols' => 80
                    ),
            ))
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy', // sync with datepicker.js
                'html5' => false,
                'attr' => array('class'=>'datepicker regular'),
                'label' => 'Startdatum',
            ))
            ->add('sticky', CheckboxType::class, array(
                    'label'    => 'Klebt der Event in der Titelzeile?',
                    'required' => false,
            ))
            ->add('locked', CheckboxType::class, array(
                    'label' => 'Sperren. Hölfer können ihre Eintragungen nicht mehr ändern oder löschen.',
                    'required' => false,
            ));
        ;
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
