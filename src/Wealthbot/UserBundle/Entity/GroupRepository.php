<?php

namespace Wealthbot\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * GroupRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GroupRepository extends EntityRepository
{
    public function getRiaGroups(User $ria)
    {
        $qb = $this->createQueryBuilder('g');

        $qb->where('g.owner = :owner')
            ->orWhere('g.owner IS NULL')
            ->setParameters(array(
                'owner' => $ria,
            ));

        return $qb->getQuery()->getResult();
    }
}
