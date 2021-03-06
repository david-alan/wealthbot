<?php

namespace Wealthbot\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Wealthbot\AdminBundle\Model\CeModelInterface;

/**
 * Wealthbot\AdminBundle\Entity\SecurityAssignment
 */
class SecurityAssignment
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var integer $subclass_id
     */
    private $subclass_id;

    /**
     * @var \Wealthbot\AdminBundle\Entity\Subclass
     */
    private $subclass;

    /**
     * @var boolean $is_preferred
     */
    private $is_preferred = false;

    /**
     * @var integer $ria_user_id
     */
    private $ria_user_id;

    /**
     * @var integer
     */
    private $security_id;

    /**
     * @var \Wealthbot\AdminBundle\Entity\Security
     */
    private $security;

    /**
     * @var \Wealthbot\ClientBundle\Entity\AccountOutsideFund
     */
    private $accountAssociations;

    /**
     * @var integer
     */
    private $model_id;

    /**
     * @var \Wealthbot\AdminBundle\Entity\CeModel
     */
    private $model;

    /**
     * @var boolean
     */
    private $muni_substitution;

    /**
     * @var SecurityTransaction
     */
    private $securityTransaction;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->muni_substitution = false;
        $this->accountAssociations = new ArrayCollection();
        $this->ceModelEntity = new ArrayCollection();
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
     * Set subclass_id
     *
     * @param integer $subclassId
     * @return SecurityAssignment
     */
    public function setSubclassId($subclassId)
    {
        $this->subclass_id = $subclassId;

        return $this;
    }

    /**
     * Get subclass_id
     *
     * @return integer
     */
    public function getSubclassId()
    {
        return $this->subclass_id;
    }

    /**
     * Set subclass
     *
     * @param \Wealthbot\AdminBundle\Entity\Subclass $subclass
     * @return SecurityAssignment
     */
    public function setSubclass(\Wealthbot\AdminBundle\Entity\Subclass $subclass = null)
    {
        $this->subclass = $subclass;

        return $this;
    }

    /**
     * Get subclass
     *
     * @return \Wealthbot\AdminBundle\Entity\Subclass
     */
    public function getSubclass()
    {
        return $this->subclass;
    }

    /**
     * Set is_preferred
     *
     * @param boolean $isPreferred
     * @return SecurityAssignment
     */
    public function setIsPreferred($isPreferred)
    {
        $this->is_preferred = $isPreferred;

        return $this;
    }

    /**
     * Get is_preferred
     *
     * @return boolean
     */
    public function getIsPreferred()
    {
        return $this->is_preferred;
    }


    /**
     * Set security_id
     *
     * @param integer $securityId
     * @return SecurityAssignment
     */
    public function setSecurityId($securityId)
    {
        $this->security_id = $securityId;

        return $this;
    }

    /**
     * Get security_id
     *
     * @return integer
     */
    public function getSecurityId()
    {
        return $this->security_id;
    }

    /**
     * Set security
     *
     * @param \Wealthbot\AdminBundle\Entity\Security $security
     * @return SecurityAssignment
     */
    public function setSecurity(\Wealthbot\AdminBundle\Entity\Security $security = null)
    {
        $this->security = $security;

        return $this;
    }

    /**
     * Get security
     *
     * @return \Wealthbot\AdminBundle\Entity\Security
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * Set accountAssociations
     *
     * @param \Wealthbot\ClientBundle\Entity\AccountOutsideFund $accountAssociations
     * @return SecurityAssignment
     */
    public function setAccountAssociations(\Wealthbot\ClientBundle\Entity\AccountOutsideFund $accountAssociations = null)
    {
        $this->accountAssociations = $accountAssociations;

        return $this;
    }

    /**
     * Get accountAssociations
     *
     * @return \Wealthbot\ClientBundle\Entity\AccountOutsideFund
     */
    public function getAccountAssociations()
    {
        return $this->accountAssociations;
    }

    /**
     * Add accountAssociations
     *
     * @param \Wealthbot\ClientBundle\Entity\AccountOutsideFund $accountAssociations
     * @return SecurityAssignment
     */
    public function addAccountAssociation(\Wealthbot\ClientBundle\Entity\AccountOutsideFund $accountAssociations)
    {
        $this->accountAssociations[] = $accountAssociations;

        return $this;
    }

    /**
     * Remove accountAssociations
     *
     * @param \Wealthbot\ClientBundle\Entity\AccountOutsideFund $accountAssociations
     */
    public function removeAccountAssociation(\Wealthbot\ClientBundle\Entity\AccountOutsideFund $accountAssociations)
    {
        $this->accountAssociations->removeElement($accountAssociations);
    }

    /**
     * Set model_id
     *
     * @param integer $modelId
     * @return SecurityAssignment
     */
    public function setModelId($modelId)
    {
        $this->model_id = $modelId;

        return $this;
    }

    /**
     * Get model_id
     *
     * @return integer
     */
    public function getModelId()
    {
        return $this->model_id;
    }

    /**
     * Set model
     *
     * @param CeModelInterface $model
     * @return SecurityAssignment
     */
    public function setModel(CeModelInterface $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return \Wealthbot\AdminBundle\Entity\CeModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set muni_substitution
     *
     * @param boolean $muniSubstitution
     * @return SecurityAssignment
     */
    public function setMuniSubstitution($muniSubstitution)
    {
        $this->muni_substitution = $muniSubstitution;

        return $this;
    }

    /**
     * Get muni_substitution
     *
     * @return boolean
     */
    public function getMuniSubstitution()
    {
        return $this->muni_substitution;
    }

    /**
     * Get securityTransaction
     *
     * @return SecurityTransaction
     */
    public function getSecurityTransaction()
    {
        return $this->securityTransaction;
    }

    /**
     * Set securityTransaction
     *
     * @param \Wealthbot\AdminBundle\Entity\SecurityTransaction $securityTransaction
     * @return SecurityAssignment
     */
    public function setSecurityTransaction(\Wealthbot\AdminBundle\Entity\SecurityTransaction $securityTransaction = null)
    {
        $this->securityTransaction = $securityTransaction;

        return $this;
    }

    /**
     * Get security expense_ratio
     *
     * @return float
     */
    public function getExpenseRatio()
    {
        return $this->getSecurity() ? $this->getSecurity()->getExpenseRatio() : 0;
    }

    /**
     * Get clone
     *
     * @return SecurityAssignment
     */
    public function getCopy()
    {
        $clone = clone $this;

        $clone->id = null;
        $clone->accountAssociations = new ArrayCollection();

        return $clone;
    }

    /**
     * @var ArrayCollection
     */
    private $ceModelEntity;


    /**
     * Add ceModelEntity
     *
     * @param \Wealthbot\AdminBundle\Entity\CeModelEntity $ceModelEntity
     * @return SecurityAssignment
     */
    public function addCeModelEntity(\Wealthbot\AdminBundle\Entity\CeModelEntity $ceModelEntity)
    {
        $this->ceModelEntity[] = $ceModelEntity;

        return $this;
    }

    /**
     * Remove ceModelEntity
     *
     * @param \Wealthbot\AdminBundle\Entity\CeModelEntity $ceModelEntity
     */
    public function removeCeModelEntity(\Wealthbot\AdminBundle\Entity\CeModelEntity $ceModelEntity)
    {
        $this->ceModelEntity->removeElement($ceModelEntity);
    }

    /**
     * Get ceModelEntity
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCeModelEntity()
    {
        return $this->ceModelEntity;
    }
}
