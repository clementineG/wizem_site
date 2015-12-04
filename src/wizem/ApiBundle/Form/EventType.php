<?php

namespace wizem\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventType extends AbstractType
{

    private $container;
    private $method;

    public function __construct(ContainerInterface $container = null, $method = null)
    {
        $this->container = $container;
        $this->method = $method;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($this->method == "PUT"){
            $builder
                ->add('description')
            ;
        }else{
            $builder
                ->add('typeevent')
            ;
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'wizem\EventBundle\Entity\Event',
            'csrf_protection' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wizem_apibundle_event';
    }
}
