<?php

namespace App\Service\Payment\Typ;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class Paypal {

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
     * @var type string
     */
    private $paypal_order;

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
        return $this->getEm()->getRepository(\App\Entity\Order::class)->findOneById($order_id);
    }

    /**
     *
     * @return type $number
     */
    private function getTotalPriceItems() {
        $items_price = 0;
        foreach ($this->getItems() as $item) {
            $date_from = new \DateTime($item['date_from']->format('d.m.Y'));
            $date_to = new \DateTime($item['date_to']->format('d.m.Y'));
            $interval = $date_from->diff($date_to);
            $count_days = $interval->format('%a');
            $items_price = $items_price + ($item['price'] * $count_days);
        }
        return $items_price;
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
     * @return type $items
     */
    private function getItems() {
        return $this->getEm()->getRepository(\App\Entity\Order::class)->getItemsByOrder($this->getCurrentOrder());
    }

    /**
     *
     * @return type Payment
     */
    private function setPaymentTyp() {
        $payment = $this->getEm()->getRepository(\App\Entity\Payment::class)->findOneBy(array('name' => 'Paypal'));
        $this->payment_typ = $this->getEm()->getRepository(\App\Entity\Payment\Paypal::class)->findOneBy(array('payment' => $payment));
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
     * @return type $address
     */
    private function getShippingAddress() {
        $address = $this->getEm()->getRepository(\App\Entity\Order\Address::class)->findOneBy(array('order' => $this->getCurrentOrder(), 'address_typ' => 'shipping'));
        return $address;
    }

    /**
     *
     * @param product $product
     * @return string product_name
     */
    private function _getProductItemName($product) {
        foreach ($product->getDescriptions() as $description) {
            if ($description->getLang() != NULL) {
                //if ($description->getLang()->getShortName() == $this->getRequest()->getLocale()) {
                if ($description->getLang()->getShortName() == 'fr') {
                    return $description->getName();
                }
            }
        }
    }

    /**
     * prepare items for paypal send
     */
    public function getOrderData() {
        $item_id = 0;
        $arr_data['intent'] = 'sale';
        $arr_data['redirect_urls']['return_url'] = $this->getContainer()->get('router')->generate('checkout_order_paypal_return', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $arr_data['redirect_urls']['cancel_url'] = $this->getContainer()->get('router')->generate('checkout_order_paypal_cancel', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $arr_data['payer']['payment_method'] = 'paypal';

        $arr_data['transactions'][0]['amount']['total'] = number_format($this->getPriceTotal(), 2);
        $arr_data['transactions'][0]['amount']['currency'] = $this->getCommon()->getCurrencyCode();
        $arr_data['transactions'][0]['amount']['details']['subtotal'] = number_format($this->getTotalPriceItems(), 2);
        $arr_data['transactions'][0]['amount']['details']['shipping'] = number_format($this->getShippingPrice() + $this->getCautionCost(), 2);


        foreach ($this->getItems() as $item) {
            $date_from = new \DateTime($item['date_from']->format('d.m.Y'));
            $date_to = new \DateTime($item['date_to']->format('d.m.Y'));
            $interval = $date_from->diff($date_to);
            $count_days = $interval->format('%a');
            $arr_data['transactions'][0]['item_list']['items'][$item_id]['quantity'] = $count_days;
            $arr_data['transactions'][0]['item_list']['items'][$item_id]['name'] = $item['name'];
            $arr_data['transactions'][0]['item_list']['items'][$item_id]['price'] = number_format($item['price'], 2);
            $arr_data['transactions'][0]['item_list']['items'][$item_id]['currency'] = $this->getCommon()->getCurrencyCode();
            $dates = $item['date_from']->format('d.m.Y') . ' - ' . $item['date_to']->format('d.m.Y');
            $arr_data['transactions'][0]['item_list']['items'][$item_id]['description'] = 'SKU: ' . $item['sku'] . ' DATES ' . $dates;
            $item_id++;
        }
        $arr_data['transactions'][0]['description'] = 'kidlou payment';
        $arr_data['transactions'][0]['invoice_number'] = $this->getCurrentOrder()->getOrderNumber();
        $arr_data['transactions'][0]['custom'] = 'merchant custom data';
        return json_encode($arr_data);
    }

    /**
     * Get paypal token
     * @return paypal
     */
    public function createAccessToken() {
        if ($this->getPaymentTyp()->getSandboxMode()) {
            $api_url = "https://api.sandbox.paypal.com/v1/oauth2/token";
        } else {
            $api_url = "https://api.paypal.com/v1/oauth2/token";
        }
        $clientId = $this->getPaymentTyp()->getClientId();
        $secretKey = $this->getPaymentTyp()->getSecretKey();
        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secretKey);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        // Get response from the server.
        $result = curl_exec($ch);

        if (!$result) {
            exit("_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }
        $json = json_decode($result);

        $this->setAccessToken($json->access_token);
        return $this;
    }

    /**
     *
     * @param type $access_token
     */
    public function setAccessToken($access_token) {
        $this->getContainer()->get('session')->set('paypal_access_token', $access_token);
    }

    /**
     *
     * @param type $access_token
     */
    public function getAccessToken() {
        return $this->getContainer()->get('session')->get('paypal_access_token');
    }

    /**
     * Create Payment
     */
    public function createPayment() {
        if ($this->getPaymentTyp()->getSandboxMode()) {
            $api_url = "https://api.sandbox.paypal.com/v1/payments/payment";
        } else {
            $api_url = "https://api.paypal.com/v1/payments/payment";
        }
        $data = $this->getOrderData();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->getAccessToken(),
            "Content-length: " . strlen($data)));
        $result = curl_exec($ch);
        if (!$result) {
            exit("_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }
    }

    /**
     *
     * @param type $id
     * @param type $payer_id
     */
    public function executePayment() {
        $id = $this->getRequest()->get('paymentID');
        if ($this->getPaymentTyp()->getSandboxMode()) {
            $api_url = "https://api.sandbox.paypal.com/v1/payments/payment/" . $id . "/execute/ ";
        } else {
            $api_url = "https://api.paypal.com/v1/payments/payment/" . $id . "/execute/ ";
        }
        $data['payer_id'] = $this->getRequest()->get('payerID');
        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->getAccessToken(),
            "Content-length: " . strlen($data)));
        $result = curl_exec($ch);
        if (!$result) {
            exit("_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }
    }

}
