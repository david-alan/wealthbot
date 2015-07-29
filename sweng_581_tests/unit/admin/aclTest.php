<?php
namespace Test\AdminBundle;

use Wealthbot\AdminBundle\Model\Acl;
use Symfony\Component\Security\Core\SecurityContextInterface;

class aclTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException     \InvalidArgumentException
     */
    public function testInvalidPermission()
    {
        $this->acl->isPermitted('abc');
    }



    public function setUp()
    {
        $mockSecConInt = $this->getMockBuilder('Symfony\\Component\\Security\\Core\\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->acl = new Acl($mockSecConInt);
    }

}