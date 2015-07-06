<?php

namespace Wealthbot\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends EntityRepository
{
    public function findByUserId($userId)
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.users', 'u')
            ->where('u.id = :userId AND d.filename IS NOT NULL')
            ->setParameters(array(
                'userId' => $userId,
            ));

        return $qb->getQuery()->getResult();
    }

    public function findByCustodianId($custodianId)
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.custodians', 'c')
            ->where('c.id = :custodianId AND d.filename IS NOT NULL')
            ->setParameters(array(
                'custodianId' => $custodianId,
            ));

        return $qb->getQuery()->getResult();
    }

    public function getUserDocumentByType($userId, $type)
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.users', 'u')
            ->where('u.id = :userId AND d.filename IS NOT NULL')
            ->andWhere('d.type = :type')
            ->setParameters(array(
                'userId' => $userId,
                'type' => $type
            ));

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getUserDocumentSorted($userId, $sort, $direction = 'asc')
    {
        if (!$direction) {
            $direction = 'asc';
        }

        switch ($sort) {
            case 'source':
                $orderBy = 'o.roles';
                break;
            case 'date':
                $orderBy = 'd.created';
                break;
            case 'type':
                $orderBy = 'd.type';
                break;
            default:
                $orderBy = 'd.created';
                break;
        }

        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.users', 'u')
            ->leftJoin('d.owner', 'o')
            ->where('u.id = :userId AND d.filename IS NOT NULL')
            ->setParameter('userId', $userId)
            ->orderBy($orderBy, $direction)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getCustodianDocumentByType($custodianId, $type)
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.custodians', 'c')
            ->where('c.id = :custodianId AND d.filename IS NOT NULL')
            ->andWhere('d.type = :type')
            ->setParameters(array(
                'custodianId' => $custodianId,
                'type' => $type
            ));

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isUserDocument($documentId, $userId)
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.users', 'u')
            ->where('u.id = :userId')
            ->andWhere('d.id = :documentId')
            ->setParameters(array(
                'userId' => $userId,
                'documentId' => $documentId
            ));

        return $qb->getQuery()->getOneOrNullResult();
    }
}
