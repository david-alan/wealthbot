<?php
namespace Test\UserBundle;

use Wealthbot\UserBundle\Model\User;
use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\GlobalExecutionContextInterface;

class UserTest extends \PHPUnit_Framework_TestCase {

    /*
     * Integration test of user and SOF user model and Execution Context
     *
     * Execution Context stores all error messages; retreivable through getViolations()
     */
    public function testIsPasswordLegal() {
        $this->user->setPlainPassword('david');

        $this->user->setUsername('david');

        $this->user->isPasswordLegal($this->executionContext);

        $this->executionContext->expects($this->any())
            ->method('addViolationAtSubPath')
            ->willReturn(1);

        var_dump($this->executionContext->getViolations());
        $this->assertTrue(true);
    }

    public function setUp()
    {
        $translatorInterface = $this->getMock(TranslatorInterface::class);
        $globalExecutionContextInterface = $this->getMock(GlobalExecutionContextInterface::class);
        $this->user = new User();

        $this->executionContext = new ExecutionContext($globalExecutionContextInterface, $translatorInterface);

        /*
        $this->executionContext = $this->getMockBuilder(ExecutionContext::class)
        ->setConstructorArgs([$globalExecutionContextInterface, $translatorInterface])
        ->getMock();*/
    }
}