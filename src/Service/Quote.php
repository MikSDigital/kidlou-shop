<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Quote {
    /**
     *
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     * @param Container $container
     * @param \App\Service\Common $common
     *
     */

    /**
     *
     * @var em EntityManager
     */
    private $em;

    /**
     *
     * @var container $container
     */
    private $container;

    public function __construct(EntityManager $entityManager, Container $container) {
        $this->em = $entityManager;
        $this->container = $container;
        $this->getIsBasketInTime();
    }

    /**
     * check if basket is always in time
     */
    private function getIsBasketInTime() {
        $quote_id = $this->container->get('session')->get('quote_id');
        if ($quote_id) {
            $quote = $this->getCurrentQuote($quote_id);
            if (!isset($quote)) {
                $this->removeBasket();
            }
        }
    }

    /**
     *
     * @param type $quote_id
     * @return Quote
     */
    private function getCurrentQuote($quote_id) {
        return $this->em->getRepository(\App\Entity\Quote::class)->getCurrentQuote($quote_id);
    }

    /**
     * remove the data of basket
     *
     */
    private function removeBasket() {
        $this->container->get('session')->remove('quote_id');
        $this->container->get('session')->remove('basket_items');
        $this->container->get('session')->remove('price_subtotal');
    }

}
