<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 21.12.12
 * Time: 13:35
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\ClientBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as Orm;
use Wealthbot\ClientBundle\Collection\ConsolidatedAccountsCollection;
use Wealthbot\SignatureBundle\Entity\DocumentSignature;
use Wealthbot\SignatureBundle\Model\SignableInterface;

/**
 * Class ClientAccount
 *
 * @Orm\MappedSuperClass
 */
class ClientAccount implements WorkflowableInterface, SignableInterface
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \Wealthbot\ClientBundle\Entity\AccountGroupType
     */
    protected $groupType;

    /**
     * @var integer $client_id
     */
    protected $client_id;

    /**
     * @var \Wealthbot\UserBundle\Entity\User
     */
    protected $client;

    /**
     * @var integer
     */
    protected $process_step;

    const PROCESS_STEP_NEED_CREDENTIALS = 0;
    const PROCESS_STEP_COMPLETED_CREDENTIALS = 1;

    const PROCESS_STEP_START_TRANSFER = 0;
    const PROCESS_STEP_STARTED_TRANSFER = 1;
    const PROCESS_STEP_FINISHED_APPLICATION = 2;

    protected static $_processSteps = array(
        AccountGroup::GROUP_DEPOSIT_MONEY => array(
            self::PROCESS_STEP_START_TRANSFER => 'Start Account Application',
            self::PROCESS_STEP_STARTED_TRANSFER => 'Continue Your Progress',
            self::PROCESS_STEP_FINISHED_APPLICATION => 'Application Completed'
        ),
        AccountGroup::GROUP_FINANCIAL_INSTITUTION => array(
            self::PROCESS_STEP_START_TRANSFER => 'Start Transfer Application',
            self::PROCESS_STEP_STARTED_TRANSFER => 'Continue Your Progress',
            self::PROCESS_STEP_FINISHED_APPLICATION => 'Application Completed'
        ),
        AccountGroup::GROUP_OLD_EMPLOYER_RETIREMENT => array(
            self::PROCESS_STEP_START_TRANSFER => 'Start Rollover Application',
            self::PROCESS_STEP_STARTED_TRANSFER => 'Continue Your Progress',
            self::PROCESS_STEP_FINISHED_APPLICATION => 'Application Completed'
        ),
        AccountGroup::GROUP_EMPLOYER_RETIREMENT => array(
            self::PROCESS_STEP_NEED_CREDENTIALS =>'Enter Credentials',
            self::PROCESS_STEP_COMPLETED_CREDENTIALS => 'Application Completed'
        )
    );

    /**
     * @var string
     */
    protected $step_action;

    //ENUM values for step_action column
    const STEP_ACTION_BASIC = 'basic';
    const STEP_ACTION_ADDITIONAL_BASIC = 'additional_basic';
    const STEP_ACTION_PERSONAL = 'personal';
    const STEP_ACTION_ADDITIONAL_PERSONAL = 'additional_personal';
    const STEP_ACTION_BENEFICIARIES = 'beneficiaries';
    const STEP_ACTION_FUNDING_DISTRIBUTING = 'funding_distributing';
    const STEP_ACTION_ROLLOVER = 'rollover';
    const STEP_ACTION_TRANSFER = 'transfer';
    const STEP_ACTION_REVIEW = 'review';
    const STEP_ACTION_CREDENTIALS = 'credentials';
    const STEP_ACTION_FINISHED = 'finished';

    static private $_stepActionValues = null;

    /**
     * @var \Wealthbot\ClientBundle\Entity\SystemAccount
     */
    protected $systemAccount;

    /**
     * @var string $financial_institution
     */
    protected $financial_institution;

    /**
     * @var float $monthly_contributions
     */
    protected $monthly_contributions;

    /**
     * @var float $monthly_distributions
     */
    protected $monthly_distributions;

    /**
     * @var \Wealthbot\ClientBundle\Entity\AccountContribution
     */
    protected $accountContribution;

    // Enum values for owner column
    const OWNER_SELF = 'self';
    const OWNER_SPOUSE = 'spouse';

    static private $_ownerValues = null;

    /**
     * @var \Wealthbot\ClientBundle\Entity\TransferInformation
     */
    protected  $transferInformation;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $beneficiaries;

    /**
     * @var integer
     */
    protected $system_type;

    /**
     * @var integer
     */
    protected $consolidator_id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $consolidatedAccounts;

    /**
     * @var \Wealthbot\ClientBundle\Entity\ClientAccount
     */
    protected $consolidator;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $accountOwners;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accountOwners = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client_id
     *
     * @param integer $clientId
     * @return ClientAccount
     */
    public function setClientId($clientId)
    {
        $this->client_id = $clientId;

        return $this;
    }

    /**
     * Get client_id
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Set client
     *
     * @param \Wealthbot\UserBundle\Entity\User $client
     * @return ClientAccount
     */
    public function setClient(\Wealthbot\UserBundle\Entity\User $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Wealthbot\UserBundle\Entity\User
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set financial_institution
     *
     * @param string $financialInstitution
     * @return ClientAccount
     */
    public function setFinancialInstitution($financialInstitution)
    {
        $this->financial_institution = $financialInstitution;

        return $this;
    }

    /**
     * Get financial_institution
     *
     * @return string
     */
    public function getFinancialInstitution()
    {
        return $this->financial_institution;
    }

    /**
     * Get array ENUM values step_action column
     *
     * @static
     * @return array
     */
    static public function getStepActionChoices()
    {
        // Build $_typeValues if this is the first call
        if (self::$_stepActionValues == null) {
            self::$_stepActionValues = array();
            $oClass = new \ReflectionClass('\Wealthbot\ClientBundle\Model\ClientAccount');
            $classConstants = $oClass->getConstants();
            $constantPrefix = "STEP_ACTION_";
            foreach ($classConstants as $key => $val) {
                if (substr($key, 0, strlen($constantPrefix)) === $constantPrefix) {
                    self::$_stepActionValues[$val] = $val;
                }
            }
        }

        return self::$_stepActionValues;
    }

    /**
     * Set groupType
     *
     * @param \Wealthbot\ClientBundle\Entity\AccountGroupType $groupType
     * @return ClientAccount
     */
    public function setGroupType(\Wealthbot\ClientBundle\Entity\AccountGroupType $groupType = null)
    {
        $this->groupType = $groupType;

        return $this;
    }

    /**
     * Get groupType
     *
     * @return \Wealthbot\ClientBundle\Entity\AccountGroupType
     */
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * Set process_step
     *
     * @param integer $processStep
     * @return ClientAccount
     */
    public function setProcessStep($processStep)
    {
        $this->process_step = $processStep;

        return $this;
    }

    /**
     * Get process_step
     *
     * @return integer
     */
    public function getProcessStep()
    {
        return $this->process_step;
    }

    /**
     * Get account process by process_step and account group
     *
     * @return mixed
     * @throws \Exception
     * @return string
     */
    public function getProcess()
    {
        $group = $this->getGroupName();
        $step = $this->getProcessStep();

        if (!array_key_exists($group, self::$_processSteps)) {
            throw new \Exception(sprintf('Process step value is not defined for group: $s.', $group));
        }

        if (!array_key_exists($step, self::$_processSteps[$group])) {
            throw new \Exception(sprintf('Undefined process step $s for account group: $s.', $group));
        }

        if ($this->getConsolidatedAccounts()->count()) {
            return 'Start Applications';
        }

        return self::$_processSteps[$group][$step];
    }

    /**
     * Set step_action
     *
     * @param string $stepAction
     * @return ClientAccount
     * @throws \InvalidArgumentException
     */
    public function setStepAction($stepAction)
    {
        if (!is_null($stepAction) && !in_array($stepAction, self::getStepActionChoices())) {
            throw new \InvalidArgumentException(
                sprintf('Invalid value for client_accounts.step_action : %s.', $stepAction)
            );
        }

        $this->step_action = $stepAction;

        return $this;
    }

    /**
     * Get step_action
     *
     * @return string
     */
    public function getStepAction()
    {
        return $this->step_action;
    }


    /**
     * Get name of group of account
     *
     * @return null|string
     */
    public function getGroupName()
    {
        $groupType = $this->getGroupType();

        if (!$groupType) {
            $groupName = null;
        } else {
            if (!$groupType->getGroup()) {
                $groupName = null;
            } else {
                $groupName = $groupType->getGroup()->getName();
            }
        }

        return $groupName;
    }

    /**
     * Get name of type of account
     *
     * @return null|string
     */
    public function getTypeName()
    {
        $groupType = $this->getGroupType();

        if (!$groupType) {
            $typeName = null;
        } else {
            if (!$groupType->getType()) {
                $typeName = null;
            } else {
                $typeName = $groupType->getType()->getName();
            }
        }

        return $typeName;
    }

    /**
     * Get account type string
     *
     * @return string
     */
    public function getTypeString()
    {
        $group = $this->getGroupName();
        $employerName = null;

        if ($group === AccountGroup::GROUP_FINANCIAL_INSTITUTION ||
            $group === AccountGroup::GROUP_OLD_EMPLOYER_RETIREMENT
        ) {
            $employerName = $this->getFinancialInstitution();
        } elseif ($group === AccountGroup::GROUP_EMPLOYER_RETIREMENT) {
            $employerFinancialInstitution = explode('(', $this->getFinancialInstitution());
            $employerName = trim($employerFinancialInstitution[1], ' )');
        }

        $result = $this->getOwnersAsString();
        if (null !== $employerName) {
            $result .= ' ' . $employerName;
        }
        $result .= ' ' . $this->getTypeName();

        return $result;
    }

    /**
     * Get account type by group
     *
     * @return null|string
     */
    public function getAccountType()
    {
        $group = $this->getGroupName();

        switch ($group) {
            case AccountGroup::GROUP_FINANCIAL_INSTITUTION:
                $type = 'Transfer';
                break;
            case AccountGroup::GROUP_DEPOSIT_MONEY:
                $type = 'Deposit';
                break;
            case AccountGroup::GROUP_OLD_EMPLOYER_RETIREMENT:
                $type = 'Rollover';
                break;
            case AccountGroup::GROUP_EMPLOYER_RETIREMENT:
                $type = 'Current retirement plan';
                break;
            default:
                $type = null;
                break;
        }

        return $type;
    }

    /**
     * Get account activity by group
     *
     * @return null|string
     */
    public function getActivity()
    {
        $group = $this->getGroupName();

        switch ($group) {
            case AccountGroup::GROUP_FINANCIAL_INSTITUTION:
                $activity = 'Transfer';
                break;
            case AccountGroup::GROUP_DEPOSIT_MONEY:
                $activity = 'New Account';
                break;
            case AccountGroup::GROUP_OLD_EMPLOYER_RETIREMENT:
                $activity = 'Rollover';
                break;
            case AccountGroup::GROUP_EMPLOYER_RETIREMENT:
                $activity = 'Advice';
                break;
            default:
                $activity = null;
                break;
        }

        return $activity;
    }

    /**
     * Set transferInformation
     *
     * @param \Wealthbot\ClientBundle\Entity\TransferInformation $transferInformation
     * @return $this
     */
    public function setTransferInformation(\Wealthbot\ClientBundle\Entity\TransferInformation $transferInformation = null)
    {
        $this->transferInformation = $transferInformation;

        return $this;
    }

    /**
     * Get transferInformation
     *
     * @return \Wealthbot\ClientBundle\Entity\TransferInformation
     */
    public function getTransferInformation()
    {
        return $this->transferInformation;
    }

    /**
     * Add beneficiaries
     *
     * @param \Wealthbot\ClientBundle\Entity\Beneficiary $beneficiaries
     * @return ClientAccount
     */
    public function addBeneficiarie(\Wealthbot\ClientBundle\Entity\Beneficiary $beneficiaries)
    {
        $this->beneficiaries[] = $beneficiaries;

        return $this;
    }

    /**
     * Remove beneficiaries
     *
     * @param \Wealthbot\ClientBundle\Entity\Beneficiary $beneficiaries
     */
    public function removeBeneficiarie(\Wealthbot\ClientBundle\Entity\Beneficiary $beneficiaries)
    {
        $this->beneficiaries->removeElement($beneficiaries);
    }

    /**
     * Get beneficiaries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBeneficiaries()
    {
        return $this->beneficiaries;
    }

    /**
     * Set system_type
     *
     * @param $systemType
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setSystemType($systemType)
    {
        if (!array_key_exists($systemType, SystemAccount::getTypeChoices())) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value of system_type: %s for %s',
                $systemType,
                get_class($this)
            ));
        }

        $this->system_type = $systemType;

        return $this;
    }

    /**
     * Get system_type
     *
     * @return int
     */
    public function getSystemType()
    {
        return $this->system_type;
    }

    /**
     * Get system_type as string
     *
     * @return string
     */
    public function getSystemTypeAsString()
    {
        $types = SystemAccount::getTypeChoices();

        return $types[$this->system_type];
    }

    /**
     * Set accountContribution
     *
     * @param \Wealthbot\ClientBundle\Entity\AccountContribution $accountContribution
     * @return ClientAccount
     */
    public function setAccountContribution(\Wealthbot\ClientBundle\Entity\AccountContribution $accountContribution = null)
    {
        $this->accountContribution = $accountContribution;

        return $this;
    }

    /**
     * Get accountContribution
     *
     * @return \Wealthbot\ClientBundle\Entity\AccountContribution
     */
    public function getAccountContribution()
    {
        return $this->accountContribution;
    }

    /**
     * Set consolidator_id
     *
     * @param integer $consolidatorId
     * @return ClientAccount
     */
    public function setConsolidatorId($consolidatorId)
    {
        $this->consolidator_id = $consolidatorId;

        return $this;
    }

    /**
     * Get consolidator_id
     *
     * @return integer
     */
    public function getConsolidatorId()
    {
        return $this->consolidator_id;
    }

    /**
     * Add consolidatedAccounts
     *
     * @param \Wealthbot\ClientBundle\Entity\ClientAccount $consolidatedAccounts
     * @return ClientAccount
     */
    public function addConsolidatedAccount(\Wealthbot\ClientBundle\Entity\ClientAccount $consolidatedAccounts)
    {
        $this->consolidatedAccounts[] = $consolidatedAccounts;

        return $this;
    }

    /**
     * Remove consolidatedAccounts
     *
     * @param \Wealthbot\ClientBundle\Entity\ClientAccount $consolidatedAccounts
     */
    public function removeConsolidatedAccount(\Wealthbot\ClientBundle\Entity\ClientAccount $consolidatedAccounts)
    {
        $this->consolidatedAccounts->removeElement($consolidatedAccounts);
    }

    /**
     * Get consolidatedAccounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsolidatedAccounts()
    {
        return $this->consolidatedAccounts;
    }

    /**
     * Set consolidator
     *
     * @param \Wealthbot\ClientBundle\Entity\ClientAccount $consolidator
     * @return ClientAccount
     */
    public function setConsolidator(\Wealthbot\ClientBundle\Entity\ClientAccount $consolidator = null)
    {
        $this->consolidator = $consolidator;

        return $this;
    }

    /**
     * Set systemAccount
     *
     * @param \Wealthbot\ClientBundle\Entity\SystemAccount $systemAccount
     * @return ClientAccount
     */
    public function setSystemAccount(\Wealthbot\ClientBundle\Entity\SystemAccount $systemAccount = null)
    {
        $this->systemAccount = $systemAccount;

        return $this;
    }

    /**
     * Get systemAccount
     *
     * @return \Wealthbot\ClientBundle\Entity\SystemAccount
     */
    public function getSystemAccount()
    {
        if (null === $this->systemAccount && $this->consolidator) {
            return $this->consolidator->getSystemAccount();
        }

        return $this->systemAccount;
    }

    /**
     * Has system account
     *
     * @return bool
     */
    public function hasSystemAccount()
    {
        $systemAccount = $this->getSystemAccount();

        return $systemAccount ? true : false;
    }

    /**
     * Get account number
     *
     * @return string
     */
    public function getAccountNumber()
    {
        $systemAccount = $this->getSystemAccount();

        return $systemAccount ? $systemAccount->getAccountNumber() : '';
    }

    /**
     * Set monthly_contributions
     *
     * @param float $monthlyContributions
     * @return ClientAccount
     */
    public function setMonthlyContributions($monthlyContributions)
    {
        $this->monthly_contributions = $monthlyContributions;

        return $this;
    }

    /**
     * Get monthly_contributions
     *
     * @return float
     */
    public function getMonthlyContributions()
    {
        return $this->monthly_contributions;
    }

    /**
     * Set monthly_distributions
     *
     * @param float $monthlyDistributions
     * @return ClientAccount
     */
    public function setMonthlyDistributions($monthlyDistributions)
    {
        $this->monthly_distributions = $monthlyDistributions;

        return $this;
    }

    public function hasMonthlyContributions()
    {
        return (floatval($this->getMonthlyContributions()) > 0);
    }

    /**
     * Returns true if monthly_contributions > 0 and false otherwise
     *
     * @return bool
     */
    public function hasFunding()
    {
        if ($this->hasMonthlyContributions()) {
            return true;
        }

        if ($this->getConsolidatedAccounts()) {
            foreach ($this->getConsolidatedAccounts() as $account) {
                if ($account->hasMonthlyContributions()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns true if monthly_distributions > 0 and false otherwise
     *
     * @return bool
     */
    public function hasDistributing()
    {
        if (floatval($this->getMonthlyDistributions()) > 0) {
            return true;
        }

        if ($this->getConsolidatedAccounts()) {
            foreach ($this->getConsolidatedAccounts() as $account) {
                if (floatval($account->getMonthlyDistributions()) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get monthly_distributions
     *
     * @return float
     */
    public function getMonthlyDistributions()
    {
        return $this->monthly_distributions;
    }

    /**
     * Get consolidator
     *
     * @return \Wealthbot\ClientBundle\Entity\ClientAccount
     */
    public function getConsolidator()
    {
        return $this->consolidator;
    }

    /**
     * Returns true if account has Personal Investment system_type and false otherwise
     *
     * @return bool
     */
    public function isPersonalType()
    {
        return ($this->getSystemType() == SystemAccount::TYPE_PERSONAL_INVESTMENT) ? true : false;
    }

    /**
     * Returns true if account has Joint Investment system_type and false otherwise
     *
     * @return bool
     */
    public function isJointType()
    {
        return ($this->getSystemType() == SystemAccount::TYPE_JOINT_INVESTMENT) ? true : false;
    }

    /**
     * Returns true if account has Roth IRA system_type and false otherwise
     *
     * @return bool
     */
    public function isRothIraType()
    {
        return ($this->getSystemType() == SystemAccount::TYPE_ROTH_IRA) ? true : false;
    }

    /**
     * Returns true if account has Traditional IRA system_type and false otherwise
     *
     * @return bool
     */
    public function isTraditionalIraType()
    {
        return ($this->getSystemType() == SystemAccount::TYPE_TRADITIONAL_IRA) ? true : false;
    }

    /**
     * Returns true if account has Retirement system_type and false otherwise
     *
     * @return bool
     */
    public function isRetirementType()
    {
        return ($this->getSystemType() == SystemAccount::TYPE_RETIREMENT) ? true : false;
    }

    /**
     * Returns true if account has group $group or if account has consolidated account with group $group
     * and false otherwise
     *
     * @param $group
     * @return bool
     */
    public function hasGroup($group)
    {

        if ($this->getGroupName() === $group) {
            return true;
        }

        if ($this->getConsolidatedAccounts()) {
            foreach ($this->getConsolidatedAccounts() as $consolidatedAccount) {
                if ($consolidatedAccount->getGroupName() === $group) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get transfer consolidated accounts
     *
     * @return ArrayCollection
     */
    public function getTransferConsolidatedAccounts()
    {
        $result = new ArrayCollection();

        if ($this->getGroupName() === AccountGroup::GROUP_FINANCIAL_INSTITUTION) {
            $result->add($this);
        }

        foreach ($this->getConsolidatedAccounts() as $account) {
            if ($account->getGroupName() === AccountGroup::GROUP_FINANCIAL_INSTITUTION) {
                $result->add($account);
            }
        }

        return $result;
    }

    /**
     * Get rollover consolidated accounts
     *
     * @return ArrayCollection
     */
    public function getRolloverConsolidatedAccounts()
    {
        $result = new ArrayCollection();

        if ($this->getGroupName() === AccountGroup::GROUP_OLD_EMPLOYER_RETIREMENT) {
            $result->add($this);
        }

        foreach ($this->getConsolidatedAccounts() as $account) {
            if ($account->getGroupName() === AccountGroup::GROUP_OLD_EMPLOYER_RETIREMENT) {
                $result->add($account);
            }
        }

        return $result;
    }

    /**
     * Add accountOwners
     *
     * @param ClientAccountOwner $accountOwners
     * @return ClientAccount
     */
    public function addAccountOwner(ClientAccountOwner $accountOwners)
    {
        $this->accountOwners[] = $accountOwners;

        return $this;
    }

    /**
     * Remove accountOwners
     *
     * @param ClientAccountOwner $accountOwners
     */
    public function removeAccountOwner(ClientAccountOwner $accountOwners)
    {
        $this->accountOwners->removeElement($accountOwners);
    }

    /**
     * Get accountOwners
     *
     * @return ClientAccountOwner[]
     */
    public function getAccountOwners()
    {
        return $this->accountOwners;
    }

    /**
     * Get account owners as array
     *
     * @return array
     */
    public function getOwnersAsArray()
    {
        $result = array();

        if ($this->getAccountOwners()) {
            foreach ($this->getAccountOwners() as $accountOwner) {
                $type = $accountOwner->getOwnerType();
                $result[$type] = $accountOwner->getOwner()->getId();
            }
        }

        return $result;
    }

    /**
     * Get account owners as string
     *
     * @return string
     */
    public function getOwnersAsString()
    {
        $result = array();
        foreach ($this->getAccountOwners() as $accountOwner) {
            $owner = $accountOwner->getOwner();
            $result[] = $owner->getFirstName();
        }

        return join(' & ', $result);
    }

    /**
     * Get array of account owners.
     * Key of array is type of account owner
     *
     * @return AccountOwnerInterface[]
     */
    public function getOwners()
    {
        $result = array();
        foreach ($this->getAccountOwners() as $accountOwner) {
            $result[$accountOwner->getOwnerType()] = $accountOwner->getOwner();
        }

        return $result;
    }

    /**
     * Get primary applicant of account
     *
     * @return AccountOwnerInterface|null
     */
    public function getPrimaryApplicant()
    {
        $owners = $this->getOwners();
        if (!$this->client->isMarried() && array_key_exists(ClientAccountOwner::OWNER_TYPE_SELF, $owners)) {
            return $owners[ClientAccountOwner::OWNER_TYPE_SELF];
        }

        $primaryApplicant = null;

        if (array_key_exists(ClientAccountOwner::OWNER_TYPE_SELF, $owners)) {
            $primaryApplicant = $owners[ClientAccountOwner::OWNER_TYPE_SELF];
        } elseif (array_key_exists(ClientAccountOwner::OWNER_TYPE_SPOUSE, $owners)) {
            $primaryApplicant = $owners[ClientAccountOwner::OWNER_TYPE_SPOUSE];
        }

        return $primaryApplicant;
    }

    /**
     * Get secondary applicant of account
     *
     * @return AccountOwnerInterface|null
     */
    public function getSecondaryApplicant()
    {
        if (!$this->isJointType()) {
            return null;
        }

        $owners = $this->getOwners();
        $secondaryApplicant = null;

        if (array_key_exists(ClientAccountOwner::OWNER_TYPE_SELF, $owners) &&
            array_key_exists(ClientAccountOwner::OWNER_TYPE_SPOUSE, $owners)
        ) {
            $secondaryApplicant = $owners[ClientAccountOwner::OWNER_TYPE_SPOUSE];
        } elseif (array_key_exists(ClientAccountOwner::OWNER_TYPE_OTHER, $owners)) {
            $secondaryApplicant = $owners[ClientAccountOwner::OWNER_TYPE_OTHER];
        }

        return $secondaryApplicant;
    }

    /**
     * Get consolidated accounts collection
     *
     * @return ConsolidatedAccountsCollection
     */
    public function getConsolidatedAccountsCollection()
    {
        $elements = array_merge(array($this), $this->consolidatedAccounts->getValues());

        return new ConsolidatedAccountsCollection($elements);
    }

    /**
     * Get workflow message code
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getWorkflowMessageCode()
    {
        $group = $this->getGroupName();
        switch ($group) {
            case AccountGroup::GROUP_FINANCIAL_INSTITUTION:
                $messageCode = Workflow::MESSAGE_CODE_PAPERWORK_TRANSFER;
                break;
            case AccountGroup::GROUP_DEPOSIT_MONEY:
                $messageCode = Workflow::MESSAGE_CODE_PAPERWORK_NEW_ACCOUNT;
                break;
            case AccountGroup::GROUP_OLD_EMPLOYER_RETIREMENT:
                $messageCode = Workflow::MESSAGE_CODE_PAPERWORK_ROLLOVER;
                break;
            case AccountGroup::GROUP_EMPLOYER_RETIREMENT:
                $messageCode = Workflow::MESSAGE_CODE_ALERT_NEW_RETIREMENT_ACCOUNT;
                break;
            default:
                throw new \RuntimeException(sprintf('Invalid group: %s for client account object.', $group));
                break;
        }

        return $messageCode;
    }

    /**
     * Get client account object
     *
     * @return ClientAccount
     */
    public function getClientAccount()
    {
        return $this;
    }

    /**
     * Get id of source object
     *
     * @return mixed
     */
    public function getSourceObjectId()
    {
        return $this->id;
    }

    /**
     * Get type of document signature
     *
     * @return string
     */
    public function getDocumentSignatureType()
    {
        return DocumentSignature::TYPE_OPEN_OR_TRANSFER_ACCOUNT;
    }

}
