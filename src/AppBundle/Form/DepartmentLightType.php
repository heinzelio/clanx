<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

// This type must always be a subset of "DepartmentType"!
// chiefs may edit only some fields, admins may edit
// the whole entity.

class DepartmentLightType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('requirement',null,array(
                'label' => 'Anforderung (z.B. mind 18j)',
            ))
            ->add('deputyUser', null, array(
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
            'data_class' => 'AppBundle\Entity\Department'
        ));
    }
}
