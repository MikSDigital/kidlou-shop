<?php

namespace App\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use App\RepositoryClasses\Product AS ProductClass;

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

    /**
     *
     * @param type $url_key
     * @param type $lang
     * @return type products
     */
    public function getDetailProduct($url_key, $lang, $img_product_size, $img_children_size) {
        if ($this->hasChildren($url_key)) {
            return $this->getParentChildrenProduct($url_key, $lang, $img_product_size, $img_children_size);
        } else {
            return $this->getParentProduct($url_key, $lang, $img_product_size);
        }
    }

    /**
     *
     * @param type $url_key
     * @param type $lang
     * @return type products
     */
    public function getParentChildrenProduct($url_key, $lang, $img_product_size, $img_children_size) {
        $arr_data = new ArrayCollection();
        $query = $this->getEntityManager()
                ->createQuery(
                        "SELECT
                                p.id AS product_id,

                                p.sku,

                                p.url_key,

                                price.value,

                                pma.children AS children_id,

                                pd.name,

                                pd.indicies,

                                pd.accessoires,

                                pd.long_text,

                                pd.short_text,

                                IFNULL(GROUP_CONCAT(DISTINCT img.original_name SEPARATOR ','),'" . $img_product_size . ".jpg') AS product_image_original_names,

                                IFNULL(GROUP_CONCAT(DISTINCT size.path, img.name SEPARATOR ','),'media/placeholder/" . $img_product_size . ".jpg') AS product_image_names,

                                (SELECT child_price.value FROM App\Entity\Product child_p
                                    INNER JOIN App\Entity\Price child_price
                                    WITH child_p.id = child_price.product
                                    WHERE child_price.product = children_id) AS children_price,

                                (SELECT GROUP_CONCAT(child_pd.name SEPARATOR ',') FROM App\Entity\Product\Description child_pd
                                    INNER JOIN App\Entity\Language child_lang
                                    WITH child_pd.lang = child_lang.id AND child_lang.short_name = :lang
                                    WHERE child_pd.product = children_id) AS children_name,

                                (SELECT IFNULL(GROUP_CONCAT(child_original_img.original_name SEPARATOR ','),'placeholder" . $img_children_size . ".jpg') FROM App\Entity\Product\Image child_original_img
                                    INNER JOIN App\Entity\Product\Image\Size child_original_size
                                    WITH child_original_img.size = child_original_size.id
                                    AND child_original_size.name = :img_children_name
                                    WHERE child_original_img.product = children_id) AS children_image_original_names,

                                (SELECT IFNULL(GROUP_CONCAT(child_size.path, child_img.name SEPARATOR ','),'media/placeholder/placeholder" . $img_children_size . ".jpg') FROM App\Entity\Product\Image child_img
                                    INNER JOIN App\Entity\Product\Image\Size child_size
                                    WITH child_img.size = child_size.id
                                    AND child_size.name = :img_children_name
                                    WHERE child_img.product = children_id) AS children_image_names

                                    FROM App\Entity\Product p
                                        INNER JOIN App\Entity\Map\ProductAdditional pma WITH p.id = pma.parent AND p.url_key = :url_key AND p.status = 1
                                        INNER JOIN App\Entity\Product pchild WITH pma.children = pchild.id AND pchild.status = 1
                                        INNER JOIN App\Entity\Product\Description pd WITH p.id = pd.product
                                        INNER JOIN App\Entity\Language lang WITH pd.lang = lang.id AND lang.short_name = :lang
                                        INNER JOIN App\Entity\Product\Image img WITH p.id = img.product
                                        INNER JOIN App\Entity\Price price WITH p.id = price.product
                                        INNER JOIN App\Entity\Product\Image\Size size WITH img.size = size.id AND size.name = :img_product_name
                                        GROUP BY pma.children"
                )
                ->setParameter('url_key', $url_key)
                ->setParameter('img_product_name', 'image' . $img_product_size)
                ->setParameter('img_children_name', 'image' . $img_children_size)
                ->setParameter('lang', $lang);
        $arr_data = $query->getResult();
        $product = new ProductClass($arr_data[0]);
        $original_images = explode(',', $arr_data[0]['product_image_original_names']);
        $images = explode(',', $arr_data[0]['product_image_names']);
        foreach ($original_images as $key => $original_image) {
            $product->addImages($original_image, $images[$key]);
        }

        foreach ($arr_data as $data) {
            $product->addChildren($data);
            $original_images = explode(',', $data['children_image_original_names']);
            $images = explode(',', $data['children_image_names']);
            foreach ($original_images as $key => $original_image) {
                $product->getCurrentChildren()->addImages($original_image, $images[$key]);
            }
        }
        return $product;
    }

    /**
     *
     * @param type $url_key
     * @param type $lang
     * @return type products
     */
    public function getParentProduct($url_key, $lang, $img_product_size) {
        $arr_data = new ArrayCollection();
        $query = $this->getEntityManager()
                ->createQuery(
                        "SELECT
                                p.id AS product_id,

                                p.sku,

                                p.url_key,

                                price.value,

                                pd.name,

                                pd.indicies,

                                pd.accessoires,

                                pd.long_text,

                                pd.short_text,

                                IFNULL(GROUP_CONCAT(DISTINCT img.original_name SEPARATOR ','),'" . $img_product_size . ".jpg') AS product_image_original_names,

                                IFNULL(GROUP_CONCAT(DISTINCT size.path, img.name SEPARATOR ','),'media/placeholder/" . $img_product_size . ".jpg') AS product_image_names

                                FROM App\Entity\Product p
                                    INNER JOIN App\Entity\Product\Description pd WITH p.id = pd.product AND p.url_key = :url_key AND p.status = 1
                                    INNER JOIN App\Entity\Language lang WITH pd.lang = lang.id AND lang.short_name = :lang
                                    INNER JOIN App\Entity\Product\Image img WITH p.id = img.product
                                    INNER JOIN App\Entity\Price price WITH p.id = price.product
                                    INNER JOIN App\Entity\Product\Image\Size size WITH img.size = size.id AND size.name = :img_product_name
"
                )
                ->setParameter('url_key', $url_key)
                ->setParameter('img_product_name', 'image' . $img_product_size)
                ->setParameter('lang', $lang);
        $arr_data = $query->getResult();
        $product = new ProductClass($arr_data[0]);
        $original_images = explode(',', $arr_data[0]['product_image_original_names']);
        $images = explode(',', $arr_data[0]['product_image_names']);
        foreach ($original_images as $key => $original_image) {
            $product->addImages($original_image, $images[$key]);
        }

        return $product;
    }

    /**
     * Diese Methode prüft ob Produkt children hat
     * @return boolean
     */
    private function hasChildren($url_key) {
        // check if has children
        $arr_data = new ArrayCollection();
        $query = $this->getEntityManager()
                ->createQuery("SELECT pma.parent
                                    FROM App\Entity\Product p
                                    INNER JOIN App\Entity\Map\ProductAdditional pma WITH p.id = pma.parent AND p.url_key = :url_key AND p.status = 1
                                ")
                ->setParameter('url_key', $url_key);
        $arr_data = $query->getResult();
        if ($arr_data) {
            return TRUE;
        }
        return FALSE;
    }

}
