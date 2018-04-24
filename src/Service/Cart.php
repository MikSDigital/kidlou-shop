<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
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
     * @var container
     */
    private $container;

    /**
     *
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     * @param \App\Service\Common $common
     */
    public function __construct(EntityManager $entityManager, Common $common, Container $container) {
        $this->common = $common;
        $this->em = $entityManager;
        $this->container = $container;
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
     * @param type $coupon
     * @param type $lang
     * @return type
     */
    public function getCoupon($coupon, $lang) {
        $coupon = $this->em
                ->getRepository(\App\Entity\Gift::class)
                ->getCoupon($coupon, $lang);
        return $coupon;
    }

    /**
     *
     * @param type $coupon
     * @param type $lang
     * @return counter
     */
    public function setCouponCounter($code, $is_active = TRUE) {
        $quote_id = $this->container->get('session')->get('quote_id');
        $order_id = $this->container->get('session')->get('order_id');
        $coupon = $this->em->getRepository(\App\Entity\Gift\Coupon::class)->findOneById($code);
        $quote = $this->em->getRepository(\App\Entity\Quote::class)->findOneById($quote_id);
        $order = $this->em->getRepository(\App\Entity\Order::class)->findOneById($order_id);
        $counter = new \App\Entity\Gift\Coupon\Counter();
        $counter->setCoupon($coupon);
        $counter->setQuote($quote);
        $counter->setOrder($order);
        $counter->setCreatedAt(new \DateTime());
        $counter->setIsActive($is_active);
        $this->em->persist($counter);
        $this->em->flush();
        return $counter;
    }

    /**
     *
     * @param type $percent
     * @param type $description
     */
    public function setAmount($percent, $description) {
        $amount_cost = $this->container->get('session')->get('price_subtotal') / 100;
        $amount_cost = number_format($amount_cost * $percent, 2);
        $this->container->get('session')->set('amount_subtotal_cost', $amount_cost);
        $this->container->get('session')->set('amount_description', $description);
    }

}
