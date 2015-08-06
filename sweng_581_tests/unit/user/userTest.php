<?php
namespace Test\UserBundle;

use Wealthbot\UserBundle\Model\User;
use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\GlobalExecutionContextInterface;
use Symfony\Component\Validator\Tests\Fixtures\StubGlobalExecutionContext;

class UserTest extends \PHPUnit_Framework_TestCase {

    /*
     * Integration test of user and SOF user model and Execution Context
     *
     * Execution Context stores all error messages; retreivable through getViolations()
     */
    public function testIsPasswordLegalPasswordEqualsUsername() {
        $expectedViolation = 'Email and password cant be the same';
        $this->user->setPlainPassword('david');
        $this->user->setUsername('david');

        $this->user->isPasswordLegal($this->executionContext);
        $violationList = $this->executionContext->getViolations();
        $violation = $violationList->get(0);
        $this->assertSame($violation->getMessageTemplate(), $expectedViolation);
    }

    public function testIsPasswordLegalPasswordInvalidBecauseItLacksAnUpperCaseLetter() {
        $expectedViolation = 'Password is not valid!';
        $this->user->setPlainPassword('david1234');
        $this->user->setUsername('SWENG581');

        $this->user->isPasswordLegal($this->executionContext);
        $violationList = $this->executionContext->getViolations();
        $violation = $violationList->get(0);
        $this->assertSame($violation->getMessageTemplate(), $expectedViolation);
    }

    public function testIsPasswordLegalPasswordInvalidBecauseItLacksALowerCaseLetter() {
        $expectedViolation = 'Password is not valid!';
        $this->user->setPlainPassword('DAVID1234');
        $this->user->setUsername('SWENG581');

        $this->user->isPasswordLegal($this->executionContext);
        $violationList = $this->executionContext->getViolations();
        $violation = $violationList->get(0);
        $this->assertSame($violation->getMessageTemplate(), $expectedViolation);
    }

    public function testIsPasswordLegalPasswordInvalidBecauseItLacksANumber() {
        $expectedViolation = 'Password is not valid!';
        $this->user->setPlainPassword('DAVIDHASKINS');
        $this->user->setUsername('SWENG581');

        $this->user->isPasswordLegal($this->executionContext);
        $violationList = $this->executionContext->getViolations();
        $violation = $violationList->get(0);
        $this->assertSame($violation->getMessageTemplate(), $expectedViolation);
    }

    public function testIsPasswordLegalPasswordInvalidBecauseItContainsFirstName() {
        $expectedViolation = 'Password is not valid!';
        $this->user->setPlainPassword('DAVIDHASKINS');
        $this->user->setUsername('SWENG581');
        $this->user->setFirstName('David123');

        $this->user->isPasswordLegal($this->executionContext);
        $violationList = $this->executionContext->getViolations();
        $violation = $violationList->get(0);
        $this->assertSame($violation->getMessageTemplate(), $expectedViolation);
    }

    public function testIsPasswordLegalPasswordInvalidBecauseItContainsLastName() {
        $expectedViolation = 'Password is not valid!';
        $this->user->setPlainPassword('DAVIDHASKINS');
        $this->user->setUsername('SWENG581');
        $this->user->setLastName('Haskins123');

        $this->user->isPasswordLegal($this->executionContext);
        $violationList = $this->executionContext->getViolations();
        $violation = $violationList->get(0);
        $this->assertSame($violation->getMessageTemplate(), $expectedViolation);
    }

    public function testIsPasswordLegalPasswordIsValid() {
        $this->user->setPlainPassword('SomeS3kr3t');
        $this->user->setUsername('SWENG581');
        $this->user->setFirstName('David');
        $this->user->setLastName('Haskins');

        $this->user->isPasswordLegal($this->executionContext);
        $violationList = $this->executionContext->getViolations();

        $this->assertEquals(0, count($violationList));
    }

    public function setUp()
    {
        $translatorInterface = $this->getMock(TranslatorInterface::class);
        $this->globalExecutionContext = new StubGlobalExecutionContext();
        $this->user = new User();

        $this->executionContext = new ExecutionContext($this->globalExecutionContext, $translatorInterface);
    }
}