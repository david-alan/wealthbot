<?php

namespace Wealthbot\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wealthbot\UserBundle\Entity\ClientRetiremenFund
 */
class ClientRetirementFund
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $accounts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return ClientRetiremenFund
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add accounts
     *
     * @param Wealthbot\ClientBundle\Entity\ClientRetirementAccount $accounts
     * @return ClientRetiremenFund
     */
    public function addAccount(\Wealthbot\ClientBundle\Entity\ClientRetirementAccount $accounts)
    {
        $this->accounts[] = $accounts;
    
        return $this;
    }

    /**
     * Remove accounts
     *
     * @param Wealthbot\ClientBundle\Entity\ClientRetirementAccount $accounts
     */
    public function removeAccount(\Wealthbot\ClientBundle\Entity\ClientRetirementAccount $accounts)
    {
        $this->accounts->removeElement($accounts);
    }

    /**
     * Get accounts
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAccounts()
    {
        return $this->accounts;
    }
    /**
     * @var string $symbol
     */
    private $symbol;


    /**
     * Set symbol
     *
     * @param string $symbol
     * @return ClientRetirementFund
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    
        return $this;
    }

    /**
     * Get symbol
     *
     * @return string 
     */
    public function getSymbol()
    {
        return $this->symbol;
    }
}