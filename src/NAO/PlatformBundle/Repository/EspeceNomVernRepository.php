<?php

namespace NAO\PlatformBundle\Repository;

/**
 * EspeceNomVernRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EspeceNomVernRepository extends \Doctrine\ORM\EntityRepository
{
    function findLikeByName($name){
        $qb = $this->createQueryBuilder('e')
            ->where('e.nomVern LIKE :name')
            ->setParameter('name', "%$name%")
            ->orderBy('e.nomVern')
            ->setMaxResults(10);
        return $qb->getQuery()->getResult();
    }
}

