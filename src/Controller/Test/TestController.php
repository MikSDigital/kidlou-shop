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
     * @Template()
     * @Route("/cart/", name="test_checkout_cart")
     */
    public function cartAction(Request $request, ServicePayment $servicePayment) {
        $this->container->get('session')->set('quote_id', 155);
        $locale = $request->getLocale();
        $payments = $servicePayment->getPayments();
        return array(
            'payments' => $payments,
            'zoneplz' => '1971',
            'zonecity' => 'Grimisuat'
        );
    }

    /**
     * @Route("/createpaypal/", name="test_create_paypal")
     */
    public function createPaypalAction(Request $request, ServiceOrder $serviceOrder) {
        $serviceOrder->save();



//        print_r($request->request->get('billing'));
//        $this->container->get('session')->get('quote_id');
//        $order = $this->getDoctrine()->getRepository(\App\Entity\Order::class)->findOneById('');
//        $this->container->get('session')->set('order_id', $order->getId());
//        $serviceOrder->setPaypal('createPayment')->sendHttpPost();
//        $paypal = $serviceOrder->setPaypal();
        return new \Symfony\Component\HttpFoundation\JsonResponse();
    }

    /**
     * @Route("/executepaypal/", name="test_execute_paypal")
     */
    public function executePaypalAction() {
        $this->container->get('session')->get('quote_id');
    }

}
