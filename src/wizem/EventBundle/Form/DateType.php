<?php

namespace wizem\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
                'attr' => array('class' => 'datepicker' ),
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'wizem\EventBundle\Entity\Date',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wizem_eventbundle_date';
    }
}
