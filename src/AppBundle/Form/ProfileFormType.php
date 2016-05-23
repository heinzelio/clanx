<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form;

use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildUserForm($builder, $options);

        // $builder->add('current_password', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'), array(
        //     'label' => 'form.current_password',
        //     'translation_domain' => 'FOSUserBundle',
        //     'mapped' => false,
        //     'constraints' => new UserPassword(),
        // ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'csrf_token_id' => 'profile',
            // BC for SF < 2.8
            'intention'  => 'profile',
        ));
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    // BC for SF < 3.0
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'fos_user_profile';
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('forename', null, array(
                'label' => 'Vorname',
            ))
            ->add('surname', null, array(
                'label' => 'Nachname',
            ))
            ->add('gender', GenderType::class, array(
                'label' => 'Geschlecht',
            ))
            ->add('dateOfBirth', BirthdayType::class, array(
                'widget' => 'single_text',
                'label' => 'Geburtsdatum',
                'required' => false,
            ))
            ->add('street', null, array(
                'label' => 'Strasse',
                'required' => false,
            ))
            ->add('zip', null, array(
                'label' => 'PLZ',
                'required' => false,
            ))
            ->add('city', null, array(
                'label' => 'Ort',
                'required' => false,
            ))
            ->add('country', CountryType::class, array(
                'label' => 'Land',
                'required' => false,
            ))
            ->add('phone', null, array(
                'label' => 'Telefon',
                'required' => false,
            ))
            ->add('occupation', null, array(
                'label' => 'Beruf / Fachbereich',
                'required' => false,
            ))
            ->add('username', null, array(
                'label' => 'form.username',
                'translation_domain' => 'FOSUserBundle',
            ))
            ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array(
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
            ))
        ;
    }
}
