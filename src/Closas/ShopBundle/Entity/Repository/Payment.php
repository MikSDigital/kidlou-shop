<?php

namespace Closas\ShopBundle\Entity\Repository;

/**
 * Calendar
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Payment extends \Doctrine\ORM\EntityRepository {

    public function getAllEnabled() {
        $payments = $this->createQueryBuilder('p')
                ->where('p.status = 1')
                ->getQuery()
                ->getResult();
    }

}
