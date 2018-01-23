<?php

namespace App\Service\Payment\Typ;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;

class Post {

    /**
     *
     * @var type $data
     */
    private $data = array();

    /**
     *
     * @var type $data_out
     */
    private $data_out = array();

    /**
     *
     * @var type $data_params_hash
     */
    private $data_params_hash = array();

    /**
     *
     * @var request
     */
    private $request;

    /**
     *
     * @var em EntityManager
     */
    private $em;

    /**
     *
     * @var type container
     */
    private $container;

    /**
     *
     * @var type common
     */
    private $common;

    /**
     *
     * @var type arr_typ
     */
    private $payment_typ;

    /**
     *
     * @param type $em
     * @param type $request
     * @param type $container
     */
    public function __construct($em, $request, $container, $common) {
        $this->em = $em;
        $this->request = $request;
        $this->container = $container;
        $this->common = $common;
        $this->setPaymentTyp();
        $this->setFormFieldsValues();
        $this->setSort();
        $this->setShasign();
        $this->setHashCodeOut();
    }

    /**
     *
     * @return type $em
     */
    private function getEm() {
        return $this->em;
    }

    /**
     *
     * @return type $request
     */
    private function getRequest() {
        return $this->request;
    }

    /**
     *
     * @return type $container
     */
    private function getContainer() {
        return $this->container;
    }

    /**
     *
     * @return type $common
     */
    private function getCommon() {
        return $this->common;
    }

    /**
     *
     * @return type $order
     */
    private function getCurrentOrder() {
        $order_id = $this->getContainer()->get('session')->get('order_id');
        return $this->getEm()->getRepository(Order::class)->findOneById($order_id);
    }

    /**
     *
     * @return type $number
     */
    private function getTotalPriceItems() {
        return $this->getEm()->getRepository(Order::class)->getTotalPriceItems($this->getCurrentOrder())['price'];
    }

    /**
     *
     * @return type shippingPrice
     */
    private function getShippingPrice() {
        return $this->getCommon()->setTotalPrice($this->getTotalPriceItems())->getShippingCost();
    }

    /**
     *
     * @return type
     */
    private function getCautionCost() {
        return $this->getCommon()->getCautionCost();
    }

    /**
     *
     * @return type $priceTotal
     */
    private function getPriceTotal() {
        return $this->getTotalPriceItems() + $this->getShippingPrice() + $this->getCautionCost();
    }

    /**
     *
     * @return type Payment
     */
    private function setPaymentTyp() {
        $payment = $this->getEm()->getRepository(Payment::class)->findOneBy(array('name' => 'Post'));
        $this->payment_typ = $this->getEm()->getRepository(Payment\Post::class)->findOneBy(array('payment' => $payment));
    }

    /**
     *
     * @return type $payment
     */
    public function getPaymentTyp() {
        return $this->payment_typ;
    }

    /**
     *
     * @param type $order
     */
    private function setFormFieldsValues() {
        $this->data['AMOUNT'] = (number_format($this->getPriceTotal(), 2) * 100);
        $this->data['CURRENCY'] = $this->getCommon()->getCurrencyCode();
        $this->data['LANGUAGE'] = $this->getRequest()->getLocale() . '_' . strtoupper($this->getRequest()->getLocale());
        $this->data['ORDERID'] = $this->getCurrentOrder()->getOrderNumber();
        $this->data['PSPID'] = $this->getPaymentTyp()->getPspid();
        $this->data['TITLE'] = $this->getPaymentTyp()->getTitle();
        $this->data['BGCOLOR'] = $this->getPaymentTyp()->getBgColor();
        $this->data['TXTCOLOR'] = $this->getPaymentTyp()->getTxtColor();
        $this->data['TBLBGCOLOR'] = $this->getPaymentTyp()->getTableBgColor();
        $this->data['TBLTXTCOLOR'] = $this->getPaymentTyp()->getTableTxtColor();
        $this->data['BUTTONBGCOLOR'] = $this->getPaymentTyp()->getButtonBgColor();
        $this->data['BUTTONTXTCOLOR'] = $this->getPaymentTyp()->getButtonTxtColor();
//        if ($this->getLogo() != "") {
//            $this->data['LOGO'] = $this->getLogo();
//        }
        $this->data['FONTTYPE'] = $this->getPaymentTyp()->getFontType();
        $this->data['HOMEURL'] = $this->getContainer()->get('router')->generate('checkout_order_payment_home', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->data['ACCEPTURL'] = $this->getContainer()->get('router')->generate('checkout_order_post_success', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->data['DECLINEURL'] = $this->getContainer()->get('router')->generate('checkout_order_post_decline', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->data['CANCELURL'] = $this->getContainer()->get('router')->generate('checkout_order_post_cancel', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->data['EXCEPTIONURL'] = $this->getContainer()->get('router')->generate('checkout_order_post_exception', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->data['BACKURL'] = $this->getContainer()->get('router')->generate('checkout_order_post_back', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        // sortieren
        //ksort($this->data, SORT_STRING);
        foreach ($this->data as $name => $data) {
            if ($data == '') {
                unset($this->data[$name]);
            }
        }
    }

    /**
     * set shasign
     */
    private function setShasign() {
        $secret_key = $this->getPaymentTyp()->getSecretKey();
        $shasign = '';
        foreach ($this->data as $name => $data) {
            $shasign .= $name . '=' . $data . $secret_key;
        }
        $this->data['SHASIGN'] = strtoupper(sha1($shasign));
    }

    /**
     * set sort
     */
    private function setSort() {
        ksort($this->data, SORT_STRING);
    }

    /**
     *
     * @return type
     */
    public function getFormFieldsValues() {
        return $this->data;
    }

    /**
     *
     * @param type $params
     * @return type $params
     */
    private function setParamsForHashCodeOut() {
        $params = $this->getRequest()->request->all();
        foreach ($params as $name => $data) {
            if ($name != 'SHASIGN' && $data != '') {
                $this->data_params_hash[strtoupper($name)] = $data;
            }
        }
        // sortiere alphabetisch
        ksort($this->data_params_hash, SORT_STRING);
        return $this;
    }

    /**
     *
     * @return type array
     */
    public function getParamsForHashCodeOut() {
        return $this->data_params_hash;
    }

    /**
     *
     * @return type
     */
    private function setHashCodeOut() {
        $payId = $this->getRequest()->request->get('PAYID');
        if ($payId) {
            $params = $this->setParamsForHashCodeOut()->getParamsForHashCodeOut();
            // setzte alle zusammen mit dem secretkey
            $secretKey = $this->getPaymentTyp()->getSecretKey();
            $hash = '';
            foreach ($params as $name => $data) {
//                if ($name != 'SHASIGN' && $data != '') {
                $hash .= $name . '=' . $data . $secretKey;
//                }
            }
            $this->data_out = strtoupper(sha1($hash));
        }
    }

    /**
     *
     * @return type $data_out
     */
    public function getHashCodeOut() {
        return $this->data_out;
    }

    /**
     *
     * @return type $string
     */
    public function getShasignOut() {
        return $this->getRequest()->request->get('SHASIGN');
    }

}
