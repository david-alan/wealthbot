<?php

namespace Wealthbot\ClientBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Wealthbot\ClientBundle\Model\SystemAccount as BaseSystemAccount;

/**
 * SystemAccount
 */
class SystemAccount extends BaseSystemAccount
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $client_id;

    /**
     * @var integer
     */
    private $client_account_id;

    /**
     * @var string
     */
    protected $account_number;

    /**
     * @var string
     */
    private $account_description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $transferInformations;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var \Wealthbot\UserBundle\Entity\User
     */
    private $client;

    /**
     * @var \Wealthbot\ClientBundle\Entity\ClientAccount
     */
    private $clientAccount;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    private $source;

    /**
     * @var BillItem[]|ArrayCollection
     */
    protected $billItems;

    /**
     * @var ClientAccountValue[]|ArrayCollection
     */
    protected $clientAccountValues;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $rebalancerActions;

    /**
     * @var \DateTime
     */
    protected $activated_on;

    /**
     * @var \DateTime
     */
    protected $closed;

    /**
     * @var integer
     */
    protected $creationType;


    const SOURCE_SAMPLE = 'sample';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transferInformations = new ArrayCollection();
        $this->source = self::SOURCE_SAMPLE;
        $this->billItems = new ArrayCollection();
        $this->clientAccountValues = new ArrayCollection();
        $this->rebalancerActions = new ArrayCollection();

        parent::__construct();
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
     * @return SystemAccount
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
     * Set account_number
     *
     * @param string $accountNumber
     * @return SystemAccount
     */
    public function setAccountNumber($accountNumber)
    {
        return parent::setAccountNumber($accountNumber);
    }

    /**
     * Get account_number
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return parent::getAccountNumber();
    }

    /**
     * Set account_description
     *
     * @param string $accountDescription
     * @return SystemAccount
     */
    public function setAccountDescription($accountDescription)
    {
        $this->account_description = $accountDescription;

        return $this;
    }

    /**
     * Get account_description
     *
     * @return string
     */
    public function getAccountDescription()
    {
        return $this->account_description;
    }

    /**
     * Add transferInformations
     *
     * @param \Wealthbot\ClientBundle\Entity\TransferInformation $transferInformations
     * @return SystemAccount
     */
    public function addTransferInformation(\Wealthbot\ClientBundle\Entity\TransferInformation $transferInformations)
    {
        $this->transferInformations[] = $transferInformations;

        return $this;
    }

    /**
     * Remove transferInformations
     *
     * @param \Wealthbot\ClientBundle\Entity\TransferInformation $transferInformations
     */
    public function removeTransferInformation(\Wealthbot\ClientBundle\Entity\TransferInformation $transferInformations)
    {
        $this->transferInformations->removeElement($transferInformations);
    }

    /**
     * Get transferInformations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransferInformations()
    {
        return $this->transferInformations;
    }

    /**
     * Set client
     *
     * @param \Wealthbot\UserBundle\Entity\User $client
     * @return SystemAccount
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
     * Set type
     *
     * @param integer $type
     * @return SystemAccount
     */
    public function setType($type)
    {
        parent::setType($type);

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return parent::getType();
    }

    /**
     * Set client_account_id
     *
     * @param integer $clientAccountId
     * @return SystemAccount
     */
    public function setClientAccountId($clientAccountId)
    {
        $this->client_account_id = $clientAccountId;

        return $this;
    }

    /**
     * Get client_account_id
     *
     * @return integer
     */
    public function getClientAccountId()
    {
        return $this->client_account_id;
    }

    /**
     * Set clientAccount
     *
     * @param \Wealthbot\ClientBundle\Entity\ClientAccount $clientAccount
     * @return SystemAccount
     */
    public function setClientAccount(\Wealthbot\ClientBundle\Entity\ClientAccount $clientAccount = null)
    {
        $this->clientAccount = $clientAccount;

        return $this;
    }

    /**
     * Get clientAccount
     *
     * @return \Wealthbot\ClientBundle\Entity\ClientAccount
     */
    public function getClientAccount()
    {
        return $this->clientAccount;
    }

    /**
     * Get account contribution
     *
     * @return null|AccountContribution
     */
    public function getAccountContribution()
    {
        return $this->getClientAccount() ? $this->getClientAccount()->getAccountContribution() : null;
    }

    /**
     * Returns true if exist account contribution with not one-time transaction_frequency and false otherwise
     *
     * @return bool
     */
    public function hasAutoInvestContribution()
    {
        $accountContribution = $this->getAccountContribution();
        if (!$accountContribution) {
            return false;
        }

        return $accountContribution->isOneTimeContribution() ? false : true;
    }

    public function __toString()
    {
        return $this->getAccountNumber() . ' - ' . $this->getAccountDescription();
    }

    /**
     * Set status
     *
     * @param string $status
     * @return SystemAccount
     */
    public function setStatus($status)
    {
        parent::setStatus($status);

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return parent::getStatus();
    }

    /**
     * Set source
     *
     * @param string $source
     * @return SystemAccount
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    public function addBillItem(BillItem $billItem)
    {
        $this->billItems[]=$billItem;
        return $this;
    }

    public function removeBillItem(BillItem $billItem)
    {
        $this->billItems->removeElement($billItem);
        return $this;
    }

    public function getBillItems()
    {
        return $this->billItems;
    }

    public function setBillItems($billItems)
    {
        $this->billItems = $billItems;
        return $this;
    }

    public function getClientAccountValues()
    {
        return $this->clientAccountValues;
    }

    public function setClientAccountValues($clientAccountValues)
    {
        $this->clientAccountValues = $clientAccountValues;
        return $this;
    }

    public function addClientAccountValue(ClientAccountValue $clientAccountValue)
    {
        $this->clientAccountValues[]=$clientAccountValue;
        return $this;
    }

    public function removeClientAccountValue(ClientAccountValue $clientAccountValue)
    {
        $this->clientAccountValues->removeElement($clientAccountValue);
        return $this;
    }

    /**
     * Add rebalancerActions
     *
     * @param \Wealthbot\AdminBundle\Entity\RebalancerAction $rebalancerActions
     * @return SystemAccount
     */
    public function addRebalancerAction(\Wealthbot\AdminBundle\Entity\RebalancerAction $rebalancerActions)
    {
        $this->rebalancerActions[] = $rebalancerActions;

        return $this;
    }

    /**
     * Remove rebalancerActions
     *
     * @param \Wealthbot\AdminBundle\Entity\RebalancerAction $rebalancerActions
     */
    public function removeRebalancerAction(\Wealthbot\AdminBundle\Entity\RebalancerAction $rebalancerActions)
    {
        $this->rebalancerActions->removeElement($rebalancerActions);
    }

    /**
     * Get rebalancerActions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRebalancerActions()
    {
        return $this->rebalancerActions;
    }

    /**
     * Set activated_on
     *
     * @param \DateTime $activatedOn
     * @return SystemAccount
     */
    public function setActivatedOn($activatedOn)
    {
        parent::setActivatedOn($activatedOn);

        return $this;
    }

    /**
     * Get activated_on
     *
     * @return \DateTime 
     */
    public function getActivatedOn()
    {
        return parent::getActivatedOn();
    }

    /**
     * Set closed
     *
     * @param \DateTime $closed
     * @return SystemAccount
     */
    public function setClosed($closed)
    {
        parent::setClosed($closed);

        return $this;
    }

    /**
     * Get closed
     *
     * @return \DateTime 
     */
    public function getClosed()
    {
        return parent::getClosed();
    }

    /**
     * @return float
     */
    public function getProjectedValue()
    {
        $value = 0;
        if ($this->clientAccount) {
            $value = $this->clientAccount->getValueSum();
        }

        return $value;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $distributions;


    /**
     * Add distributions
     *
     * @param \Wealthbot\ClientBundle\Entity\Distribution $distributions
     * @return SystemAccount
     */
    public function addDistribution(\Wealthbot\ClientBundle\Entity\Distribution $distributions)
    {
        $this->distributions[] = $distributions;

        return $this;
    }

    /**
     * Remove distributions
     *
     * @param \Wealthbot\ClientBundle\Entity\Distribution $distributions
     */
    public function removeDistribution(\Wealthbot\ClientBundle\Entity\Distribution $distributions)
    {
        $this->distributions->removeElement($distributions);
    }

    /**
     * Get distributions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDistributions()
    {
        return $this->distributions;
    }

    /**
     * @param int $creationType
     */
    public function setCreationType($creationType)
    {
        $this->creationType = $creationType;
    }

    /**
     * @return int
     */
    public function getCreationType()
    {
        return $this->creationType;
    }


    /**
     * @var \DateTime
     */
    private $performanceInception;

    /**
     * @var \DateTime
     */
    private $billingInception;


    /**
     * Set performanceInception
     *
     * @param \DateTime $performanceInception
     * @return SystemAccount
     */
    public function setPerformanceInception($performanceInception)
    {
        $this->performanceInception = $performanceInception;

        return $this;
    }

    /**
     * Get performanceInception
     *
     * @return \DateTime
     */
    public function getPerformanceInception()
    {
        return $this->performanceInception;
    }

    /**
     * Set billingInception
     *
     * @param \DateTime $billingInception
     * @return SystemAccount
     */
    public function setBillingInception($billingInception)
    {
        $this->billingInception = $billingInception;

        return $this;
    }

    /**
     * Get billingInception
     *
     * @return \DateTime
     */
    public function getBillingInception()
    {
        return $this->billingInception;
    }
    /**
     * @var \Wealthbot\ClientBundle\Entity\SystemAccount
     */
    private $billingAccount;


    /**
     * Set billingAccount
     *
     * @param \Wealthbot\ClientBundle\Entity\SystemAccount $billingAccount
     * @return SystemAccount
     */
    public function setBillingAccount(\Wealthbot\ClientBundle\Entity\SystemAccount $billingAccount = null)
    {
        $this->billingAccount = $billingAccount;

        return $this;
    }

    /**
     * Get billingAccount
     *
     * @return \Wealthbot\ClientBundle\Entity\SystemAccount
     */
    public function getBillingAccount()
    {
        return $this->billingAccount;
    }
}
