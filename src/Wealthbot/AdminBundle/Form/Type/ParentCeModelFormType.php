<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 31.10.12
 * Time: 17:56
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParentCeModelFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text');
//        $this->subscribe($builder);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wealthbot\AdminBundle\Entity\CeModel'
        ));
    }

    public function getName()
    {
        return 'strategy';
    }

    //TODO Should be removed in feature (not using)
    protected function subscribe(FormBuilderInterface $builder)
    {
//        $subscriber = new ParentCeModelFormTypeEventsListener();
//        $builder->addEventSubscriber($subscriber);
    }
}
