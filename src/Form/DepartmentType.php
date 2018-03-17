<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use App\Entity\Department;

class DepartmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('requirement',null,array(
                'label' => 'Anforderung (z.B. mind 18j)',
            ))
            ->add('chiefUser',null,array(
                'label' => 'Ressortleiter',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC')
                        ->orderBy('u.surname', 'ASC')
                        ->orderBy('u.forename', 'ASC')
                        ;
                    }
            ))
            ->add('deputyUser',null,array(
                'label' => 'Stellvertreter',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC')
                        ->orderBy('u.surname', 'ASC')
                        ->orderBy('u.forename', 'ASC')
                        ;
                    }
            ))
            ->add('locked', CheckboxType::class, array(
                'label' => 'Einschreiben gesperrt?',
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
            'data_class' => Department::class
        ));
    }
}
