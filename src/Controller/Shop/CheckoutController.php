<?php

namespace App\Controller\Shop;

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
use App\Service\Payment As ServicePayment;
use App\Service\Order As ServiceOrder;

/**
 * @Route("/checkout")
 */
class CheckoutController extends Controller {

    /**
     * @Template()
     * @Route("/cart/", name="checkout_cart")
     */
    public function cartAction(Request $request, ServicePayment $servicePayment) {
        $locale = $request->getLocale();
        $payments = $servicePayment->getPayments();
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('shop/cart/empty.html.twig');
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
    public function orderAction(Request $request, ServiceOrder $serviceOrder) {
        //first save order
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('shop/cart/empty.html.twig');
        }
        //first save order
        try {
            $isServiceOrder = $serviceOrder->save();
        } catch (UniqueConstraintViolationException $ex) {
            return $this->render('shop/checkout/duplicate.html.twig', array(
                        'zoneplz' => $request->request->get('shipping')['post_code'],
                        'zonecity' => $request->request->get('shipping')['city'])
            );
        } catch (Exception $ex) {
            return $this->render('shop/checkout/failed.html.twig');
        }

        // redirect to payment institut
        $url = '';
        if ($isServiceOrder == FALSE) {
            return $this->render('shop/checkout/failed.html.twig');
        }
        $order = $serviceOrder->getCurrentOrder();
        // Daten verarbeitung
        // in get url wird in die demenstprechende url redirectet
        // check which paymenttyp
        if ($serviceOrder->getPaymentName() == 'Post') {
            $post = $serviceOrder->setPost()->getPost();
            return $this->render('shop/checkout/postForm.html.twig', array(
                        'fields' => $post->getFormFieldsValues(),
                        'url' => $post->getPaymentTyp()->getFormularUrl()));
        } else if ($serviceOrder->getPaymentName() == 'Paypal') {
            if ($order && $request->request->get('token') != '' && $request->request->get('token') != '') {
                $paypal = $serviceOrder->setPaypal('doExpressCheckout')->sendHttpPost();
                if ($paypal->isExpressCheckoutResult()) {
                    $serviceOrder->setOrderStatus('complete');
                    // set additional information
                    $paypal = $serviceOrder->setPaypal('getExpressCheckoutDetails')->sendHttpPost();
                    $serviceOrder->setAdditionalInformation($paypal->getExpressCheckoutResult());
                    $this->removeSessions($serviceOrder);
                    return $this->render('shop/checkout/paypalSuccess.html.twig', array('order' => $order));
                } else {
                    // set status canceled
                    $serviceOrder->setOrderStatus('canceled');
                    $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                    $this->removeSessions($serviceOrder);
                    return $this->render('shop/checkout/paypalFailed.html.twig', array('paypalmessage' => $paypal->getExpressCheckoutResult()['L_LONGMESSAGE0']));
                }
            } else {
                $paypal = $serviceOrder->setPaypal('setExpressCheckout')->getPaypal()->sendHttpPost();
                if ($paypal->isExpressCheckoutResult()) {
                    return $this->redirect($paypal->getExpressCheckoutTokenurl());
                } else {
                    // set status canceled
                    $serviceOrder->setOrderStatus('canceled');
                    $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                    $this->removeSessions($serviceOrder);
                    return $this->render('shop/checkout/paypalFailed.html.twig', array('paypalmessage' => urldecode($paypal->getExpressCheckoutResult()['L_LONGMESSAGE0'])));
                }
            }
        } else if ($serviceOrder->getPaymentName() == 'Bank') {
            return $this->redirectToRoute('checkout_order_bank_success');
        } else {
            return array();
        }
    }

    /**
     * remove sessions
     */
    private function removeSessions($serviceOrder) {
        // remove quote session
        $serviceOrder->removeQuoteSession();
        // remove order session
        $serviceOrder->removeOrderSession();
        // remove basket items
        $serviceOrder->removeBasketItemsSession();
    }

    /**
     * @Template("shop/checkout/bankSuccess.html.twig")
     * @Route("/bank/success", name="checkout_order_bank_success")
     */
    public function bankSuccessAction(Request $request, ServiceOrder $serviceOrder, \Swift_Mailer $mailer) {

        $order = $serviceOrder->getCurrentOrder();
        // status order
        $serviceOrder->setOrderStatus('complete');
        // additional information
        $serviceOrder->setAdditionalInformation($serviceOrder->getInstitutPaymentAsArray('Bank'));
        // remove sessions
        $this->removeSessions($serviceOrder);

        $serviceOrder->sendEmailMessage($order, $mailer);

        return array(
            'order' => $order
        );
    }

    /**
     * @Template()
     * @Route("/post/success", name="checkout_order_post_success")
     */
    public function postSuccessAction(ServiceOrder $serviceOrder) {
        $order = $serviceOrder->getCurrentOrder();
        $post = $serviceOrder->setPost()->getPost();
        $status = true;
        if ($post->getShasignOut() == $post->getHashCodeOut()) {
            // status order
            $serviceOrder->setOrderStatus('complete');
        } else {
            $status = false;
            $serviceOrder->setOrderStatus('canceled');
        }

        // additional information
        $serviceOrder->setAdditionalInformation($post->getParamsForHashCodeOut());
        $this->removeSessions($serviceOrder);
        if ($status) {
            return $this->render('shop/checkout/postSuccess.html.twig', array('order' => $order));
        } else {
            return $this->render('shop/checkout/postCancel.html.twig', array('order' => $order));
        }
    }

    /**
     * @Template()
     * @Route("/post/cancel", name="checkout_order_post_cancel")
     */
    public function postCancelAction(ServiceOrder $serviceOrder) {
        $order = $serviceOrder->getCurrentOrder();
        $serviceOrder->setOrderStatus('canceled');
        $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions($serviceOrder);
        return array(
            'order' => $order
        );
    }

    /**
     * @Template()
     * @Route("/payment/post/back", name="checkout_order_post_back")
     */
    public function postBackAction(ServiceOrder $serviceOrder) {
        $order = $serviceOrder->getCurrentOrder();
        $this->removeSessions($serviceOrder);
        return array();
    }

    /**
     * @Template()
     * @Route("/post/decline", name="checkout_order_post_decline")
     */
    public function postDeclineAction(ServiceOrder $serviceOrder) {
        $order = $serviceOrder->getCurrentOrder();
        $serviceOrder->setOrderStatus('canceled');
        $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions($serviceOrder);
        return array();
    }

    /**
     * @Template()
     * @Route("/post/exception", name="checkout_order_post_exception")
     */
    public function postExceptionAction(ServiceOrder $serviceOrder) {
        $order_id = $this->container->get('session')->get('order_id');
        $serviceOrder->setOrderStatus('canceled');
        $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        return array();
    }

    /**
     * @Route("/paypal/return", name="checkout_order_paypal_return")
     */
    public function paypalReturnAction(Request $request, ServiceOrder $serviceOrder) {

        if ($order && $request->request->get('token') != '' && $request->request->get('token') != '') {
            $paypal = $serviceOrder->setPaypal('doExpressCheckout')->sendHttpPost();
            if ($paypal->isExpressCheckoutResult()) {
                $serviceOrder->setOrderStatus('complete');
                // set additional information
                $paypal = $serviceOrder->setPaypal('getExpressCheckoutDetails')->sendHttpPost();
                $serviceOrder->setAdditionalInformation($paypal->getExpressCheckoutResult());
                return $this->render('shop/checkout/paypalSuccess.html.twig', array('order' => $order));
            } else {
                // set status canceled
                $serviceOrder->setOrderStatus('canceled');
                $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
                return $this->render('shop/checkout/paypalFailed.html.twig', array('paypalmessage' => $paypal->getExpressCheckoutResult()['L_LONGMESSAGE0']));
            }
        }
        return $this->render('shop/checkout/paypalCancel.html.twig', array('order' => $order));
    }

    /**
     * @Template()
     * @Route("/paypal/cancel", name="checkout_order_paypal_cancel")
     */
    public function paypalCancelAction(ServiceOrder $serviceOrder) {
        $order = $serviceOrder->getCurrentOrder();
        $serviceOrder->setOrderStatus('canceled');
        $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions($serviceOrder);
        return array('order' => $order);
    }

    /**
     * @Route("/home", name="checkout_order_payment_home")
     */
    public function homeAction() {
        return $this->redirect($this->generateUrl('index_page'));
    }

}
