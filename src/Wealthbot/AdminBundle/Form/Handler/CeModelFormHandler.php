<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 06.03.13
 * Time: 19:34
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\AdminBundle\Form\Handler;


use Wealthbot\AdminBundle\Entity\CeModel;
use Symfony\Component\HttpFoundation\Request;

class CeModelFormHandler extends AbstractFormHandler
{
    protected function success()
    {
        $isShowAssumption = $this->getOption('is_show_assumption');

        /** @var $ceModel CeModel */
        $ceModel = $this->form->getData();

        if ($isShowAssumption) {
            $ceModel->setIsAssumptionLocked(true);
        }

        $this->em->persist($ceModel);
        $this->em->flush();
   }
}