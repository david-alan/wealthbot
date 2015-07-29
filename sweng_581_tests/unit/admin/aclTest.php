<?php
namespace Test\AdminBundle;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Wealthbot\AdminBundle\Model\Acl;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Wealthbot\UserBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class aclTest extends \PHPUnit_Framework_TestCase {

    /**
    * @expectedException     \InvalidArgumentException
    */
    public function testInvalidPermission()
    {
        $this->acl->isPermitted('abc');
    }

    public function testValidPermissionAllowsAccess()
    {
        $this->mockSecConInt->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->mockSecConInt->expects($this->once())
            ->method('isGranted')
            ->willReturn(true);

        $this->assertTrue($this->acl->isPermitted('view'));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testInvalidPermissionThrowsAccessDenied()
    {

        $this->mockSecConInt->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->mockSecConInt->expects($this->once())
            ->method('isGranted')
            ->willReturn(false);

        $this->acl->checkAccess('view');
    }

    public function setUp()
    {
        $this->token = $this->getMock(AbstractToken::class);
        $this->user = $this->getMock('Wealthbot\\UserBundle\\Model\\User');

        $this->mockSecConInt = $this->getMockBuilder('Symfony\\Component\\Security\\Core\\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->acl = new Acl($this->mockSecConInt);
    }

}