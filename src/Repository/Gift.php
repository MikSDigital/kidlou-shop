<?php

namespace App\Repository;

/**
 * Gift
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Gift extends \Doctrine\ORM\EntityRepository {

    public function getCoupon($code, $lang) {
        $date = new \DateTime();
        return $this->createQueryBuilder('g')
                        ->addSelect('co.code, g.percent, te.description, g.max_uses, co.counter')
                        ->innerJoin('g.coupons', 'co')
                        ->innerJoin('g.texts', 'te')
                        //->where(':date >= g.date_from AND :date <= g.date_to AND g.isActive = 1 AND co.code = :code AND co.counter <= g.max_uses AND te.gift = g AND te.lang = :lang')
                        ->where(':date >= g.date_from AND :date <= g.date_to AND g.isActive = 1 AND co.code = :code AND te.gift = g AND te.lang = :lang')
                        ->setParameter('date', $date)
                        ->setParameter('date', $date)
                        ->setParameter('code', $code)
                        ->setParameter('lang', $lang)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

}
