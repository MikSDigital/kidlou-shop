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

    protected function getCommon() {
        return $this->common;
    }

    public function getId($product) {
        return $this->getCommon()->encrypt($this->secret_key . '__' . $product->getSku() . '__' . time() . '__' . $product->getId(), $this->key);
    }

    public function getProduct1($encryptstr) {
        $arr_decrypt = explode('__', $this->getCommon()->decrypt($encryptstr, $this->key));
        $product_id = trim($arr_decrypt[count($arr_decrypt) - 1]);
        $product = $this->em
                ->getRepository(Product::class)
                ->findOneById($product_id);
        //
        return $product;
    }

    public function getProduct($url_key) {
        $product = $this->em
                ->getRepository(Product::class)
                ->findOneBy(array('url_key' => $url_key));
        return $product;
    }

}
