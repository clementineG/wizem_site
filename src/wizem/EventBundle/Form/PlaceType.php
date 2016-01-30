<?php

namespace wizem\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlaceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adress', 'text', array(
                // 'class' => 'wizemEventBundle:Typeevent',
                // 'label' => 'Type', 
                // 'required' => true, 
                // 'multiple' => false, 
                // 'expanded' => true, 
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'wizem\EventBundle\Entity\Place',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wizem_eventbundle_place';
    }
}
