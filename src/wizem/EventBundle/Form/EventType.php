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
                'label' => 'Type', 
                'required' => true, 
                'multiple' => false, 
                'expanded' => true, 
            ))
            ->add('description')
        ;

        $formModifier = function (FormInterface $form, Typeevent $typeevent = null) {
           // var_dump($typeevent);exit();
            if($typeevent == 1){
                $form->add('date','collection',array(
                    'type'=> new DateType(),
                    'label'=> ' ', 
                    'attr' => array('class' => '' ),
                    'allow_add' => true,
                ));
            }else{
                $form->add('place','collection',array(
                    'type'=> new PlaceType(),
                    'label'=> ' ', 
                    'attr' => array('class' => '' ),
                    'allow_add' => true,
                ));
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getTypeevent());
            }
        );

        $builder->get('typeevent')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $typeevent = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $typeevent);
            }
        );        
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
