<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Payment\Typ;
use App\Entity\Payment\Bank;

class Payment {

    /**
     *
     * @var requestStack
     */
    private $requestStack;

    /**
     *
     * @var trequest
     */
    private $request;

    /**
     *
     * @var common
     */
    private $common;

    /**
     *
     * @var em EntityManager
     */
    private $em;

    /**
     *
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     * @param Common $common
     */
    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Common $common) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->setCurrentRequest();
        $this->common = $common;
    }

    protected function setCurrentRequest() {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    protected function getRequest() {
        return $this->request;
    }

    protected function getCommon() {
        return $this->common;
    }

    protected function getEm() {
        return $this->em;
    }

    public function getPayments() {
        $arr_payments = array();
        $locale = $this->getRequest()->getLocale();
        $payments = $this->getEm()->getRepository(\App\Entity\Payment::class)->findBy(array('status' => 1));
        $language = $this->getEm()->getRepository(\App\Entity\Language::class)->findOneBy(array('short_name' => $locale));
        // payments
        foreach ($payments as $payment) {
            $str_class = '\\App\\Entity\\Payment\\' . $payment->getName();
            $object = new $str_class;
            $paymentTyp = $this->getEm()->getRepository(get_class($object))
                    ->findOneBy(array('payment' => $payment));
            $name = strtolower($payment->getName());
            $label = $this->getEm()->getRepository(\App\Entity\Payment\Lang\Label::class)
                    ->findOneBy(array($name => $paymentTyp, 'lang' => $language));
            if ($payment->getName() == 'Bank') {
                $arr_payments[] = new Typ($payment, $label, $paymentTyp, NULL, NULL, NULL, $this->getCommon());
            } elseif ($payment->getName() == 'Cash') {
                $arr_payments[] = new Typ($payment, $label, NULL, $paymentTyp, NULL, NULL, $this->getCommon());
            } elseif ($payment->getName() == 'Paypal') {
                $arr_payments[] = new Typ($payment, $label, NULL, NULL, $paymentTyp, NULL, $this->getCommon(), false);
            } elseif ($payment->getName() == 'Post') {
                $arr_payments[] = new Typ($payment, $label, NULL, NULL, NULL, $paymentTyp, $this->getCommon(), false);
            }
        }
        return $arr_payments;
    }

    /**
     *
     * @return string
     */
    public function getJsPaypal() {
        $html = "
            <script>
            var CREATE_PAYMENT_URL  = 'https://my-store.com/paypal/create-payment';
            var EXECUTE_PAYMENT_URL = 'https://my-store.com/paypal/execute-payment';

            var _actions;

//            document.querySelector('#confirm-button')
//                    .addEventListener('click', function() {
//                    });


            paypal.Button.render({
                env: 'sandbox', // Or 'production',
                // commit: true, // Show a 'Pay Now' button
                locale: 'de_DE',
                style: {
                  color: 'gold',
                  size: 'small'
                },


                validate: function(actions) {
                    _actions = actions;
                    //alert('validate');
                    actions.disable();
                },

                onClick: function() {
                    _actions.enable();
                },


                payment: function(actions) {
                    //action.disable();
                    //alert('osks');
                  /*
                   * Set up the payment here
                   */
//                    return paypal.request.post(CREATE_PAYMENT_URL).then(function(data) {
//                        return data.id;
//                    });
                },

                onAuthorize: function(data, actions) {
                  /*
                   * Execute the payment here
                   */
                },

                onCancel: function(data, actions) {
                  /*
                   * Buyer cancelled the payment
                   */
                },

                onError: function(err) {
                  /*
                   * An error occurred during the transaction
                   */
                }
              },
              '#paypal-button'
            );
            </script>
        ";
        return $html;
    }

}
