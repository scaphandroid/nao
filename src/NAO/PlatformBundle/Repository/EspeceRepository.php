<?php

namespace NAO\PlatformBundle\Repository;

/**
 * EspeceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EspeceRepository extends \Doctrine\ORM\EntityRepository
{
    function findLikeByName($name, $maxResults){
        $qb = $this->createQueryBuilder('e')
            ->where('e.nomConcat LIKE :name')
            ->setParameter('name', "%$name%")
            ->orderBy('e.nomVern')
            ->setMaxResults($maxResults);
        return $qb->getQuery()->getResult();
    }
}

