<?php

namespace Wealthbot\UserBundle\Service;


use Doctrine\ORM\EntityManager;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\GenericSerializationVisitor;
use Wealthbot\ClientBundle\Manager\CashCalculationManager;
use Wealthbot\UserBundle\Entity\User;

class SerializerSubscriber implements EventSubscriberInterface{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CashCalculationManager
     */
    private $cashManager;

    public function __construct(EntityManager $em, CashCalculationManager $cashManager)
    {
        $this->em = $em;
        $this->cashManager = $cashManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => Events::POST_SERIALIZE, 'method' => 'onSerializeUser', 'class' => 'Wealthbot\UserBundle\Entity\User', 'format' => 'json')
        );
    }

    public function onSerializeUser(ObjectEvent $event)
    {
        $object = $event->getObject();
        /** @var GenericSerializationVisitor $visitor */
        $visitor = $event->getVisitor();
        $groups = $event->getContext()->attributes->get('groups')->get();
        if ($object instanceof User) {
            //Cash Portfolio value
            if (in_array('summaryTable', $groups)) {
                $portfolioValue = $this->em->getRepository('WealthbotClientBundle:ClientAccount')->getAccountsSum($object);
                $visitor->addData('portfolioValue', $portfolioValue);
            }
            if ($object->additionalSerializerFields) {
                foreach($object->additionalSerializerFields as $key => $value){
                    $visitor->addData($key, $value);
                }
            }
        }
    }

} 