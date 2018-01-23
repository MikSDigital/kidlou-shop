<?php

namespace App\Repository;

/**
 * Product
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Product extends \Doctrine\ORM\EntityRepository {

    /**
     *
     * @param type $arr_categories
     * @return type array
     */
    public function findCategories($arr_categories, $offset = '', $limit = '') {
        if (!empty($limit)) {
            return $this->createQueryBuilder('p')
                            ->innerJoin('p.categories', 'c')
                            ->where('c.id IN (:arrcategories) AND p.status = 1')
                            ->setParameter('arrcategories', $arr_categories)
                            ->setFirstResult($offset)
                            ->setMaxResults($limit)
                            ->getQuery()
                            ->getResult();
        } else {
            return $this->createQueryBuilder('p')
                            ->innerJoin('p.categories', 'c')
                            ->where('c.id IN (:arrcategories) AND p.status = 1')
                            ->setParameter('arrcategories', $arr_categories)
                            ->getQuery()
                            ->getResult();
        }
    }

    /**
     *
     * @param type $name
     * @param type $lang
     * @return type array
     */
    public function findShopProducts($name, $lang) {
        $query = $this->getEntityManager()
                ->createQuery(
                        "SELECT p.sku, p.url_key, pd.name, IFNULL(pi.name,'placeholder50.jpg') AS image, IFNULL(pis.path,'media/placeholder/') AS path FROM App\Entity\Product p
                                INNER JOIN App\Entity\Product\Description pd WITH p.id = pd.product AND
                                (p.sku LIKE :name
                                OR pd.name LIKE :name
                                OR pd.long_text LIKE :name
                                OR pd.short_text LIKE :name
                                OR pd.indicies LIKE :name)
                                INNER JOIN App\Entity\Product\Typ pt WITH p.typ = pt.id AND pt.name = 'simple'
                                INNER JOIN App\Entity\Language l WITH pd.lang = l.id AND l.short_name = :lang
                                LEFT JOIN App\Entity\Product\Image pi WITH p.id = pi.product AND pi.is_default = 1 AND pi.size = 1
                                LEFT JOIN App\Entity\Product\Image\Size pis WITH pi.size = pis.id
                                GROUP BY p.sku"
                )
                ->setParameter('name', '%' . $name . '%')
                ->setParameter('lang', $lang);
        return $query->getResult();
    }

    /**
     *
     * @param type $sku
     * @return type $products
     */
    public function findRelationProducts($skulike, $sku) {
        return $this->createQueryBuilder('p')
                        ->where('p.sku LIKE :skulike AND p.status = 1 AND p.sku != :sku')
                        ->setParameter('skulike', $skulike . '%')
                        ->setParameter('sku', $sku)
                        ->getQuery()
                        ->getResult();
    }

}
