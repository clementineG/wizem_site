<?php

namespace wizem\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', 'password', array(
                'translation_domain' => 'FOSUserBundle',
                'label' => 'form.password',
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            // ->add('notification', 'choice', array(
            //     'label' => 'Notifications', 
            //     'required' => false, 
            //     'multiple' => true, 
            //     'expanded' => true, 
            // ))
        ;
    }
    
    /**
    *   Load FOSUser fields
    */
    // public function getParent()
    // {
    //     return 'fos_user_registration';
    // }

    // Remove if getParent() is defined
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'wizem\UserBundle\Entity\user',
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'wizem_user_registration';
    }
}
