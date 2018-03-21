<?php

namespace App\Controller\Test;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Payment As ServicePayment;
use App\Service\Order As ServiceOrder;

class TestController extends Controller {

    /**
     * @Route("/token/", name="test_token")
     */
    public function tokenAction() {
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
        return $json->access_token;
    }

    /**
     * @Template()
     * @Route("/cart/", name="test_checkout_cart")
     */
    public function cartAction(Request $request, ServicePayment $servicePayment) {
        $locale = $request->getLocale();
        $payments = $servicePayment->getPayments();
        return array(
            'payments' => $payments,
            'zoneplz' => '1971',
            'zonecity' => 'Grimisuat'
        );
    }

    /**
     * @Route("/order/", name="checkout_order")
     */
    public function orderAction(Request $request, ServiceOrder $serviceOrder) {
        $order = $this->getDoctrine()->getRepository(\App\Entity\Order::class)->findOneById('');
        $this->container->get('session')->set('order_id', $order->getId());
        $serviceOrder->setPaypal('createPayment')->sendHttpPost();
        $serviceOrder->setPaypal()->createPayment()->sendHttpPost();
        $paypal = $serviceOrder->setPaypal();
    }

    /**
     * @Route("/createpaypal/", name="test_create_paypal")
     */
    public function createPaypalAction() {
        $this->container->get('session')->get('quote_id');
    }

    /**
     * @Route("/executepaypal/", name="test_execute_paypal")
     */
    public function executePaypalAction() {
        $this->container->get('session')->get('quote_id');
    }

}
