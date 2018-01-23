<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Product As EntityProduct;
use App\Entity\Map\ProductAdditional;
use App\Service\Common;

class Product {

    /**
     *
     * @var requestStack
     */
    private $requestStack;

    /**
     *
     * @var trequest
     */
    private $request;

    /**
     *
     * @var common
     */
    private $common;

    /**
     *
     * @var em EntityManager
     */
    private $em;

    /**
     *
     * @var type integer
     */
    private $count_pages;

    /**
     *
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     * @param Common $common
     */
    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Common $common) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->setCurrentRequest();
        $this->common = $common;
    }

    protected function setCurrentRequest() {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    protected function getCommon() {
        return $this->common;
    }

    public function getItemsPage() {
        return $this->items_page;
    }

    public function getProducts($categories) {
        $arr_category = array();
        if (!is_array($categories)) {
            $arr_category[] = $categories->getId();
        }

        foreach ($categories as $category) {
            $arr_category[] = $category->getId();
        }

        $page = $this->request->get('page');
        $limit_page = $this->request->get('limit');
        if ($page == '') {
            $page = 1;
        }

        if ($limit_page == '') {
            $limit_page = $this->getCommon()->getItemsPage();
        }

        $offset = ($page * $limit_page) - $limit_page;

        $products = $this->em
                ->getRepository(EntityProduct::class)
                ->findCategories($arr_category, $offset, $limit_page);

        $this->setCountPages($arr_category);

        return $products;
    }

    private function setCountPages($arr_category) {
        $limit_page = $this->request->get('limit');
        if ($limit_page == '') {
            $limit_page = $this->getCommon()->getItemsPage();
        }

        $products = $this->em
                ->getRepository(EntityProduct::class)
                ->findCategories($arr_category);
        $this->count_pages = ceil(count($products) / $limit_page);
    }

    public function getCountPages() {
        return $this->count_pages;
    }

    public function setEncryptByProducts($products) {
        foreach ($products as $product) {
            $encrypt = $this->getEncrypt($product);
            $product->setEncryptVirtuelId($encrypt);
            $this->setCurrentDescriptionByLocale($product);
        }
    }

    public function getEncrypt($product) {
        return $this->getCommon()->encrypt($product);
    }

    public function getProduct($url_key) {
        $product = $this->em
                ->getRepository(EntityProduct::class)
                ->findOneBy(array('url_key' => $url_key));
//
//
        //return $this->setCurrentDescriptionByLocale($product);
        return $product;
    }

    public function getProduct1($encryptstr) {
        $arr_decrypt = explode('__', $this->getCommon()->decrypt($encryptstr));
        $product_id = trim($arr_decrypt[count($arr_decrypt) - 1]);
        $product = $this->em
                ->getRepository(EntityProduct::class)
                ->findOneById($product_id);
//
        return $this->setCurrentDescriptionByLocale($product);
    }

    protected function setCurrentDescriptionByLocale($product) {
        $lang_key = 0;
        if ($product != NULL) {
            foreach ($product->getDescriptions() as $key => $description) {
                if (!is_null($description->getLang())) {
                    if ($description->getLang()->getShortName() == $this->request->getLocale()) {
                        $lang_key = $key;
                    }
                }
            }

            foreach ($product->getDescriptions() as $key => $description) {
                $product->setDescription($lang_key);
            }
            return $product;
        }

        return NULL;
    }

    public function getChildrenByParent($id) {
        $product = $this->getProduct($id);
        $children = $this->em
                ->getRepository(ProductAdditional::class)
                ->findByParent($product->getId());

        $products = new ArrayCollection();
        foreach ($children as $child) {
            $products[] = $this->em
                    ->getRepository(EntityProduct::class)
                    ->findOneById($child->getChildren());
        }
        $children = new ArrayCollection();
        foreach ($products as $product) {
            $children[] = $this->setCurrentDescriptionByLocale($product);
        }

        return $children;
    }

    public function getChildrenByRealParent($id) {
        $children = $this->em
                ->getRepository(ProductAdditional::class)
                ->findByParent($id);

        $products = new ArrayCollection();
        foreach ($children as $child) {
            $products[] = $this->em
                    ->getRepository(EntityProduct::class)
                    ->findOneById($child->getChildren());
        }

        return $products;
    }

}
