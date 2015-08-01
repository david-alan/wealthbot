<?php
namespace Test\ClientBundle;
use Wealthbot\ClientBundle\Model\ClientAccount;
use Wealthbot\ClientBundle\Entity\AccountGroup;
use Wealthbot\ClientBundle\Entity\AccountGroupType;
use Wealthbot\ClientBundle\Entity\ClientAccount as EntityClientAccount;
use Wealthbot\ClientBundle\Collection\ConsolidatedAccountsCollection;

class ClientAccountTest extends \PHPUnit_Framework_TestCase {

    /**
     * Use pairwise tests for ClientAccount::getProcess()
     *
     * Pairs (name,process):
     *  {deposit_money,Start Account Application}
     *  {financial_institution,Start Transfer Application}
     *  {old_employer_retirement,Start Rollover Application}
     *  {employer_retirement,Enter Credentials}
     */
    public function testGetProcessDepositMoneyStartAccountApplication()
    {
        $this->accountGroupType->expects($this->any())
            ->method('getGroup')
            ->willReturn($this->accountGroup);

        $this->accountGroup->expects($this->any())
            ->method('getName')
            ->willReturn('deposit_money');

        $this->consolidatedAccountsCollection->expects($this->any())
            ->method('count')
            ->willReturn(0);

        $this->clientAccount->setProcessStep(ClientAccount::PROCESS_STEP_START_TRANSFER);

        $this->clientAccount->addConsolidatedAccount($this->clientAccountEntity);

        $this->clientAccount->setGroupType($this->accountGroupType);
        $this->clientAccount->getStepActionChoices();

        $actualProcesses = $this->clientAccount->getProcess();
        $expectedProcess = "Start Account Application";

        $this->assertEquals($actualProcesses, $expectedProcess);
    }

    public function testGetProcessFinancialInstitutionStartTransferApplication()
    {
        $this->accountGroupType->expects($this->any())
            ->method('getGroup')
            ->willReturn($this->accountGroup);

        $this->accountGroup->expects($this->any())
            ->method('getName')
            ->willReturn('financial_institution');

        $this->consolidatedAccountsCollection->expects($this->any())
            ->method('count')
            ->willReturn(0);

        $this->clientAccount->setProcessStep(ClientAccount::PROCESS_STEP_START_TRANSFER);

        $this->clientAccount->addConsolidatedAccount($this->clientAccountEntity);

        $this->clientAccount->setGroupType($this->accountGroupType);
        $this->clientAccount->getStepActionChoices();

        $actualProcesses = $this->clientAccount->getProcess();
        $expectedProcess = "Start Transfer Application";

        $this->assertEquals($actualProcesses, $expectedProcess);
    }

    public function testGetProcessOldEmployerRetirementStartRolloverApplication()
    {
        $this->accountGroupType->expects($this->any())
            ->method('getGroup')
            ->willReturn($this->accountGroup);

        $this->accountGroup->expects($this->any())
            ->method('getName')
            ->willReturn('old_employer_retirement');

        $this->consolidatedAccountsCollection->expects($this->any())
            ->method('count')
            ->willReturn(0);

        $this->clientAccount->setProcessStep(ClientAccount::PROCESS_STEP_START_TRANSFER);

        $this->clientAccount->addConsolidatedAccount($this->clientAccountEntity);

        $this->clientAccount->setGroupType($this->accountGroupType);
        $this->clientAccount->getStepActionChoices();

        $actualProcesses = $this->clientAccount->getProcess();
        $expectedProcess = "Start Rollover Application";

        $this->assertEquals($actualProcesses, $expectedProcess);
    }

    public function testGetProcessEmployerRetirementEnterCredentials()
    {
        $this->accountGroupType->expects($this->any())
            ->method('getGroup')
            ->willReturn($this->accountGroup);

        $this->accountGroup->expects($this->any())
            ->method('getName')
            ->willReturn('employer_retirement');

        $this->consolidatedAccountsCollection->expects($this->any())
            ->method('count')
            ->willReturn(0);

        $this->clientAccount->setProcessStep(ClientAccount::PROCESS_STEP_START_TRANSFER);

        $this->clientAccount->addConsolidatedAccount($this->clientAccountEntity);

        $this->clientAccount->setGroupType($this->accountGroupType);
        $this->clientAccount->getStepActionChoices();

        $actualProcesses = $this->clientAccount->getProcess();
        $expectedProcess = "Enter Credentials";

        $this->assertEquals($actualProcesses, $expectedProcess);
    }

    public function setUp()
    {
        $this->clientAccountEntity = $this->getMock(EntityClientAccount::class);
        $this->accountGroup = $this->getMock(AccountGroup::class);
        $this->accountGroupType = $this->getMock(AccountGroupType::class);
        $this->clientAccount = new ClientAccount();

        $this->ca = $this->getMock(\Doctrine\Common\Collections\Collection::class);
        $this->consolidatedAccountsCollection = $this->getMockBuilder(ConsolidatedAccountsCollection::class)
            ->setConstructorArgs(array(array($this->clientAccount)))
            ->getMock();
    }
}