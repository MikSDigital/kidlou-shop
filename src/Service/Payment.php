<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\RouterInterface;
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
     * @var router
     */
    private $router;

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
    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Common $common, $router) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->setCurrentRequest();
        $this->common = $common;
        $this->router = $router;
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
        $create_url = $this->router->generate('test_create_paypal');
        $execute_url = $this->router->generate('test_execute_paypal');
        $html = "
            <script>
            var CREATE_PAYMENT_URL  = '" . $create_url . "';
            var EXECUTE_PAYMENT_URL = '" . $execute_url . "';

            var _actions;
            var _isEmpty = true;
            document.querySelectorAll('.send-order input').forEach(function(el) {
                el.addEventListener('focusout', function() {
                    if(isPaypalButton()){
                        // check if all inputs are empty
                        if(!isInputFieldsEmptyForPaypal(el)){
                            _actions.enable();
                            _isEmpty = false;
                        }
                    }
                });
            });

            // check if checkbox is checked
            document.querySelectorAll('.send-order .save_new_user').forEach(function(el) {
                el.addEventListener('click', function() {
                    if(isPaypalButton()){
                        // check if is checked
                        if(!isCheckboxEmptyForPaypal(_isEmpty)){
                            _actions.enable();
                            _isEmpty = false;
                        } else {
                            _actions.disable();
                            _isEmpty = true;
                        }
                    }
                });
            });


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
                    actions.disable();
                },

                onClick: function() {
                    setErrorFieldsForPaypal();
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
