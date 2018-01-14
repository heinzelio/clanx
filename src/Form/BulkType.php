<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BulkEntryType;
use App\Entity\Bulk;
use App\Entity\BulkEntry;


class BulkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('entries', CollectionType::class, array(
            'entry_type' => BulkEntryType::class
        ));

        $builder->add('action', ChoiceType::class, array(
            'choices' => $options['choices']
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Bulk::class,
            // you should pass choices in the options array, when you call
            // $this->createForm from your controller.
            'choices' => array('fd2b5168-f810-446c-af95-d3497ef75dd3'=>'fd2b5168-f810-446c-af95-d3497ef75dd3')
        ));
    }
}
