<?php

namespace Closas\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Closas\ShopBundle\Helper\Payment As HelperPayment;

/**
 * @Route("/checkout")
 */
class CheckoutController extends Controller {

    /**
     * @Template()
     * @Route("/cart/", name="checkout_cart")
     */
    public function cartAction(Request $request, HelperPayment $helperPayment) {
        $locale = $request->getLocale();
        $payments = $helperPayment->getPayments();
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle:Cart:empty.html.twig');
        }
        // if session login failed ist unter handler authentification handler
        if ($this->container->get('session')->get('login_failed')) {
            return array(
                'payments' => $payments,
                'zoneplz' => $this->container->get('session')->get('zone-plz'),
                'zonecity' => $this->container->get('session')->get('zone-city')
            );
        }
        if ($request->request->get('zone-plz') == '' && $request->request->get('zone-city') == '') {
            return new RedirectResponse($this->generateUrl('index_cart'));
        }
        return array(
            'payments' => $payments,
            'zoneplz' => $request->request->get('zone-plz'),
            'zonecity' => $request->request->get('zone-city')
        );
    }

    /**
     * @Template()
     * @Route("/order/", name="checkout_order")
     */
    public function orderAction(Request $request) {
        //first save order
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle:Cart:empty.html.twig');
        }
        //first save order
        try {
            $helperOrder = $this->get('helper.order')->save();
        } catch (UniqueConstraintViolationException $ex) {
            return $this->render('ClosasShopBundle:Checkout:duplicate.html.twig', array(
                        'zoneplz' => $request->request->get('shipping')['post_code'],
                        'zonecity' => $request->request->get('shipping')['city'])
            );
        } catch (Exception $ex) {
            return $this->render('ClosasShopBundle:Checkout:failed.html.twig');
        }

        // redirect to payment institut
        $url = '';
        if ($helperOrder == FALSE) {
            return $this->render('ClosasShopBundle:Checkout:failed.html.twig');
        }
        $order = $this->get('helper.order')->getCurrentOrder();
        // Daten verarbeitung
        // in get url wird in die demenstprechende url redirectet
        // check which paymenttyp
        if ($this->get('helper.order')->getPaymentName() == 'Post') {
            $post = $this->get('helper.order')->setPost()->getPost();
            return $this->render('ClosasShopBundle:Checkout:postForm.html.twig', array(
                        'fields' => $post->getFormFieldsValues(),
                        'url' => $post->getPaymentTyp()->getFormularUrl()));
        } else if ($this->get('helper.order')->getPaymentName() == 'Paypal') {
            if ($order && $request->request->get('token') != '' && $request->request->get('token') != '') {
                $paypal = $this->get('helper.order')->setPaypal('doExpressCheckout')->sendHttpPost();
                if ($paypal->isExpressCheckoutResult()) {
                    $this->get('helper.order')->setOrderStatus('complete');
                    // set additional information
                    $paypal = $this->get('helper.order')->setPaypal('getExpressCheckoutDetails')->sendHttpPost();
                    $this->get('helper.order')->setAdditionalInformation($paypal->getExpressCheckoutResult());
                    $this->removeSessions();
                    return $this->render('ClosasShopBundle:Checkout:paypalSuccess.html.twig', array('order' => $order));
                } else {
                    // set status canceled
                    $this->get('helper.order')->setOrderStatus('canceled');
                    $this->get('helper.order')->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                    $this->removeSessions();
                    return $this->render('ClosasShopBundle:Checkout:paypalFailed.html.twig', array('paypalmessage' => $paypal->getExpressCheckoutResult()['L_LONGMESSAGE0']));
                }
            } else {
                $paypal = $this->get('helper.order')->setPaypal('setExpressCheckout')->getPaypal()->sendHttpPost();
                if ($paypal->isExpressCheckoutResult()) {
                    return $this->redirect($paypal->getExpressCheckoutTokenurl());
                } else {
                    // set status canceled
                    $this->get('helper.order')->setOrderStatus('canceled');
                    $this->get('helper.order')->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                    $this->removeSessions();
                    return $this->render('ClosasShopBundle:Checkout:paypalFailed.html.twig', array('paypalmessage' => urldecode($paypal->getExpressCheckoutResult()['L_LONGMESSAGE0'])));
                }
            }
        } else if ($this->get('helper.order')->getPaymentName() == 'Bank') {
            return $this->redirectToRoute('checkout_order_bank_success');
        } else {
            return array();
        }
    }

    /**
     * remove sessions
     */
    private function removeSessions() {
        // remove quote session
        $this->get('helper.order')->removeQuoteSession();
        // remove order session
        $this->get('helper.order')->removeOrderSession();
        // remove basket items
        $this->get('helper.order')->removeBasketItemsSession();
    }

    /**
     * @Template()
     * @Route("/bank/success", name="checkout_order_bank_success")
     */
    public function bankSuccessAction(Request $request) {

        $order = $this->get('helper.order')->getCurrentOrder();
        // status order
        $this->get('helper.order')->setOrderStatus('complete');
        // additional information
        //json_encode($this->getInstitutPaymentAsArray($paymenttyp->getName("Bank")));
        $this->get('helper.order')->setAdditionalInformation($this->get('helper.order')->getInstitutPaymentAsArray('Bank'));
        // remove sessions
        $this->removeSessions();

        $this->get('helper.order')->sendEmailMessage($order);

        return array(
            'order' => $order
        );
    }

    /**
     * @Template()
     * @Route("/post/success", name="checkout_order_post_success")
     */
    public function postSuccessAction() {
        $order = $this->get('helper.order')->getCurrentOrder();
        $post = $this->get('helper.order')->setPost()->getPost();
        $status = true;
        if ($post->getShasignOut() == $post->getHashCodeOut()) {
            // status order
            $this->get('helper.order')->setOrderStatus('complete');
        } else {
            $status = false;
            $this->get('helper.order')->setOrderStatus('canceled');
        }

        // additional information
        $this->get('helper.order')->setAdditionalInformation($post->getParamsForHashCodeOut());
        $this->removeSessions();
        if ($status) {
            return $this->render('ClosasShopBundle:Checkout:postSuccess.html.twig', array('order' => $order));
        } else {
            return $this->render('ClosasShopBundle:Checkout:postCancel.html.twig', array('order' => $order));
        }
    }

    /**
     * @Template()
     * @Route("/post/cancel", name="checkout_order_post_cancel")
     */
    public function postCancelAction() {
        $order = $this->get('helper.order')->getCurrentOrder();
        $this->get('helper.order')->setOrderStatus('canceled');
        $this->get('helper.order')->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions();
        return array(
            'order' => $order
        );
    }

    /**
     * @Template()
     * @Route("/payment/post/back", name="checkout_order_post_back")
     */
    public function postBackAction() {
        $order = $this->get('helper.order')->getCurrentOrder();
        $this->removeSessions();
        return array();
    }

    /**
     * @Template()
     * @Route("/post/decline", name="checkout_order_post_decline")
     */
    public function postDeclineAction() {
        $order = $this->get('helper.order')->getCurrentOrder();
        $this->get('helper.order')->setOrderStatus('canceled');
        $this->get('helper.order')->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions();
        return array();
    }

    /**
     * @Template()
     * @Route("/post/exception", name="checkout_order_post_exception")
     */
    public function postExceptionAction() {
        $order_id = $this->container->get('session')->get('order_id');
        $this->get('helper.order')->setOrderStatus('canceled');
        $this->get('helper.order')->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        return array();
    }

    /**
     * @Route("/paypal/return", name="checkout_order_paypal_return")
     */
    public function paypalReturnAction(Request $request) {

        if ($order && $request->request->get('token') != '' && $request->request->get('token') != '') {
            $paypal = $this->get('helper.order')->setPaypal('doExpressCheckout')->sendHttpPost();
            if ($paypal->isExpressCheckoutResult()) {
                $this->get('helper.order')->setOrderStatus('complete');
                // set additional information
                $paypal = $this->get('helper.order')->setPaypal('getExpressCheckoutDetails')->sendHttpPost();
                $this->get('helper.order')->setAdditionalInformation($paypal->getExpressCheckoutResult());
                return $this->render('ClosasShopBundle:Checkout:paypalSuccess.html.twig', array('order' => $order));
            } else {
                // set status canceled
                $this->get('helper.order')->setOrderStatus('canceled');
                $this->get('helper.order')->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                return $this->render('ClosasShopBundle:Checkout:paypalFailed.html.twig', array('paypalmessage' => $paypal->getExpressCheckoutResult()['L_LONGMESSAGE0']));
            }
        }
        return $this->render('ClosasShopBundle:Checkout:paypalCancel.html.twig', array('order' => $order));
    }

    /**
     * @Template()
     * @Route("/paypal/cancel", name="checkout_order_paypal_cancel")
     */
    public function paypalCancelAction() {
        $order = $this->get('helper.order')->getCurrentOrder();
        $this->get('helper.order')->setOrderStatus('canceled');
        $this->get('helper.order')->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions();
        return array('order' => $order);
    }

    /**
     * @Route("/home", name="checkout_order_payment_home")
     */
    public function homeAction() {
        return $this->redirect($this->generateUrl('index_page'));
    }

//    /**
//     * @Route("/emaildetail/{id}/", name="email_detail")
//     */
//    public function emailDetailAction($id = null) {
//        $order = $this->get('helper.order')->getOrderDataById($id);
//        return $this->render('email/' . $order['local_code'] . '_' . strtolower($order['payment_name']) . '.html.twig', array('order' => $order));
//    }
}
