<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Service\Payment As ServicePayment;
use App\Service\Order As ServiceOrder;
use App\Service\Cart As ServiceCart;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @Route("/checkout")
 */
class CheckoutController extends Controller {

    /**
     * @Template()
     * @Route("/cart/", name="checkout_cart")
     */
    public function cartAction(Request $request, ServicePayment $servicePayment) {
        $this->setZonePlzCity($request);
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
                'zonecity' => $this->container->get('session')->get('zone-city'),
                'personal' => '',
                'user' => ''
            );
        }

        if ($this->container->get('session')->get('zone-plz') == '' && $this->container->get('session')->get('zone-city') == '') {
            return new RedirectResponse($this->generateUrl('index_cart'));
        }
//        if ($request->request->get('zone-plz') == '' && $request->request->get('zone-city') == '') {
//            return new RedirectResponse($this->generateUrl('index_cart'));
//        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $_personal = '';
        $_user = '';
        if (!is_string($user)) {
            foreach ($user->getPersonals() as $personal) {
                if ($personal->getStandard()) {
                    $_personal = $personal;
                    $_user = $user;
                }
            }
            if ($_personal == '') {
                foreach ($user->getPersonals() as $personal) {
                    $_personal = $personal;
                    $_user = $user;
                    break;
                }
            }
        }
        return array(
            'payments' => $payments,
            'zoneplz' => $this->container->get('session')->get('zone-plz'),
            'zonecity' => $this->container->get('session')->get('zone-city'),
            'personal' => $_personal,
            'user' => $_user
        );
    }

    /**
     *
     * @param Request $request
     */
    private function setZonePlzCity($request) {
        $plz = $request->request->get('zone-plz');
        $city = $request->request->get('zone-city');
        if ($plz && $city) {
            $this->container->get('session')->set('zone-plz', $plz);
            $this->container->get('session')->set('zone-city', $city);
        }
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
        } else if ($serviceOrder->getPaymentName() == 'Bank') {
            return $this->redirectToRoute('checkout_order_bank_success');
        } else if ($serviceOrder->getPaymentName() == 'Cash') {
            return $this->redirectToRoute('checkout_order_cash_success');
        }
    }

    /**
     * remove sessions
     */
    private function removeSessions($serviceOrder, $serviceCart) {
        // remove quote session
        $serviceOrder->removeQuoteSession();
        // remove order session
        $serviceOrder->removeOrderSession();
        // remove subtotal amount
        $serviceCart->removeSubtotalAmount();
        // remove basket items
        $serviceOrder->removeBasketItemsSession();
        // remove paypal session
        $serviceOrder->removePaypalTokenSession();
    }

    /**
     * @Template("shop/checkout/bankSuccess.html.twig")
     * @Route("/bank/success", name="checkout_order_bank_success")
     */
    public function bankSuccessAction(Request $request, ServiceOrder $serviceOrder, ServiceCart $serviceCart, \Swift_Mailer $mailer) {

        $order = $serviceOrder->getCurrentOrder();
        // status order
        $serviceOrder->setOrderStatus('complete');
        // additional information
        $serviceOrder->setAdditionalInformation($serviceOrder->getInstitutPaymentAsArray('Bank'));
        // remove sessions
        $this->removeSessions($serviceOrder, $serviceCart);

        $serviceOrder->sendEmailMessage($order, $mailer);
        return $this->render('shop/checkout/success.html.twig', array('order' => $order));
    }

    /**
     * @Template("shop/checkout/cashSuccess.html.twig")
     * @Route("/cash/success", name="checkout_order_cash_success")
     */
    public function cashSuccessAction(Request $request, ServiceOrder $serviceOrder, ServiceCart $serviceCart, \Swift_Mailer $mailer) {

        $order = $serviceOrder->getCurrentOrder();
        // status order
        $serviceOrder->setOrderStatus('complete');
        // additional information
        $serviceOrder->setAdditionalInformation($serviceOrder->getInstitutPaymentAsArray('Cash'));
        // remove sessions
        $this->removeSessions($serviceOrder, $serviceCart);

        $serviceOrder->sendEmailMessage($order, $mailer);
        return $this->render('shop/checkout/success.html.twig', array('order' => $order));
    }

    /**
     * @Template()
     * @Route("/post/success", name="checkout_order_post_success")
     */
    public function postSuccessAction(ServiceOrder $serviceOrder, ServiceCart $serviceCart, \Swift_Mailer $mailer, LoggerInterface $logger) {
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
        $this->removeSessions($serviceOrder, $serviceCart);
        if ($status) {
            $serviceOrder->sendEmailMessage($order, $mailer);
            return $this->render('shop/checkout/success.html.twig', array('order' => $order));
        } else {
            return $this->render('shop/checkout/postCancel.html.twig', array('order' => $order));
        }
    }

    /**
     * @Template()
     * @Route("/post/cancel", name="checkout_order_post_cancel")
     */
    public function postCancelAction(ServiceOrder $serviceOrder, ServiceCart $serviceCart) {
        $order = $serviceOrder->getCurrentOrder();
        $serviceOrder->setOrderStatus('canceled');
        $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions($serviceOrder, $serviceCart);
        return $this->render('shop/checkout/postCancel.html.twig', array('order' => $order));
    }

    /**
     * @Template()
     * @Route("/post/back", name="checkout_order_post_back")
     */
    public function postBackAction(ServiceOrder $serviceOrder, ServiceCart $serviceCart) {
        $order = $serviceOrder->getCurrentOrder();
        $this->removeSessions($serviceOrder, $serviceCart);
        return $this->render('shop/checkout/postBack.html.twig', array('order' => $order));
    }

    /**
     * @Template()
     * @Route("/post/decline", name="checkout_order_post_decline")
     */
    public function postDeclineAction(ServiceOrder $serviceOrder, ServiceCart $serviceCart) {
        $order = $serviceOrder->getCurrentOrder();
        $serviceOrder->setOrderStatus('canceled');
        $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions($serviceOrder, $serviceCart);
        return $this->render('shop/checkout/postDecline.html.twig', array('order' => $order));
    }

    /**
     * @Template()
     * @Route("/post/exception", name="checkout_order_post_exception")
     */
    public function postExceptionAction(ServiceOrder $serviceOrder, ServiceCart $serviceCart) {
        $order_id = $this->container->get('session')->get('order_id');
        $serviceOrder->setOrderStatus('canceled');
        $serviceOrder->setAdditionalInformation($this->get('translator')->trans('Zahlung wurde abgebrochen'));
        $this->removeSessions($serviceOrder, $serviceCart);
        return $this->render('shop/checkout/postException.html.twig', array('order' => $order));
    }

    /**
     * @Route("/paypal/return/", name="checkout_order_paypal_return")
     */
    public function paypalReturnAction(ServiceOrder $serviceOrder, ServiceCart $serviceCart, Request $request, \Swift_Mailer $mailer) {
        $order = $serviceOrder->getCurrentOrder();
        $serviceOrder->setOrderStatus('complete');
        $arr_data['typ'] = $this->get('translator')->trans('Paypal');
        $arr_data['message'] = $this->get('translator')->trans('Zahlung wurde erfolgreich ausgeführt');
        $arr_data['token'] = $request->query->get('token');
        $arr_data['paymentID'] = $request->query->get('paymentID');
        $serviceOrder->setAdditionalInformation($arr_data);
        $this->removeSessions($serviceOrder, $serviceCart);
        $serviceOrder->sendEmailMessage($order, $mailer);
        return $this->render('shop/checkout/paypalSuccess.html.twig', array('order' => $order));
    }

    /**
     * @Template()
     * @Route("/paypal/cancel/", name="checkout_order_paypal_cancel")
     */
    public function paypalCancelAction(ServiceOrder $serviceOrder, ServiceCart $serviceCart, Request $request) {
        $order = $serviceOrder->getCurrentOrder();
        $serviceOrder->setOrderStatus('canceled');
        $arr_data['typ'] = $this->get('translator')->trans('Paypal');
        $arr_data['message'] = $this->get('translator')->trans('Zahlung wurde abgebrochen');
        $arr_data['token'] = $request->query->get('token');
        $arr_data['paymentID'] = $request->query->get('paymentID');
        $serviceOrder->setAdditionalInformation($arr_data);
        $this->removeSessions($serviceOrder, $serviceCart);
        return $this->render('shop/checkout/paypalCancel.html.twig', array('order' => $order));
    }

    /**
     * @Route("/home", name="checkout_order_payment_home")
     */
    public function homeAction() {
        return $this->redirect($this->generateUrl('index_page'));
    }

    /**
     * @Route("/saveorder", name="checkout_save_order")
     */
    public function saveOrderAction(ServiceOrder $serviceOrder, Request $request) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->redirectToRoute('shop/cart/empty.html.twig');
        }
        //first save order
        try {
            $serviceOrder->save();
        } catch (UniqueConstraintViolationException $ex) {
            return $this->redirectToRoute('shop/checkout/duplicate.html.twig', array(
                        'zoneplz' => $request->request->get('shipping')['post_code'],
                        'zonecity' => $request->request->get('shipping')['city'])
            );
        } catch (Exception $ex) {
            return $this->redirectToRoute('shop/checkout/failed.html.twig');
        }

        return new JsonResponse(array(
            'message' => 'saved',
                )
        );
    }

    /**
     * @Route("/createpaypal/", name="checkout_create_paypal")
     */
    public function createPaypalAction(ServiceOrder $serviceOrder) {
        $paypal = $serviceOrder->setPaypal()->getPaypal();
        $paypal->createAccessToken()->createPayment();
        return new Response();
    }

    /**
     * @Route("/executepaypal/", name="checkout_execute_paypal")
     */
    public function executePaypalAction(ServiceOrder $serviceOrder, Request $request) {
        $paypal = $serviceOrder->setPaypal()->getPaypal();
        $paypal->executePayment();
        return new Response();
    }

}
