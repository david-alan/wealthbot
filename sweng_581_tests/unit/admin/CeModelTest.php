<?php
namespace Test\AdminBundle;
use Wealthbot\AdminBundle\Model\CeModel;

class CeModelTest extends \PHPUnit_Framework_TestCase {

    public function testRiskRating()
    {
        $expectedRating = 10;
        $this->ce->setRiskRating($expectedRating);
        $actualRating = $this->ce->getRiskRating();
        $this->assertEquals($expectedRating,$actualRating);
    }

    /**
     * @expectedException     \InvalidArgumentException
     */
    public function testInvalidRiskRating()
    {
        $rating = -1;
        $this->ce->setRiskRating($rating);
    }

    public function testSetTypeStrategy()
    {
        $validTypeStrategy = $expectedStrategy = 1;
        $this->ce->setType($validTypeStrategy);
        $this->assertEquals($expectedStrategy, $this->ce->getType());
    }

    /**
     * @expectedException     \InvalidArgumentException
     */
    public function testSetTypeCustom()
    {
        $validTypeStrategy = $expectedStrategy = 3;
        $this->ce->setType($validTypeStrategy);
        $this->assertEquals($expectedStrategy, $this->ce->getType());
    }

    public function testSetInvalidType()
    {
        $validTypeStrategy = $expectedStrategy = 2;
        $this->ce->setType($validTypeStrategy);
        $this->assertEquals($expectedStrategy, $this->ce->getType());
    }

    public function testGetCommissions()
    {
        $expectedLimits = array(1,10);
        $this->setCommissions();
        $commissionLimits = $this->ce->getCommissions();
        $this->assertEquals($expectedLimits,$commissionLimits);
    }

    public function setUp()
    {
        $this->ce = new CeModel();
    }

    private function setCommissions()
    {
        $this->ce->setCommissionMin(1);
        $this->ce->setCommissionMax(10);
    }
}