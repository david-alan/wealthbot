<?php
namespace Test\RiaBundle;
use Wealthbot\RiaBundle\RiskManagement\BaselinePortfolio;

/****
 * BVA: check the boundary values of the suggested portfolio
 *
 */
class BaselinePortfolioTest extends \PHPUnit_Framework_TestCase {

    /**
     * array(array(risk_index, stocks/bonds))
     */
    public function BvaDataProvider(){
        return array(
            array(9,  '20/80'),
            array(10, '30/70'),
            array(14, '30/70'),
            array(15, '60/40'),
            array(19, '60/40'),
            array(20, '70/30'),
            array(24, '70/30'),
            array(25, '80/20'),
        );
    }

    /**
     * @dataProvider BvaDataProvider
     */
    public function testGetSuggestedPortfolioTwentyEighty($riskIndex, $recommendedInvestment) {
        $this->assertEquals(BaselinePortfolio::getInitialSuggestedPortfolio($riskIndex), $recommendedInvestment);
    }
}
