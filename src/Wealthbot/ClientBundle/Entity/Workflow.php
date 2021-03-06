<?php

namespace Wealthbot\ClientBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Wealthbot\ClientBundle\Model\Workflow as BaseWorkflow;
use Wealthbot\SignatureBundle\Entity\DocumentSignature;

/**
 * Workflow
 */
class Workflow extends BaseWorkflow
{
    /**
     * @var integer
     */
    protected $id;


    /**
     * @var integer
     */
    protected $type;

    /**
     * @var integer
     */
    protected $object_id;

    /**
     * @var array,
     */
    protected $object_ids;

    /**
     * @var string
     */
    protected $object_type;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $message_code;

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var integer
     */
    protected $client_status;

    /**
     * @var boolean
     */
    protected $is_archived;

    /**
     * @var \DateTime
     */
    protected $submitted;

    /**
     * @var \Wealthbot\UserBundle\Entity\User
     */
    protected $client;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $documentSignatures;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var string
     */
    protected $amount;


    public function __construct()
    {
        parent::__construct();

        $this->documentSignatures = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return parent::getId();
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Workflow
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
     * Set object_id
     *
     * @param integer $objectId
     * @return Workflow
     */
    public function setObjectId($objectId)
    {
        parent::setObjectId($objectId);

        return $this;
    }

    /**
     * Get object_id
     *
     * @return integer
     */
    public function getObjectId()
    {
        return parent::getObjectId();
    }

    /**
     * Set object_ids
     *
     * @param array $objectIds
     * @return Workflow
     */
    public function setObjectIds(array $objectIds)
    {
        parent::setObjectIds($objectIds);
    
        return $this;
    }

    /**
     * Get object_ids
     *
     * @return array
     */
    public function getObjectIds()
    {
        return parent::getObjectIds();
    }

    /**
     * Set object_type
     *
     * @param string $objectType
     * @return Workflow
     */
    public function setObjectType($objectType)
    {
        parent::setObjectType($objectType);
    
        return $this;
    }

    /**
     * Get object_type
     *
     * @return string 
     */
    public function getObjectType()
    {
        return parent::getObjectType();
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Workflow
     */
    public function setMessage($message)
    {
        parent::setMessage($message);
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return parent::getMessage();
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Workflow
     */
    public function setStatus($status)
    {
        parent::setStatus($status);
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return parent::getStatus();
    }

    /**
     * Set client status
     *
     * @param integer $clientStatus
     * @return Workflow
     */
    public function setClientStatus($clientStatus)
    {
        parent::setClientStatus($clientStatus);

        return $this;
    }

    /**
     * Get client status
     *
     * @return integer
     */
    public function getClientStatus()
    {
        return parent::getClientStatus();
    }


    /**
     * Set is_archived
     *
     * @param boolean $isArchived
     * @return Workflow
     */
    public function setIsArchived($isArchived)
    {
        parent::setIsArchived($isArchived);
    
        return $this;
    }

    /**
     * Get is_archived
     *
     * @return boolean 
     */
    public function getIsArchived()
    {
        return parent::getIsArchived();
    }

    /**
     * Set submitted
     *
     * @param \DateTime $submitted
     * @return Workflow
     */
    public function setSubmitted($submitted)
    {
        parent::setSubmitted($submitted);
    
        return $this;
    }

    /**
     * Get submitted
     *
     * @return \DateTime 
     */
    public function getSubmitted()
    {
        return parent::getSubmitted();
    }

    /**
     * Set client
     *
     * @param \Wealthbot\UserBundle\Entity\User $client
     * @return Workflow
     */
    public function setClient(\Wealthbot\UserBundle\Entity\User $client = null)
    {
        parent::setClient($client);
    
        return $this;
    }

    /**
     * Get client
     *
     * @return \Wealthbot\UserBundle\Entity\User 
     */
    public function getClient()
    {
        return parent::getClient();
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Workflow
     */
    public function setNote($note)
    {
        parent::setNote($note);
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return parent::getNote();
    }

    /**
     * Set message_code
     *
     * @param string $messageCode
     * @return Workflow
     */
    public function setMessageCode($messageCode)
    {
        parent::setMessageCode($messageCode);
    
        return $this;
    }

    /**
     * Get message_code
     *
     * @return string 
     */
    public function getMessageCode()
    {
        return parent::getMessageCode();
    }

    /**
     * Add documentSignatures
     *
     * @param \Wealthbot\SignatureBundle\Entity\DocumentSignature $documentSignatures
     * @return Workflow
     */
    public function addDocumentSignature(\Wealthbot\SignatureBundle\Entity\DocumentSignature $documentSignatures)
    {
        $this->documentSignatures[] = $documentSignatures;

        return $this;
    }

    /**
     * Remove documentSignatures
     *
     * @param \Wealthbot\SignatureBundle\Entity\DocumentSignature $documentSignatures
     */
    public function removeDocumentSignature(\Wealthbot\SignatureBundle\Entity\DocumentSignature $documentSignatures)
    {
        $this->documentSignatures->removeElement($documentSignatures);
    }

    /**
     * Get documentSignatures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocumentSignatures()
    {
        return $this->documentSignatures;
    }

    /**
     * Get count of documentSignatures
     *
     * @return int
     */
    public function getDocumentSignaturesCount()
    {
        return $this->documentSignatures->count();
    }

    /**
     * Get filename of the first document signature
     *
     * @return null|string
     */
    public function getFirstDocumentSignatureFilename()
    {
        if ($this->getDocumentSignaturesCount() > 0) {
            /** @var DocumentSignature $signature */
            $signature = $this->documentSignatures->first();

            return $signature->getDocument()->getFilename();
        }

        return null;
    }

    /**
     * Is all document signatures are created
     *
     * @return bool
     */
    public function isDocumentSignaturesCreated()
    {
        if (!$this->documentSignatures->count()) {
            return false;
        }

        foreach ($this->getDocumentSignatures() as $signature) {
            if (!$signature->isCreated()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Is all document signatures are completed
     *
     * @return bool
     */
    public function isDocumentSignaturesCompleted()
    {
        if (!$this->documentSignatures->count()) {
            return false;
        }

        foreach ($this->getDocumentSignatures() as $signature) {
            if (!$signature->isCompleted()) {
                return false;
            }
        }

        return true;
    }

//    /**
//     * Update client status by document signatures
//     */
//    public function updateClientStatusByDocumentSignatures()
//    {
//        if ($this->isDocumentSignaturesCreated()) {
//            $this->setClientStatus(self::CLIENT_STATUS_ENVELOPE_CREATED);
//        } elseif ($this->isDocumentSignaturesCompleted()) {
//            $this->setClientStatus(self::CLIENT_STATUS_ENVELOPE_COMPLETED);
//        } else {
//            $this->setClientStatus(self::CLIENT_STATUS_ENVELOPE_OPENED);
//        }
//    }
//
//    /**
//     * Update client status by client portfolio object
//     *
//     * @param ClientPortfolio $clientPortfolio
//     */
//    public function updateClientStatusByClientPortfolio(ClientPortfolio $clientPortfolio)
//    {
//        if ($clientPortfolio->isProposed()) {
//            $this->setClientStatus(self::CLIENT_STATUS_PORTFOLIO_PROPOSED);
//        } elseif ($clientPortfolio->isClientAccepted()) {
//            $this->setClientStatus(self::CLIENT_STATUS_PORTFOLIO_CLIENT_ACCEPTED);
//            $this->setIsArchived(true);
//        }
//    }

    /**
     * Get all documents of document signatures
     *
     * @return array
     */
    public function getSignaturesDocuments()
    {
        $documents = array();

        /** @var DocumentSignature $signature */
        foreach ($this->getDocumentSignatures() as $signature) {
            $documents[] = $signature->getDocument();
        }

        return $documents;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return Workflow
     */
    public function setAmount($amount)
    {
        parent::setAmount($amount);

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return parent::getAmount();
    }
}
