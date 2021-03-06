<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 17.06.13
 * Time: 17:27
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\AdminBundle\Form\Handler;

use Wealthbot\AdminBundle\Entity\CeModel;
use Wealthbot\AdminBundle\Manager\CeModelManager;

class ModelAssumptionFormHandler extends AbstractFormHandler
{
    protected function success()
    {
        $modelManager = new CeModelManager($this->em, '\Wealthbot\AdminBundle\Entity\CeModel');

        /** @var CeModel $parentModel */
        $parentModel = $this->form->getData();
        $parentModel->setIsAssumptionLocked(true);

        $this->em->persist($parentModel);

        /** @var CeModel $model */
        foreach ($modelManager->getChildModels($parentModel) as $model) {

            if ($model->getIsAssumptionLocked() || $model->getIsDeleted()) {
                continue;
            }

            $model->setAssumption($parentModel->getAssumption());

            $this->em->persist($model);
        }

        $this->em->flush();
    }
}