<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 19.07.13
 * Time: 17:37
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\ClientBundle\EventListener;


use Wealthbot\ClientBundle\ClientEvents;
use Wealthbot\ClientBundle\Event\WorkflowEvent;
use Wealthbot\ClientBundle\Manager\WorkflowManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkflowEventListener implements EventSubscriberInterface
{
    /** @var \Wealthbot\ClientBundle\Manager\WorkflowManager  */
    private $wm;

    public function __construct(WorkflowManager $wm)
    {
        $this->wm = $wm;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ClientEvents::CLIENT_WORKFLOW => 'createWorkflow'
        );
    }

    public function createWorkflow(WorkflowEvent $event)
    {
        $workflow = $this->wm->createWorkflow(
            $event->getClient(),
            $event->getObject(),
            $event->getType(),
            $event->getSignatures(),
            $event->getObjectIds()
        );

        $event->setData($workflow);
    }
}