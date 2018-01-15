<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Question;

class QuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO: Localization
        $builder
            ->add('text',null, array(
                'label'=>'Text',
            ))
            ->add('hint',null, array(
                'label'=>'Hint',
                'required' => false,
            ))
            ->add('type',null, array(
                'label'=>'F=Yes/No, T=Text, S=Selection',
                'required' => false,
            ))
            ->add('data',TextareaType::class, array(
                    'label' => 'Data (a JSON string)',
                    'attr' => array(
                        'rows' => 5,
                        'cols' => 80
                    ),
            ))
            ->add('optional',CheckboxType::class, array(
                    'label' => 'Optional.',
                    'required' => false,
            ))
            ->add('aggregate',CheckboxType::class, array(
                    'label' => 'Use this in statistic. (Makes no sense for text questions.)',
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
            'data_class' => Question::class
        ));
    }
}
