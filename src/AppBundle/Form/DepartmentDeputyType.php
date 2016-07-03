<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class DepartmentDeputyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
