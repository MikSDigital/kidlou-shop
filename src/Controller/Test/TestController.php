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
        $this->container->get('session')->set('quote_id', 145);
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
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('shop/cart/empty.html.twig');
        }
        //first save order
        try {

            $paymenttyp_id = 3; // 3 is paypal typ
            $serviceOrder->save($paymenttyp_id);
        } catch (UniqueConstraintViolationException $ex) {
            return $this->render('shop/checkout/duplicate.html.twig', array(
                        'zoneplz' => '1971',
                        'zonecity' => 'Grimisuat')
            );
        } catch (Exception $ex) {
            return $this->render('shop/checkout/failed.html.twig');
        }

        $paypal = $serviceOrder->setPaypal()->getPaypal();
        return $paypal->createPayment();
    }

    /**
     * @Route("/executepaypal/", name="test_execute_paypal")
     */
    public function executePaypalAction() {
        $this->container->get('session')->get('quote_id');
    }

}
