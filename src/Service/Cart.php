<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Product;

class Cart {

    /**
     * secret key
     * @var string
     */
    private $secret_key = '$uTeaeUJhsU/&%';

    /**
     * key
     * @var string
     */
    private $key = 'kidlou.com';

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
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     * @param \App\Service\Common $common
     */
    public function __construct(EntityManager $entityManager, Common $common) {
        $this->common = $common;
        $this->em = $entityManager;
    }

    /**
     *
     * @return Common
     */
    protected function getCommon() {
        return $this->common;
    }

    /**
     *
     * @param type $product
     * @return Common
     */
    public function getId($product) {
        return $this->getCommon()->encrypt($this->secret_key . '__' . $product->getSku() . '__' . time() . '__' . $product->getId(), $this->key);
    }

    /**
     *
     * @param type $url_key
     * @return type
     */
    public function getProduct($url_key) {
        $product = $this->em
                ->getRepository(Product::class)
                ->findOneBy(array('url_key' => $url_key));
        return $product;
    }

    /**
     *
     */
    public function addCoupon($coupon) {

        $product = $this->em
                ->getRepository(Product::class)
                ->findOneBy(array('url_key' => $url_key));
        return $product;
    }

}
