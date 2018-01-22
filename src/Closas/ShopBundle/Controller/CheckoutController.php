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
use Closas\ShopBundle\Helper\Order As HelperOrder;

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
            return $this->render('ClosasShopBundle/Cart/empty.html.twig');
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
    public function orderAction(Request $request, HelperOrder $helperOrder) {
        //first save order
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle/Cart:empty.html.twig');
        }
        //first save order
        try {
            $isHelperOrder = $helperOrder->save();
        } catch (UniqueConstraintViolationException $ex) {
            return $this->render('ClosasShopBundle/Checkout/duplicate.html.twig', array(
                        'zoneplz' => $request->request->get('shipping')['post_code'],
                        'zonecity' => $request->request->get('shipping')['city'])
            );
        } catch (Exception $ex) {
            return $this->render('ClosasShopBundle/Checkout/failed.html.twig');
        }

        // redirect to payment institut
        $url = '';
        if ($isHelperOrder == FALSE) {
            return $this->render('ClosasShopBundle/Checkout/failed.html.twig');
        }
        $order = $helperOrder->getCurrentOrder();
        // Daten verarbeitung
        // in get url wird in die demenstprechende url redirectet
        // check which paymenttyp
        if ($helperOrder->getPaymentName() == 'Post') {
            $post = $helperOrder->setPost()->getPost();
            return $this->render('ClosasShopBundle/Checkout/postForm.html.twig', array(
                        'fields' => $post->getFormFieldsValues(),
                        'url' => $post->getPaymentTyp()->getFormularUrl()));
        } else if ($helperOrder->getPaymentName() == 'Paypal') {
            if ($order && $request->request->get('token') != '' && $request->request->get('token') != '') {
                $paypal = $helperOrder->setPaypal('doExpressCheckout')->sendHttpPost();
                if ($paypal->isExpressCheckoutResult()) {
                    $helperOrder->setOrderStatus('complete');
                    // set additional information
                    $paypal = $helperOrder->setPaypal('getExpressCheckoutDetails')->sendHttpPost();
                    $helperOrder->setAdditionalInformation($paypal->getExpressCheckoutResult());
                    $this->removeSessions();
                    return $this->render('ClosasShopBundle/Checkout/paypalSuccess.html.twig', array('order' => $order));
                } else {
                    // set status canceled
                    $helperOrder->setOrderStatus('canceled');
                    $helperOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                    $this->removeSessions();
                    return $this->render('ClosasShopBundle/Checkout/paypalFailed.html.twig', array('paypalmessage' => $paypal->getExpressCheckoutResult()['L_LONGMESSAGE0']));
                }
            } else {
                $paypal = $helperOrder->setPaypal('setExpressCheckout')->getPaypal()->sendHttpPost();
                if ($paypal->isExpressCheckoutResult()) {
                    return $this->redirect($paypal->getExpressCheckoutTokenurl());
                } else {
                    // set status canceled
                    $helperOrder->setOrderStatus('canceled');
                    $helperOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                    $this->removeSessions($helperOrder);
                    return $this->render('ClosasShopBundle/Checkout/paypalFailed.html.twig', array('paypalmessage' => urldecode($paypal->getExpressCheckoutResult()['L_LONGMESSAGE0'])));
                }
            }
        } else if ($helperOrder->getPaymentName() == 'Bank') {
            return $this->redirectToRoute('checkout_order_bank_success');
        } else {
            return array();
        }
    }

    /**
     * remove sessions
     */
    private function removeSessions($helperOrder) {
        // remove quote session
        $helperOrder->removeQuoteSession();
        // remove order session
        $helperOrder->removeOrderSession();
        // remove basket items
        $helperOrder->removeBasketItemsSession();
    }

    /**
     * @Template()
     * @Route("/bank/success", name="checkout_order_bank_success")
     */
    public function bankSuccessAction(Request $request, HelperOrder $helperOrder) {

        $order = $helperOrder->getCurrentOrder();
        // status order
        $helperOrder->setOrderStatus('complete');
        // additional information
        $helperOrder->setAdditionalInformation($helperOrder->getInstitutPaymentAsArray('Bank'));
        // remove sessions
        $this->removeSessions();

        $helperOrder->sendEmailMessage($order);

        return array(
            'order' => $order
        );
    }

    /**
     * @Template()
     * @Route("/post/success", name="checkout_order_post_success")
     */
    public function postSuccessAction(HelperOrder $helperOrder) {
        $order = $helperOrder->getCurrentOrder();
        $post = $helperOrder->setPost()->getPost();
        $status = true;
        if ($post->getShasignOut() == $post->getHashCodeOut()) {
            // status order
            $helperOrder->setOrderStatus('complete');
        } else {
            $status = false;
            $helperOrder->setOrderStatus('canceled');
        }

        // additional information
        $helperOrder->setAdditionalInformation($post->getParamsForHashCodeOut());
        $this->removeSessions();
        if ($status) {
            return $this->render('ClosasShopBundle/Checkout/postSuccess.html.twig', array('order' => $order));
        } else {
            return $this->render('ClosasShopBundle/Checkout/postCancel.html.twig', array('order' => $order));
        }
    }

    /**
     * @Template()
     * @Route("/post/cancel", name="checkout_order_post_cancel")
     */
    public function postCancelAction(HelperOrder $helperOrder) {
        $order = $helperOrder->getCurrentOrder();
        $helperOrder->setOrderStatus('canceled');
        $helperOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions();
        return array(
            'order' => $order
        );
    }

    /**
     * @Template()
     * @Route("/payment/post/back", name="checkout_order_post_back")
     */
    public function postBackAction(HelperOrder $helperOrder) {
        $order = $helperOrder->getCurrentOrder();
        $this->removeSessions();
        return array();
    }

    /**
     * @Template()
     * @Route("/post/decline", name="checkout_order_post_decline")
     */
    public function postDeclineAction(HelperOrder $helperOrder) {
        $order = $helperOrder->getCurrentOrder();
        $helperOrder->setOrderStatus('canceled');
        $helperOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions();
        return array();
    }

    /**
     * @Template()
     * @Route("/post/exception", name="checkout_order_post_exception")
     */
    public function postExceptionAction(HelperOrder $helperOrder) {
        $order_id = $this->container->get('session')->get('order_id');
        $helperOrder->setOrderStatus('canceled');
        $helperOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        return array();
    }

    /**
     * @Route("/paypal/return", name="checkout_order_paypal_return")
     */
    public function paypalReturnAction(Request $request, HelperOrder $helperOrder) {

        if ($order && $request->request->get('token') != '' && $request->request->get('token') != '') {
            $paypal = $helperOrder->setPaypal('doExpressCheckout')->sendHttpPost();
            if ($paypal->isExpressCheckoutResult()) {
                $helperOrder->setOrderStatus('complete');
                // set additional information
                $paypal = $helperOrder->setPaypal('getExpressCheckoutDetails')->sendHttpPost();
                $helperOrder->setAdditionalInformation($paypal->getExpressCheckoutResult());
                return $this->render('ClosasShopBundle/Checkout/paypalSuccess.html.twig', array('order' => $order));
            } else {
                // set status canceled
                $helperOrder->setOrderStatus('canceled');
                $helperOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                return $this->render('ClosasShopBundle/Checkout/paypalFailed.html.twig', array('paypalmessage' => $paypal->getExpressCheckoutResult()['L_LONGMESSAGE0']));
            }
        }
        return $this->render('ClosasShopBundle/Checkout/paypalCancel.html.twig', array('order' => $order));
    }

    /**
     * @Template()
     * @Route("/paypal/cancel", name="checkout_order_paypal_cancel")
     */
    public function paypalCancelAction(HelperOrder $helperOrder) {
        $order = $helperOrder->getCurrentOrder();
        $helperOrder->setOrderStatus('canceled');
        $helperOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions();
        return array('order' => $order);
    }

    /**
     * @Route("/home", name="checkout_order_payment_home")
     */
    public function homeAction() {
        return $this->redirect($this->generateUrl('index_page'));
    }

}
