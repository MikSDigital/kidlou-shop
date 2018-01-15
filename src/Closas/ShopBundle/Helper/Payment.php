<?php

namespace Closas\ShopBundle\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Helper\Payment\Typ;

class Payment {

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

    protected function getRequest() {
        return $this->request;
    }

    protected function getCommon() {
        return $this->common;
    }

    protected function getEm() {
        return $this->em;
    }

    public function getPayments() {
        $arr_payments = array();
        $locale = $this->getRequest()->getLocale();
        $payments = $this->getEm()->getRepository('ClosasShopBundle:Payment')->findBy(array('status' => 1));
        $language = $this->getEm()->getRepository('ClosasShopBundle:Language')->findOneBy(array('short_name' => $locale));
        // payments
        foreach ($payments as $payment) {
            $paymentTyp = $this->getEm()->getRepository('ClosasShopBundle:Payment\\' . $payment->getName())
                    ->findOneBy(array('payment' => $payment));
            $name = strtolower($payment->getName());
            $label = $this->getEm()->getRepository('ClosasShopBundle:Payment\Lang\Label')
                    ->findOneBy(array($name => $paymentTyp, 'lang' => $language));
            if ($payment->getName() == 'Bank') {
                $arr_payments[] = new Typ($payment, $label, $paymentTyp, NULL, NULL, NULL, $this->getCommon());
            } elseif ($payment->getName() == 'Cash') {
                $arr_payments[] = new Typ($payment, $label, NULL, $paymentTyp, NULL, NULL, $this->getCommon());
            } elseif ($payment->getName() == 'Paypal') {
                $arr_payments[] = new Typ($payment, $label, NULL, NULL, $paymentTyp, NULL, $this->getCommon(), false);
            } elseif ($payment->getName() == 'Post') {
                $arr_payments[] = new Typ($payment, $label, NULL, NULL, NULL, $paymentTyp, $this->getCommon(), false);
            }
        }
        return $arr_payments;
    }

}
