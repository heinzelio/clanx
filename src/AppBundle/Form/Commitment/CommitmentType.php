<?php

namespace AppBundle\Form\Commitment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommitmentType extends AbstractType
{
    /**
     * @var string
     */
    const USE_DEPARTMENTS_KEY = 'use_departments';
    /**
     * @var string
     */
    const DEPARTMENT_CHOICES_KEY = 'department_choices';
    /**
     * @var string
     */
    const USE_VOLUNTEER_NOTIFICATION_KEY = 'use_volunteer_notification';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO Localization
        if ($options[CommitmentType::USE_VOLUNTEER_NOTIFICATION_KEY]) {
            $builder->add('message', TextareaType::class, array(
                         'label' => "Beim speichern diese Nachricht an den Hölfer senden:",
                         'required' => false,
                         'mapped' => false,
                         // used to copy parts of edit form to delete form
                         'attr' => array('class'=>'clx-commitment-message'),
             ))
             ->add('noMessage', CheckboxType::class, array(
                 'label' => "Keine Nachricht an den Hölfer senden.",
                 'required' => false,
                 'mapped' => false,
                 // used to copy parts of edit form to delete form
                 'attr' => array('class'=>'clx-commitment-noMessage'),
             ));
        }
        if ($options[CommitmentType::USE_DEPARTMENTS_KEY]) {
            $builder->add('department', EntityType::class, array(
                'class'=>'AppBundle:Department',
                'label' => 'Für Ressort (ohne Garantie)',
                'choices' => $options[CommitmentType::DEPARTMENT_CHOICES_KEY],
                'choice_label' => function ($dpt) {
                                        return $dpt->getLongText();
                                    }

            ));
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\ViewModel\Commitment\CommitmentViewModel',
            CommitmentType::DEPARTMENT_CHOICES_KEY => array(),
            CommitmentType::USE_DEPARTMENTS_KEY => true,
            CommitmentType::USE_VOLUNTEER_NOTIFICATION_KEY => true,
        ));

        // this throw "Array access is only supported within closures of lazy options and normalizers."
        // if ($resolver[CommitmentType::USE_DEPARTMENTS_KEY]) {
        //     $resolver->isRequired(CommitmentType::DEPARTMENT_CHOICES_KEY);
        // }
    }

}
