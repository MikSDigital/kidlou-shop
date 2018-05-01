<?php

namespace App\Repository;

use DoctrineExtensions\Query\Mysql\GroupConcat;

/**
 * Order
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Order extends \Doctrine\ORM\EntityRepository {

    /**
     *
     * @param type $order
     * @return type $items
     */
    public function getTotalPriceItems($order) {

        $query = $this->getEntityManager()
                ->createQuery(
                        'SELECT sum(oi.price) AS price FROM App\Entity\Calendar c'
                        . ' INNER JOIN App\Entity\Order\Item oi WITH c.id = oi.calendar AND c.order = :order'
                )
                ->setParameter('order', $order);

        return $query->getOneOrNullResult();
    }

    /**
     *
     * @param type $order
     * @return type $items
     */
    public function getItemsByOrder($order) {

        $query = $this->getEntityManager()
                ->createQuery(
                        'SELECT oi.sku, oi.name, oi.price, c.date_from, c.date_to, DATE_DIFF(c.date_to, c.date_from) AS count_days FROM App\Entity\Calendar c'
                        . ' INNER JOIN App\Entity\Order\Item oi WITH c.id = oi.calendar AND c.order = :order'
                )
                ->setParameter('order', $order);

        return $query->getResult();
    }

    /**
     *
     * @param type $month
     * @param type $year
     * @param type $product
     * @return type $sql
     */
    public function getAllOrderDatas() {
        $query = $this->getEntityManager()
                ->createQuery(
                'SELECT o.id, o.order_number, l.name AS lang, o.created, os.name AS status, op.caution_cost + op.subtotal_cost + op.shipping_cost AS cost, GROUP_CONCAT(oa.firstname, \' \', oa.lastname, \'|\', oa.address_typ SEPARATOR \',\') AS address_name FROM App\Entity\Order o'
                . ' INNER JOIN App\Entity\Calendar c WITH o.id = c.order'
                . ' INNER JOIN App\Entity\Order\Item oi WITH oi.calendar = c.id'
                . ' INNER JOIN App\Entity\Order\Address oa WITH o.id = oa.order'
                . ' INNER JOIN App\Entity\Order\Status os WITH os.id = o.status'
                . ' INNER JOIN App\Entity\Order\Payment op WITH o.id = op.order'
                . ' INNER JOIN App\Entity\Language l WITH o.lang = l.id'
                . ' GROUP BY o.id'
        );

        return $query->getResult();
    }

    /**
     *
     * @param type $month
     * @param type $year
     * @param type $product
     * @return type $sql
     */
    public function getOrderData($id) {
        $query = $this->getEntityManager()
                ->createQuery(
                        'SELECT o.id, o.order_number, l.name AS lang, l.short_name AS local_code, o.created, os.name AS status,  op.caution_cost + op.subtotal_cost + op.shipping_cost AS cost, op.subtotal_cost, op.amount_subtotal_cost, op.amount_subtotal_description, op.amount_subtotal_code, op.shipping_cost, op.caution_cost, op.cash_cost, op.additional_information, op.payment_name,'
                        . ' (SELECT GROUP_CONCAT(DISTINCT oi_item.product_id, \'|\', IFNULL(oia_item.children_product,\'NULL\'), \'|\', DATE_FORMAT(c_item.date_from,\'%d-%m-%Y\'), \'|\', DATE_FORMAT(c_item.date_to,\'%d-%m-%Y\'), \'|\', DATE_DIFF(c_item.date_to, c_item.date_from),\'|\', oi_item.name, \'|\', oi_item.sku, \'|\', IFNULL(oia_item.parent_product,\'NULL\'), \'|\', oi_item.price '
                        . ' ORDER BY c_item.date_from,oi_item.id SEPARATOR \',\')'
                        . ' FROM App\Entity\Order o_item'
                        . ' INNER JOIN App\Entity\Calendar c_item WITH o_item.id=c_item.order'
                        . ' INNER JOIN App\Entity\Order\Item oi_item WITH c_item.id=oi_item.calendar'
                        . ' LEFT JOIN App\Entity\Map\OrderItemAdditional oia_item WITH oi_item.id=oia_item.children OR oi_item.id=oia_item.parent'
                        . ' WHERE o_item.id=o.id) AS item,'
                        . ' GROUP_CONCAT(DISTINCT oa.firstname, \' \', oa.lastname, \'|\', oa.street, \'|\', oa.post_code, \'|\', oa.city, \'|\', oa.country_code, '
                        . ' \'|\', IFNULL(oa.phone,\'NULL\'), \'|\', IFNULL(oa.mobile,\'NULL\'), \'|\', IFNULL(oa.email,\'NULL\'), \'|\', IFNULL(oa.address_typ,\'NULL\'), \'|\', IFNULL(u.username,\'NULL\'),  \'|\', IFNULL(oa.name,\'NULL\'), \'|\', IFNULL(oa.shipping_typ,\'NULL\')  SEPARATOR \',\') AS address_name '
                        . ' FROM App\Entity\Order o'
                        . ' INNER JOIN App\Entity\Calendar c WITH o.id = c.order AND o.id = :id'
                        . ' INNER JOIN App\Entity\Order\Item oi WITH oi.calendar = c.id'
                        . ' INNER JOIN App\Entity\Order\Address oa WITH o.id = oa.order'
                        . ' LEFT JOIN App\Entity\User u WITH oa.user = u.id'
                        . ' INNER JOIN App\Entity\Order\Status os WITH os.id = o.status'
                        . ' INNER JOIN App\Entity\Order\Payment op WITH o.id = op.order'
                        . ' INNER JOIN App\Entity\Language l WITH o.lang = l.id'
                        . ' LEFT JOIN App\Entity\Map\OrderItemAdditional oia WITH oi.id=oia.parent'
                        . ' GROUP BY o.id'
                )
                ->setParameter('id', $id);
        return $query->getOneOrNullResult();
    }

    /**
     *
     * @param type $product
     * @param type $month
     * @param type $year
     * @param type $days
     * @param type $arr_status
     * @return type order
     */
    public function getOrderDates($product, $month, $year, $days, $arr_status) {

        return $this->createQueryBuilder('o')
                        ->addSelect('ca')
                        //->addSelect('s')
                        ->innerJoin('o.calendars', 'ca')
                        ->innerJoin('o.status', 's')
                        ->where('ca.date_from >= :date_from AND ca.date_to <= :date_to')
                        ->andWhere('ca.product = :product')
                        ->andWhere('s.name IN (:statusname)')
                        ->setParameter('date_from', new \DateTime($year . '-' . $month . '-01'))
                        ->setParameter('date_to', new \DateTime($year . '-' . $month . '-' . $days))
                        ->setParameter('product', $product)
                        ->setParameter('statusname', array_values($arr_status))
                        ->getQuery()
                        ->getResult();
    }

}
