<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommitmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /*
        id                | int(11)       | NO   | PRI | NULL    | auto_increment |
       | user_id           | int(11)       | YES  | MUL | NULL    |                |
       | event_id          | int(11)       | YES  | MUL | NULL    |                |
       | department_id     | int(11)       | YES  | MUL | NULL    |                |
       | remark            | varchar(1000) | YES  |     | NULL    |                |
       | possible_start    | varchar(200)  | YES  |     | NULL    |                |
       | shirt_size        | varchar(10)   | YES  |     | NULL    |                |
       | need_train_ticket
        */
        $builder
        ->add('department', ChoiceType::class, array(
            'label' => 'Für Ressort (ohne Garantie)',
            'choices' => $options['departmentChoices'],
        ))
        ->add('possibleStart', TextType::class, array(
            'label' => 'Frühestes Startdatum & Zeit',
        ))
        ->add('shirtSize', ShirtSizeType::class, array(
            'label' => 'TShirt Grösse',
        ))
        ->add('needTrainTicket', CheckboxType::class, array(
            'label' => 'Ich brauche ein Zugbillet',
            'required' => false,
        ))
        ->add('remark', TextareaType::class, array(
            'label' => "Bemerkung / Wunsch",
            'required' => false
        ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Commitment',
            'departmentChoices' => array()
        ));

        $resolver->isRequired('departmentChoices');
    }
}
