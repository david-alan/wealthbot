<?php

namespace Wealthbot\ClientBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wealthbot\ClientBundle\Entity\AccountContribution;
use Wealthbot\SignatureBundle\Entity\DocumentSignature;
use Wealthbot\SignatureBundle\Model\Envelope;
use Wealthbot\SignatureBundle\Repository\SignableObjectRepositoryInterface;

/**
 * AccountContributionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountContributionRepository extends EntityRepository implements SignableObjectRepositoryInterface
{
    /**
     * Is object has completed document signature for application
     *
     * @param int $applicationId
     * @return bool
     */
    public function isApplicationSigned($applicationId)
    {
        $sql = 'SELECT count(ds.id) FROM document_signatures ds
                LEFT JOIN client_account_contribution cac ON (ds.source_id = cac.id AND ds.type = :type)
                WHERE cac.account_id = :application_id ds.status != :status_signed AND ds.status != :status_completed';

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('type', DocumentSignature::TYPE_AUTO_INVEST_CONTRIBUTION);
        $stmt->bindValue('application_id', $applicationId);
        $stmt->bindValue('status_signed', Envelope::STATUS_SIGNED);
        $stmt->bindValue('status_completed', Envelope::STATUS_COMPLETED);

        $stmt->execute();

        return !((bool) $stmt->fetchColumn());
    }

}
