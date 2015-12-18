<?php

namespace wizem\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VoteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
                'class' => 'wizemUserBundle:User',
                'required'=>true,
            ))
            ->add('date', 'entity', array(
                'class' => 'wizemEventBundle:Date',
                'required'=>false,
            ))
            ->add('place', 'entity', array(
                'class' => 'wizemEventBundle:Place',
                'required'=>false,
            ))
            ->add('event', 'entity', array(
                'class' => 'wizemEventBundle:Event',
                'required'=>true,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'wizem\EventBundle\Entity\Vote',
            'csrf_protection' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wizem_apibundle_vote';
    }
}
