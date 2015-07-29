<?php
namespace Test\AdminBundle;

use Wealthbot\AdminBundle\Model\Acl;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Wealthbot\UserBundle\Model\User;

class aclTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException     \InvalidArgumentException
     */
    public function testInvalidPermission()
    {
        $this->acl->isPermitted('abc');
    }

    public function testInvalidPermissionThrowsAccessDenied()
    {
        $this->mockSecConInt->expects($this->once())
            ->method('getUser')
            ->willReturn('1');

        $this->mockSecConInt->expects($this->once())
            ->method('getToken')
            ->willReturn('1');

        $this->acl->checkAccess('view');


    }

    public function testPermission()
    {
//        $this->acl->isPermitted('abc');
    }


    public function setUp()
    {
        $this->user = $this->getMock('Wealthbot\\UserBundle\\Model\\User');
        $this->mockSecConInt = $this->getMockBuilder('Symfony\\Component\\Security\\Core\\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->acl = new Acl($this->mockSecConInt);
    }

}