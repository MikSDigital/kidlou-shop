<?php

namespace App\Repository\Nivoslider;

/**
 * Description
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Configuration extends \Doctrine\ORM\EntityRepository {

    public function findFirst() {
        $em = $this->getEntityManager();
        $result = $em->getRepository('App\Entity\Nivoslider\Configuration')
                ->createQueryBuilder('e')
                ->select('e')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        return $result;
    }

}