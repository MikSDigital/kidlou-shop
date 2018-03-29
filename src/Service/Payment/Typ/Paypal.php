<?php

namespace App\Service\Payment\Typ;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class Paypal {

    /**
     *
     * @var type $data
     */
    private $express_data;

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
     * @var type httpParsedResponseAr
     */
    private $httpParsedResponseAr;

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
        //$this->setPaymentOrder($payment_order);
        //$this->setExpressOrder($paypal_order);
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
     * @param type $payment
     */
    private function setPaymentOrder($payment_order) {
        if ($paypal_order == 'createPayment') {
            $this->setSetExpressCheckout();
        } else if ($paypal_order == 'executePayment') {
            //$this->setDoExpressCheckout();
        }
    }

    /**
     *
     * @param type $paypal_order
     */
    private function setExpressOrder($paypal_order) {
        if ($paypal_order == 'setExpressCheckout') {
            $this->setSetExpressCheckout();
        } else if ($paypal_order == 'doExpressCheckout') {
            $this->setDoExpressCheckout();
        } else if ($paypal_order == 'getExpressCheckoutDetails') {
            $this->getTransactionDetails();
        }
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
        $api_endpoint = "https://api.sandbox.paypal.com/v1/oauth2/token";
        $clientId = "AbWC1ORqKB2pksz3s5gEDASVugIzv0MuF6m3fySR4ZZFOkPZRwGDNBx0l8zR31tAaMWoX0fdElZKKWPE";
        $secretKey = "EKL4JF0xTutP3Ldrdc3C86juGBLLyqRY79P4zZ4fO7D3Mtf3Oppv6-28CcrHCDYDIu7s1fYDhN65OlEa";
        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
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
        $data = $this->getOrderData();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment");
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
        $data['payer_id'] = $this->getRequest()->get('payerID');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment/" . $id . "/execute/ ");
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
     * @param type $order
     */
    private function setSetExpressCheckout() {
        $this->express_data = $this->getApiAccount();
        $this->express_data .= '&METHOD=SetExpressCheckout';
        $this->express_data .= '&PAYMENTREQUEST_0_PAYMENTACTION=' . urlencode("SALE");
        $itemid = 0;
        foreach ($this->getItems() as $item) {
            $this->express_data .= '&L_PAYMENTREQUEST_0_NAME' . $itemid . '=' . urlencode($item['name']);
            $this->express_data .= '&L_PAYMENTREQUEST_0_NUMBER' . $itemid . '=' . urlencode($item['sku']);
            $this->express_data .= '&L_PAYMENTREQUEST_0_DESC' . $itemid . '=' . urlencode($item['date_from']->format('d-m-Y'));
            $this->express_data .= '&L_PAYMENTREQUEST_0_AMT' . $itemid . '=' . urlencode(number_format($item['price'], 2));
            $this->express_data .= '&L_PAYMENTREQUEST_0_QTY' . $itemid . '=1';
// Date From To
            $date = new \DateTime($item['date_from']->format('Y-m-d'));
            $date->modify('+' . ($item['count_days'] - 1) . ' day');
            $this->express_data .= '&L_PAYMENTREQUEST_0_DESC' . $itemid . '=' . urlencode($item['date_from']->format('d-m-Y') . ' - ' . $date->format('d-m-Y'));
            $itemid++;
        }

// set item
        $this->express_data .= '&PAYMENTREQUEST_0_ITEMAMT=' . urlencode(number_format($this->getTotalPriceItems(), 2));
        $this->express_data .= '&PAYMENTREQUEST_0_SHIPPINGAMT=' . urlencode(number_format($this->getShippingPrice(), 2));
        $this->express_data .= '&PAYMENTREQUEST_0_HANDLINGAMT=' . urlencode(number_format($this->getCautionCost(), 2));
        $this->express_data .= '&PAYMENTREQUEST_0_AMT=' . urlencode(number_format($this->getPriceTotal(), 2));
        $this->express_data .= '&PAYMENTREQUEST_0_CURRENCYCODE=' . urlencode($this->getCommon()->getCurrencyCode());
        $this->express_data .= '&PAYMENTREQUEST_0_SHIPTONAME=' . urlencode($this->getShippingAddress()->getFirstname() . ' ' . $this->getShippingAddress()->getLastname());
        $this->express_data .= '&PAYMENTREQUEST_0_SHIPTOSTREET=' . urlencode($this->getShippingAddress()->getStreet());
        $this->express_data .= '&PAYMENTREQUEST_0_SHIPTOCITY=' . urlencode($this->getShippingAddress()->getCity());
        $this->express_data .= '&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=' . urlencode($this->getShippingAddress()->getCountryCode());
        $this->express_data .= '&PAYMENTREQUEST_0_SHIPTOZIP=' . urlencode($this->getShippingAddress()->getPostCode());
        $this->express_data .= '&PAYMENTREQUEST_0_SHIPTOPHONENUM=' . urlencode($this->getShippingAddress()->getPhone());

        $this->express_data .= '&CARTBORDERCOLOR=87c540';
        $this->express_data .= '&RETURNURL=' . urlencode($this->getContainer()->get('router')->generate('checkout_order_paypal_return', array(), UrlGeneratorInterface::ABSOLUTE_URL));
        $this->express_data .= '&CANCELURL=' . urlencode($this->getContainer()->get('router')->generate('checkout_order_paypal_cancel', array(), UrlGeneratorInterface::ABSOLUTE_URL));
        return $this;
    }

    /**
     *
     * @param type $order
     */
    private function setDoExpressCheckout() {
        $this->express_data = $this->getApiAccount();
        $this->express_data = '&TOKEN=' . urlencode($this->getRequest()->request->get('token'));
        $this->express_data .= '&PAYERID=' . urlencode($this->getRequest()->request->get('PayerID'));
        $this->express_data .= '&PAYMENTREQUEST_0_PAYMENTACTION=' . urlencode("SALE");
        $itemid = 1;
        foreach ($this->getItems() as $item) {
            $this->express_data .= '&L_PAYMENTREQUEST_0_NAME' . $itemid . '=' . urlencode($item->getName());
            $this->express_data .= '&L_PAYMENTREQUEST_0_NUMBER' . $itemid . '=' . urlencode($item->getSku());
            $this->express_data .= '&L_PAYMENTREQUEST_0_DESC' . $itemid . '=' . urlencode($item->getDateFrom()->format('d-m-Y'));
            $this->express_data .= '&L_PAYMENTREQUEST_0_AMT' . $itemid . '=' . urlencode($item->getPrice());
// Date From To
            $date = new \DateTime($item->getDateFrom()->format('Y-m-d'));
            $date->modify('+' . ($item->getTyp()->getCountDays() - 1) . ' day');
            $this->express_data .= '&L_PAYMENTREQUEST_0_DESC' . $itemid . '=' . urlencode($item->getDateFrom()->format('d-m-Y') . ' - ' . $date->format('d-m-Y'));
            $itemid++;
        }
// set item
        $this->express_data .= '&PAYMENTREQUEST_0_ITEMAMT=' . urlencode($this->getTotalPriceItems());
        $this->express_data .= '&PAYMENTREQUEST_0_SHIPPINGAMT=' . urlencode($this->getShippingPrice());
        $this->express_data .= '&PAYMENTREQUEST_0_HANDLINGAMT=' . urlencode($this->getCautionCost());
        $this->express_data .= '&PAYMENTREQUEST_0_AMT=' . urlencode($this->getPriceTotal());
        $this->express_data .= '&PAYMENTREQUEST_0_CURRENCYCODE=' . urlencode($this->getCommon()->getCurrencyCode());
// send data
        return $this;
    }

    /**
     * set express data
     */
    private function getTransactionDetails() {
        $this->express_data = '&TOKEN=' . urlencode($this->getRequest()->request->get('token'));
        return $this;
    }

    /**
     *
     * @return type
     */
    public function isExpressCheckoutResult() {
        if ("SUCCESS" == strtoupper($this->getExpressCheckoutResult()["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->getExpressCheckoutResult()["ACK"])) {
            return true;
//header('Location: ' . $paypalurl);
        } else {
//Show error message
            return false;
        }
    }

    /**
     *
     * @return type httpParsedResponseAr
     */
    public function getExpressCheckoutResult() {
        return $this->httpParsedResponseAr;
    }

    /**
     *
     * @return string $paypalurl
     */
    public function getExpressCheckoutTokenurl() {
        if ($this->getPaymentTyp()->getSandboxMode()) {
            $paypalmode = '.sandbox';
        }
//Redirect user to PayPal store with Token received.
        $paypalurl = 'https://www' . $paypalmode . '.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . $this->getExpressCheckoutResult()["TOKEN"] . '';

        return $paypalurl;
    }

    /**
     *
     * @return type string
     */
    private function getExpressData() {
        return $this->express_data;
    }

    /**
     *
     * @return type string
     */
    private function getApiAccount() {
        $username = urlencode($this->getPaymentTyp()->getApiUsername());
        $password = urlencode($this->getPaymentTyp()->getApiPassword());
        $signature = urlencode($this->getPaymentTyp()->getApiSignature());
        $version = urlencode('109.0');
        return 'PWD=' . $password . '&USER=' . $username . '&SIGNATURE=' . $signature . '&VERSION=' . $version . '&LOCALECODE=' . $this->getRequest()->getLocale() . '_' . strtoupper($this->getRequest()->getLocale());
    }

    /**
     *
     * @return type array
     */
    public function sendHttpPost() {
        $paypalmode = '';
        if ($this->getPaymentTyp()->getSandboxMode()) {
            $paypalmode = '.sandbox';
        }
        $api_endpoint = "https://api-3t" . $paypalmode . ".paypal.com/nvp";
//$version = urlencode('109.0');
// Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

// Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getExpressData());

// Get response from the server.
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }

// Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $this->httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $this->httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }
        if ((0 == sizeof($this->httpParsedResponseAr)) || !array_key_exists('ACK', $this->httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request(" . $this->getExpressData() . ") to " . $api_endpoint);
        }
        return $this;
    }

}
