<?php

namespace wizem\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FriendshipType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('friend','text',array(
                'label'=> "Pseudo de l'ami :", 
                'attr' => array('class' => '' ),
                'required' => true, 
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'wizem\UserBundle\Entity\Friendship'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wizem_userbundle_friendship';
    }
}
