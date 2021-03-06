<?php
/**
 * Created by JetBrains PhpStorm.
 * User: maksim
 * Date: 28.05.13
 * Time: 13:28
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\AdminBundle\Tests\Model;

use Wealthbot\AdminBundle\Model\CeModelEntity;
use Wealthbot\AdminBundle\Model\CeModel;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /** @var  CeModel */
    private $model;

    public function setUp()
    {
        $this->model = new CeModel("Model");
    }

    public function testSetNegativeRiskRating()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->model->setRiskRating(-1);
    }

    public function testSetPositiveRiskRating()
    {
        $this->model->setRiskRating(1);
        $this->assertEquals(1, $this->model->getRiskRating());
    }

    public function testSetWrongType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->model->setType(3);
    }

    public function testSetCorrectType()
    {
        $this->model->setType(CeModel::TYPE_CUSTOM);
        $this->assertEquals(CeModel::TYPE_CUSTOM, $this->model->getType());

        $this->model->setType(CeModel::TYPE_STRATEGY);
        $this->assertEquals(CeModel::TYPE_STRATEGY, $this->model->getType());
    }

    public function testGetCommissions()
    {
        $this->assertEquals(array(), $this->model->getCommissions());

        $this->model->setCommissionMin(1.3);
        $this->model->setCommissionMax(null);
        $this->assertEquals(array(1.3), $this->model->getCommissions());

        $this->model->setCommissionMin(null);
        $this->model->setCommissionMax(13.5);
        $this->assertEquals(array(13.5), $this->model->getCommissions());

        $this->model->setCommissionMin(0.2);
        $this->model->setCommissionMax(11.4);
        $this->assertEquals(array(0.2, 11.4), $this->model->getCommissions());
    }

    public function testSetForecast()
    {
        $this->model->setForecast(5);
        $this->assertEquals(5, $this->model->getForecast());
    }

    public function testSetGenerousMarketReturn()
    {
        $this->model->setGenerousMarketReturn(1.4);
        $this->assertEquals(1.4, $this->model->getGenerousMarketReturn());
    }

    public function testSetLowMarketReturn()
    {
        $this->model->setLowMarketReturn(0.6);
        $this->assertEquals(0.6, $this->model->getLowMarketReturn());
    }

    public function testHasType()
    {
        $this->model->setType(CeModel::TYPE_CUSTOM);

        $this->assertEquals(false, $this->model->hasType(CeModel::TYPE_STRATEGY));
        $this->assertEquals(true, $this->model->hasType(CeModel::TYPE_CUSTOM));
    }

    public function testIsStrategy()
    {
        $this->model->setType(CeModel::TYPE_STRATEGY);
        $this->assertEquals(true, $this->model->isStrategy());
    }

    public function testIsCustom()
    {
        $this->model->setType(CeModel::TYPE_CUSTOM);
        $this->assertEquals(true, $this->model->isCustom());
    }

    public function testCopyForOwner()
    {
        $this->model->setRiskRating(5);
        $this->model->setAssumption(array(
            'commission_min' => 100,
            'commission_max' => 1000,
            'forecast' => 25,
            'generous_market_return' => 1.3,
            'low_market_return' => 0.4
        ));

        $clonedModel = clone $this->model;

        $this->assertEquals(5, $clonedModel->getRiskRating());
        $this->assertEquals(100, $clonedModel->getCommissionMin());
        $this->assertEquals(1000, $clonedModel->getCommissionMax());
        $this->assertEquals(25, $clonedModel->getForecast());
        $this->assertEquals(1.3, $clonedModel->getGenerousMarketReturn());
        $this->assertEquals(0.4, $clonedModel->getLowMarketReturn());
    }

    public function testAddModelEntity()
    {
        $entity1 = new CeModelEntity();
        $entity1->setIsQualified(false);

        $entity2 = new CeModelEntity();
        $entity2->setIsQualified(true);

        $entity3 = new CeModelEntity();
        $entity3->setIsQualified(true);

        $this->model->addModelEntity($entity1);
        $this->model->addModelEntity($entity2);
        $this->model->addModelEntity($entity3);

        $entities = $this->model->getModelEntities();

        $this->assertEquals(3, count($entities), 'Invalid count of model entities.');
    }

    public function testRemoveModelEntity()
    {
        $entity1 = new CeModelEntity();
        $entity1->setIsQualified(false);

        $entity2 = new CeModelEntity();
        $entity2->setIsQualified(true);

        $entity3 = new CeModelEntity();
        $entity3->setIsQualified(true);

        $this->model->addModelEntity($entity1);
        $this->model->addModelEntity($entity2);
        $this->model->addModelEntity($entity3);

        $this->model->removeModelEntity($entity1);
        $this->model->removeModelEntity($entity2);

        $entities = $this->model->getModelEntities();

        $this->assertEquals(1, count($entities), 'Invalid count of model entities.');
    }

    public function testBuildGroupModelEntities()
    {
        $entity1 = new CeModelEntity();
        $entity1->setIsQualified(false);

        $entity2 = new CeModelEntity();
        $entity2->setIsQualified(true);

        $entity3 = new CeModelEntity();
        $entity3->setIsQualified(true);

        $this->model->addModelEntity($entity1);
        $this->model->addModelEntity($entity2);
        $this->model->addModelEntity($entity3);

        $this->model->buildGroupModelEntities();

        $this->assertCount(2, $this->model->getQualifiedModelEntities(), 'Invalid count of qualified model entities.');
        $this->assertCount(1, $this->model->getNonQualifiedModelEntities(), 'Invalid count of non qualified model entities.');

        $this->model->removeModelEntity($entity1);
        $this->model->removeModelEntity($entity2);

        $this->assertCount(1, $this->model->getQualifiedModelEntities(), 'Invalid count of qualified model entities after model was removed.');
        $this->assertCount(0, $this->model->getNonQualifiedModelEntities(), 'Invalid count of non qualified model entities after model was removed.');
    }
}