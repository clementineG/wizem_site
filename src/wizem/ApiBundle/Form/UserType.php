<?php

namespace wizem\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserType extends AbstractType
{

    private $container;
    private $facebook;
    private $udpate;

    public function __construct(ContainerInterface $container = null, $facebook = false, $udpate = false)
    {
        $this->container = $container;
        $this->facebook = $facebook;
        $this->udpate = $udpate;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('password', 'password')
        ;

        if($this->facebook == true){
            $builder
                ->add('facebookId')
                ->add('firstname')
                ->add('lastname')
                ->add('image')
            ;
        }

        if($this->udpate == true){
            $builder
                ->remove('username')
                ->remove('email')
                ->remove('password')
            ;
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {   
        $resolver->setDefaults(array(
            'data_class' => 'wizem\UserBundle\Entity\User',
            'csrf_protection' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wizem_apibundle_user';
    }
}
