<?php

namespace wizem\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use wizem\EventBundle\Form\DateType;
use wizem\EventBundle\Form\PlaceType;
use wizem\EventBundle\Entity\Typeevent;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typeevent', 'entity', array(
                'class' => 'wizemEventBundle:Typeevent',
                'label' => "Type d'événement", 
                'required' => true, 
                'multiple' => false, 
                'expanded' => true, 
            ))
            ->add('description')
            ->add('date','collection',array(
                'type'=> new DateType(),
                'label'=> ' ', 
                'attr' => array('class' => '' ),
                'allow_add' => true,
                'required' => true, 
            ))
            ->add('place','collection',array(
                'type'=> new PlaceType(),
                'label'=> ' ', 
                'attr' => array('class' => '' ),
                'allow_add' => true,
                'required' => true, 
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'wizem\EventBundle\Entity\Event',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wizem_eventbundle_event';
    }
}
