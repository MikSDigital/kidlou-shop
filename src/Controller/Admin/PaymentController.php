<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Payment;
use App\Entity\Caution;
use App\Entity\Shipping;
use App\Service\Image;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/payment")
 */
class PaymentController extends Controller {

    /**
     *
     * @var type payment_entity
     */
    private $payment_entity;

    /**
     * @Template()
     * @Route("/index/", name="admin_payment_index")
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Template()
     * @Route("/list/", name="admin_payment_list")
     */
    public function listAction() {
        $payments = $this->getDoctrine()->getRepository(Payment::class)->findAll();

        return array(
            'payments' => $payments
        );
    }

    /**
     * @Template()
     * @Route("/caution/", name="admin_payment_caution")
     */
    public function cautionAction(Request $request) {
        $caution = $this->getDoctrine()->getRepository(Caution::class)->findAll();
        if ($caution) {
            foreach ($caution as $_caution) {
                $caution = $_caution;
            }
        } else {
            $caution = new Caution();
        }

        $form = $this->createFormBuilder($caution)
                ->add('price', TextType::class, array(
                    'label' => $this->get('translator')->trans('Price'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Price'))))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            return $this->redirectToRoute('admin_payment_index');
        }

        return array('form' => $form->createView());
    }

    /**
     * @Template()
     * @Route("/shipping/", name="admin_payment_shipping_new")
     * @Route("/shipping/{id}/", name="admin_payment_shipping")
     */
    public function shippingAction($id = null, Request $request) {
        if ($id != null) {
            $shipping = $this->getDoctrine()->getRepository(Shipping::class)->findOneById($id);
        } else {
            $shipping = new Shipping();
        }

        $form = $this->createFormBuilder($shipping)
                ->add('price_limit', TextType::class, array(
                    'label' => $this->get('translator')->trans('Pricelimit'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Pricelimit'))))
                ->add('price', TextType::class, array(
                    'label' => $this->get('translator')->trans('Price'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Price'))))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            return $this->redirectToRoute('admin_payment_shipping_list');
        }

        return array('form' => $form->createView());
    }

    /**
     * @Template()
     * @Route("/shippinglist/", name="admin_payment_shipping_list")
     */
    public function shippinglistAction() {
        $shippings = $this->getDoctrine()->getRepository(Shipping::class)->findBy(array(), array('price_limit' => 'ASC'));

        return array(
            'shippings' => $shippings
        );
    }

    /**
     * @Template()
     * @Route("/detail/{id}/", name="admin_payment_detail")
     * @Route("/detail/{id}/{lang}/", defaults={"lang" = "fr"}, name="admin_payment_detail_lang")
     */
    public function detailAction($id = null, $lang = 'fr', Request $request, ServiceImage $serviceImage) {
        if ($id != null) {
            $reposPayment = $this->getDoctrine()->getRepository(Payment::class);
            $payment = $reposPayment->findOneById($id);
            $this->setPasswordFromEntity($payment);
        } else {
            $payment = new Payment();
        }
        // get all forms
        $formStatus = $this->getPaymentForm($payment);
        $formTyp = $this->getPaymentTypForm($payment);
        $formLabel = $this->getPaymentLabelForm($payment, $request, $id, $lang);


        $formtyp = $request->request->get('form')['formtyp'];
        if ($formtyp == 'paymentstatus') {
            $formStatus->handleRequest($request);
        } else if ($formtyp == 'paymenttyp') {
            $formTyp->handleRequest($request);
        } else if ($formtyp == 'paymentlabel') {
            $formLabel->handleRequest($request);
        }

        $em = $this->getDoctrine()->getManager();
        if ($formStatus->isSubmitted() && $formStatus->isValid()) {
            $payment = $formStatus->getData();
            $em->persist($payment);
            $em->flush();
            return $this->redirectToRoute('admin_payment_detail_lang', array('id' => $id, 'lang' => $lang));
        }

        if ($formTyp->isSubmitted() && $formTyp->isValid()) {
            // check if is empty password
            $paymentTyp = $formTyp->getData();
            if ($payment->getName() == 'Post') {
                if ($paymentTyp->getSecretKey() == '') {
                    $paymentTyp->setSecretKey($this->getPasswordFromEntity()->getSecretKey());
                }
            } else if ($payment->getName() == 'Paypal') {
                if ($paymentTyp->getApiUsername() == '') {
                    $paymentTyp->setApiUsername($this->getPasswordFromEntity()->getApiUsername());
                }
                if ($paymentTyp->getApiPassword() == '') {
                    $paymentTyp->setApiPassword($this->getPasswordFromEntity()->getApiPassword());
                }
                if ($paymentTyp->getApiSignature() == '') {
                    $paymentTyp->setApiSignature($this->getPasswordFromEntity()->getApiSignature());
                }
            }
            $em->persist($paymentTyp);
            $em->flush();
            return $this->redirectToRoute('admin_payment_detail_lang', array('id' => $id, 'lang' => $lang));
        }

        if ($formLabel->isSubmitted() && $formLabel->isValid()) {
            $paymentLabel = $formLabel->getData();
            $this->deleteFile($request);
            $this->saveFile($request, $paymentLabel, $payment, $serviceImage);
            $em->persist($paymentLabel);
            $em->flush();
            return $this->redirectToRoute('admin_payment_detail_lang', array('id' => $id, 'lang' => $lang));
        }
        return $this->render('admin/payment/detail.html.twig', array(
                    'formStatus' => $formStatus->createView(),
                    'formTyp' => $formTyp->createView(),
                    'formLabel' => $formLabel->createView()
        ));
    }

    /**
     *
     * @param type $payment
     * @return typ form
     */
    private function getPaymentForm($payment) {
        return $this->createFormBuilder($payment)
                        ->add('status', ChoiceType::class, array(
                            'choices' => array('Disabled' => FALSE, 'Enabled' => TRUE)))
                        ->add('name', TextType::class, array(
                            'label' => $this->get('translator')->trans('Name'),
                            'attr' => array('placeholder' => $this->get('translator')->trans('Name'), 'readonly' => 'readonly')))
                        ->add('short_name', TextType::class, array(
                            'label' => $this->get('translator')->trans('Short name'),
                            'attr' => array('placeholder' => $this->get('translator')->trans('Short name'), 'readonly' => 'readonly')))
                        ->add('formtyp', HiddenType::class, array(
                            'mapped' => false,
                            'attr' => array('value' => 'paymentstatus')))
                        ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                        ->getForm();
    }

    /**
     *
     * @param type $payment
     * @return type $form
     */
    private function getPaymentTypForm($payment) {
        $paymentTyp = $this->getPaymentTyp($payment);
        $funcName = 'getForm' . $payment->getName();
        return $this->$funcName($paymentTyp);
    }

    /**
     *
     * @param type $payment
     * @return type $form
     */
    private function getPaymentTyp($payment) {
        return $this->getDoctrine()->getRepository('Payment\\' . $payment->getName() . '::class')
                        ->findOneBy(array('payment' => $payment));
    }

    /**
     *
     * @param type $payment
     * @return string name
     */
    private function getPaymentName($payment) {
        return strtolower($payment->getName());
    }

    /**
     *
     * @param type $request
     * @param type $paymentLabel
     * @param type $payment
     * @return boolean
     */
    private function saveFile($request, $paymentLabel, $payment, $serviceImage) {
        $file = $request->files->get('form')['image'];
        if (isset($file)) {
            $em = $this->getDoctrine()->getManager();
            $image = $serviceImage->setRequestByParams('form', 'image');
            $image->getImageByEntity('Payment\Lang\Label', $paymentLabel->getId())->removeImageByEntity()->save(1);
            foreach ($this->getPaymentLabels($payment) as $label) {
                $label->setImage($image->getImage());
                $em->persist($paymentLabel);
                $em->flush();
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Ist für das Label von Payment, sprich Text Bilder
     * @param type $payment
     * @param type $paymentTyp
     * @param type $arr_form
     * @param type $id
     * @param type $language
     * @return type form
     */
    private function getPaymentLabelForm($payment, $request, $id, $lang) {

        $label = $this->getPaymentLabel($payment, $request, $lang);
        return $this->createFormBuilder($label)
                        ->add('lang_short_name', ChoiceType::class, array(
                            'choices' => $this->getAllLanguages(),
                            'label' => $this->get('translator')->trans('Language'),
                            'attr' => array('class' => 'label-language', 'data-url' => $this->generateUrl('admin_payment_detail', array('id' => $id))),
                        ))
                        ->add('title', TextType::class, array(
                            'label' => $this->get('translator')->trans('Title'),
                            'attr' => array('placeholder' => $this->get('translator')->trans('Title')),
                            'required' => false,
                        ))
                        ->add('short_text', TextType::class, array(
                            'label' => $this->get('translator')->trans('Short Text'),
                            'attr' => array('placeholder' => $this->get('translator')->trans('Short Text')),
                            'required' => false,
                        ))
                        ->add('website_link', TextType::class, array(
                            'label' => $this->get('translator')->trans('Website'),
                            'attr' => array('placeholder' => $this->get('translator')->trans('Website')),
                            'required' => FALSE,
                        ))
                        ->add('image', FileType::class, array(
                            'attr' => array('class' => 'image', 'name' => 'image'),
                            'required' => FALSE,
                            'data_class' => NULL,
                            'mapped' => false,
                        ))
                        ->add('imagename', HiddenType::class, array(
                            'data' => $this->getImage($payment) == NULL ? NULL : $this->getImage($payment)->getName(),
                            'required' => FALSE,
                            'mapped' => false,
                        ))
                        ->add('formtyp', HiddenType::class, array(
                            'mapped' => false,
                            'required' => FALSE,
                            'data' => 'paymentlabel'))
                        ->add('id', HiddenType::class, array(
                            'mapped' => false,
                            'required' => FALSE,
                            'data' => $label->getId()))
                        ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                        ->getForm();
    }

    /**
     *
     * @return type collection
     */
    private function getAllLanguages() {
        $languages = $this->getDoctrine()->getRepository(Language::class)->findAll();
        foreach ($languages as $key => $lang) {
            $arr_lang[$lang->getName()] = $lang->getShortName();
        }
        return $arr_lang;
    }

    /**
     *
     * @param type $payment
     * @param type $request
     * @param type $lang
     * @return type label
     */
    private function getPaymentLabel($payment, $request, $lang) {
        $arr_form = $request->request->get('form');
        $lang = $this->getDoctrine()->getRepository(Language::class)->findOneBy(array('short_name' => $lang));
        if (isset($arr_form['lang_short_name'])) {
            $lang = $this->getDoctrine()->getRepository(Language::class)->findOneBy(array('short_name' => $arr_form['lang_short_name']));
        }
        return $this->getDoctrine()->getRepository(Payment\Lang\Label::class)
                        ->findOneBy(array($this->getPaymentName($payment) => $this->getPaymentTyp($payment), 'lang' => $lang));
    }

    /**
     *
     * @param type $payment
     * @return type collection
     */
    private function getPaymentLabels($payment) {
        return $this->getDoctrine()->getRepository(Payment\Lang\Label::class)
                        ->findBy(array($this->getPaymentName($payment) => $this->getPaymentTyp($payment)));
    }

    /**
     *
     * @param type $payment
     * @return string $filename
     */
    private function getImage($payment) {
        $label = $this->getDoctrine()->getRepository(Payment\Lang\Label::class)
                ->findOneBy(array($this->getPaymentName($payment) => $this->getPaymentTyp($payment)));
        return $label->getImage();
    }

    /**
     * Ist für die Payment Bank Form
     * @param type $paymentTyp
     * @return type form
     */
    private function getFormBank($paymentTyp) {
        $form = $this->createFormBuilder($paymentTyp)
                ->add('formular_url', TextType::class, array(
                    'label' => $this->get('translator')->trans('Url for payment'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Url for payment'))))
                ->add('account_holder', TextType::class, array(
                    'label' => $this->get('translator')->trans('Account holder'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Account holder'))))
                ->add('account_number', TextType::class, array(
                    'label' => $this->get('translator')->trans('Account number'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Account number'))))
                ->add('sort_code', TextType::class, array(
                    'label' => $this->get('translator')->trans('Sort code'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Sort code')),
                    'required' => false))
                ->add('bank_name', TextType::class, array(
                    'label' => $this->get('translator')->trans('Name'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Bankname'))))
                ->add('iban', TextType::class, array(
                    'label' => $this->get('translator')->trans('IBAN'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('IBAN'))))
                ->add('bic', TextType::class, array(
                    'label' => $this->get('translator')->trans('BIC'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('BIC'))))
                ->add('status', HiddenType::class, array(
                    'data' => TRUE))
                ->add('formtyp', HiddenType::class, array(
                    'mapped' => false,
                    'attr' => array('value' => 'paymenttyp')))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();
        return $form;
    }

    /**
     * Anzeige Cash Form
     * @param type $paymentTyp
     * @return type form
     */
    private function getFormCash($paymentTyp) {
        $form = $this->createFormBuilder($paymentTyp)
                ->add('formular_url', TextType::class, array(
                    'label' => $this->get('translator')->trans('Url for payment'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Url for payment')),
                    'required' => FALSE))
                ->add('price', TextType::class, array(
                    'label' => $this->get('translator')->trans('Price'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Price'))))
                ->add('status', HiddenType::class, array(
                    'data' => TRUE))
                ->add('formtyp', HiddenType::class, array(
                    'mapped' => false,
                    'attr' => array('value' => 'paymenttyp')))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();
        return $form;
    }

    /**
     * Anzeige Paypal Form
     * @param type $paymentTyp
     * @return type
     */
    private function getFormPaypal($paymentTyp) {
        $form = $this->createFormBuilder($paymentTyp)
                ->add('formular_url', TextType::class, array(
                    'label' => $this->get('translator')->trans('Url for payment'),
                    'required' => FALSE,
                    'attr' => array('placeholder' => $this->get('translator')->trans('Url for payment'))))
                ->add('email_account', TextType::class, array(
                    'label' => $this->get('translator')->trans('Email Account'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Email Account'))))
                ->add('api_username', PasswordType::class, array(
                    'label' => $this->get('translator')->trans('Api Username'),
                    'required' => FALSE,
                    'attr' => array('placeholder' => $this->get('translator')->trans('Api Username'), 'value' => $paymentTyp->getApiUsername())))
                ->add('api_password', PasswordType::class, array(
                    'label' => $this->get('translator')->trans('Api Password'),
                    'required' => FALSE,
                    'attr' => array('placeholder' => $this->get('translator')->trans('Api Password'), 'value' => $paymentTyp->getApiPassword())))
                ->add('api_signature', PasswordType::class, array(
                    'label' => $this->get('translator')->trans('Api Signature'),
                    'required' => FALSE,
                    'attr' => array('placeholder' => $this->get('translator')->trans('Api Signature'), 'value' => $paymentTyp->getApiSignature())))
                ->add('authentication_methods', HiddenType::class, array(
                    'data' => 0))
                ->add('sandbox_mode', ChoiceType::class, array(
                    'label' => $this->get('translator')->trans('Sandbox Modus'),
                    'choices' => array('Ja' => TRUE, 'Nein' => FALSE)))
                ->add('api_use_proxy', ChoiceType::class, array(
                    'label' => $this->get('translator')->trans('API verwendet Proxy'),
                    'choices' => array('Ja' => TRUE, 'Nein' => FALSE)))
                ->add('status', HiddenType::class, array(
                    'data' => TRUE))
                ->add('formtyp', HiddenType::class, array(
                    'mapped' => false,
                    'attr' => array('value' => 'paymenttyp')))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();
        return $form;
    }

    /**
     * Ist für Anzeige der Post Form
     * @param type $paymentTyp
     * @return type Form
     */
    private function getFormPost($paymentTyp) {
        $form = $this->createFormBuilder($paymentTyp)
                ->add('formular_url', TextType::class, array(
                    'label' => $this->get('translator')->trans('Url for payment'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Url for payment'))))
                ->add('pspid', TextType::class, array(
                    'label' => $this->get('translator')->trans('PSPID'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('PSPID'))))
                ->add('secret_key', PasswordType::class, array(
                    'label' => $this->get('translator')->trans('Verschlüsselung Signatur'),
                    'required' => FALSE,
                    'attr' => array('placeholder' => $this->get('translator')->trans('Verschlüsselung Signatur'))))
                ->add('title', TextType::class, array(
                    'label' => $this->get('translator')->trans('Title'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Title'))))
                ->add('bg_color', TextType::class, array(
                    'label' => $this->get('translator')->trans('BG Color'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('BG Color'))))
                ->add('txt_color', TextType::class, array(
                    'label' => $this->get('translator')->trans('Text Color'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Text Color'))))
                ->add('table_bg_color', TextType::class, array(
                    'label' => $this->get('translator')->trans('Table BG Color'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Table BG Color'))))
                ->add('table_txt_color', TextType::class, array(
                    'label' => $this->get('translator')->trans('Table Text Color'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Table Text Color'))))
                ->add('button_bg_color', TextType::class, array(
                    'label' => $this->get('translator')->trans('Button BG Color'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Button BG Color'))))
                ->add('button_txt_color', TextType::class, array(
                    'label' => $this->get('translator')->trans('Button Text Color'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Button Text Color'))))
                ->add('font_type', TextType::class, array(
                    'label' => $this->get('translator')->trans('Fonttype'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Fonttype'))))
                ->add('email_fields', TextType::class, array(
                    'label' => $this->get('translator')->trans('Email Fields'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Email Fields'))))
                ->add('is_iframe', ChoiceType::class, array(
                    'label' => $this->get('translator')->trans('E-Payment in Shop einbinden'),
                    'choices' => array('Ja' => TRUE, 'Nein' => FALSE)))
                ->add('iframe_name', TextType::class, array(
                    'label' => $this->get('translator')->trans('Iframe name'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Iframe name')),
                    'required' => false))
                ->add('iframe_width', TextType::class, array(
                    'label' => $this->get('translator')->trans('Iframe breite'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Iframe breite')),
                    'required' => false))
                ->add('iframe_height', TextType::class, array(
                    'label' => $this->get('translator')->trans('Iframe höhe'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Iframe höhe')),
                    'required' => false))
                ->add('status', HiddenType::class, array(
                    'data' => TRUE))
                ->add('formtyp', HiddenType::class, array(
                    'mapped' => false,
                    'attr' => array('value' => 'paymenttyp')))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();
        return $form;
    }

    /**
     *
     * @param type $request
     * @return boolean
     */
    private function deleteFile($request) {

        $names = $request->request->get('delete_form');
        if (isset($names)) {
            $em = $this->getDoctrine()->getManager();
            foreach ($names as $name) {
                $image = $this->getDoctrine()->getRepository(Image::class)->findOneBy(array('name' => $name));
// remove File from filesystem
                foreach ($image->getSizes() as $size) {
                    $filename = $this->get("kernel")->getRootDir() . '/../public/' . $size->getPath() . $image->getName();
                    if (is_file($filename)) {
                        unlink($filename);
                    }
                }

                $labels = $this->getDoctrine()->getRepository(Payment\Lang\Label::class)
                        ->findBy(array('image' => $image));

                foreach ($labels as $label) {
                    $label->setImage(NULL);
                    $em->persist($label);
                    $em->flush();
                }
            }
            $em->remove($image);
            $em->flush();

            return TRUE;
        }
        return FALSE;
    }

    /**
     *
     * @param type $payment
     */
    private function setPasswordFromEntity($payment) {
        $paymentTyp = $this->getDoctrine()->getRepository('Payment\\' . $payment->getName() . '::class')->findOneBy(array('payment' => $payment));
        $name = '\App\Entity\Payment\\' . $payment->getName();
        $this->payment_entity = new $name();
        if ($payment->getName() == 'Post') {
            $this->payment_entity->setSecretKey($paymentTyp->getSecretKey());
        } else if ($payment->getName() == 'Paypal

        ') {
            $this->payment_entity->setApiUsername($paymentTyp->getApiUsername());
            $this->payment_entity->setApiPassword($paymentTyp->getApiPassword());
            $this->payment_entity->setApiSignature($paymentTyp->getApiSignature());
        }
    }

    /**
     *
     * @return type $payment_entity
     */
    private function getPasswordFromEntity() {
        return $this->payment_entity;
    }

}
