<?php

namespace Closas\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\UserBundle\Entity\User;
use Closas\UserBundle\Entity\User\Personal;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Glifery\EntityHiddenTypeBundle\Form\Type\EntityHiddenType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Closas\ShopBundle\Helper\Payment As HelperPayment;

class UserController extends Controller {

    /**
     * @Template()
     * @Route("/", name="user_index")
     */
    public function indexAction() {

        return array();
    }

    /**
     * @Template()
     * @Route("/login/", name="user_login")
     */
    public function loginAction() {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return array(
            'last_username' => $lastUsername,
            'error' => $error,
        );
    }

    /**
     * @Route("/logout/", name="user_logout")
     */
    public function logoutAction() {

    }

    /**
     * @Template()
     * @Route("/check/", name="user_check")
     */
    public function checkAction(Request $request) {

    }

    /**
     * @Template()
     * @Route("/new/", name="user_new")
     * @Route("/detail/{id}/", name="user_detail")
     */
    public function newAction($id = null, Request $request) {

        if ($id != null) {
            $reposUser = $this->getDoctrine()->getRepository('ClosasUserBundle:User');
            $user = $reposUser->findOneById($id);
        } else {
            $user = new User();
        }

        $form = $this->createFormBuilder($user)
                ->add('username', TextType::class, array(
                    'label' => $this->get('translator')->trans('Username'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Username'))))
                ->add('email', EmailType::class, array(
                    'label' => $this->get('translator')->trans('Email'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Email'))))
                ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => array(
                        'label' => $this->get('translator')->trans('Password'),
                        'attr' => array(
                            'placeholder' => $this->get('translator')->trans('Password'))),
                    'second_options' => array(
                        'label' => $this->get('translator')->trans('Repeat Password'),
                        'attr' => array('placeholder' => $this->get('translator')->trans('Repeat Password')))))
                ->add('is_active', HiddenType::class, array(
                    'data' => TRUE))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($user);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                // handle exception
                $this->setErrorUniqueFlashMessage($e);
                return $this->redirectToRoute('user_new');
            }
            // automatically login and redirect to new personal
            $token = new UsernamePasswordToken($user, null, 'frontendlogin', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_frontendlogin', serialize($token));
            return $this->redirectToRoute('personal');
        }

        return $this->render('ClosasUserBundle:User:new.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @param UniqueConstraintViolationException $e
     */
    private function setErrorUniqueFlashMessage(UniqueConstraintViolationException $e) {
        switch ($e->getErrorCode()) {
            case 1062:
                $this->addFlash('error', $this->get('translator')->trans('Username already exists'));
                break;
            default:
                $this->addFlash('error', $this->get('translator')->trans('Some error occurred during writing to database'));
        }
    }

    /**
     * @Template()
     * @Route("/personales/", name="personales")
     */
    public function personalesAction(Request $request) {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user = $this->getDoctrine()->getRepository('ClosasUserBundle:User')->findOneById($user->getId());
        return array('personals' => $user->getPersonals());
    }

    /**
     * @Template()
     * @Route("/personal/", name="personal")
     * @Route("/personal/{id}/", name="edit_personal")
     */
    public function personalAction($id = null, Request $request) {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($id != null) {
            $personal = $this->getDoctrine()->getRepository('ClosasUserBundle:User\Personal')->findOneById($id);
        } else {
            $personal = new Personal();
        }

        $form = $this->createFormBuilder($personal)
                ->add('first_name', TextType::class, array(
                    'label' => $this->get('translator')->trans('First name'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('First name'))))
                ->add('last_name', TextType::class, array(
                    'label' => $this->get('translator')->trans('Last name'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Last name'))))
                ->add('street', TextType::class, array(
                    'label' => $this->get('translator')->trans('Street'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Street'))))
                ->add('post_code', TextType::class, array(
                    'label' => $this->get('translator')->trans('Postcode'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Postcode'))))
                ->add('city', TextType::class, array(
                    'label' => $this->get('translator')->trans('City'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('City'))))
                ->add('country_code', ChoiceType::class, array(
                    'choices' => array_flip($this->get('helper.common')->getCountries()), 'preferred_choices' => array('CH', 'FR', 'GB', 'DE')))
                ->add('city', TextType::class, array(
                    'label' => $this->get('translator')->trans('City'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('City'))))
                ->add('phone', TextType::class, array(
                    'label' => $this->get('translator')->trans('Phone'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Phone')),
                    'required' => FALSE))
                ->add('mobile', TextType::class, array(
                    'label' => $this->get('translator')->trans('Mobile'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Mobile'))))
                ->add('lang', EntityType::class, array(
                    'class' => 'ClosasShopBundle:Language',
                    'choices' => $this->getDoctrine()->getRepository('ClosasShopBundle:Language')->findAll(),
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'data' => $this->getDoctrine()->getRepository('ClosasShopBundle:Language')->findOneById($this->get('helper.common')->getLanguageId($request->getLocale())),
                ))
                ->add('user', EntityHiddenType::class, array(
                    'class' => 'ClosasUserBundle:User',
                    'property' => 'id', // Mapped property name (default is 'id')
                    'data' => $this->getDoctrine()->getRepository('ClosasUserBundle:User')->findOneById($user->getId()), // Field value by default
                ))
                ->add('standard', ChoiceType::class, array(
                    'choices' => array($this->get('translator')->trans('yes') => TRUE, $this->get('translator')->trans('no') => FALSE),
                    'data' => TRUE,
                    'label' => $this->get('translator')->trans('Default')))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();


        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $personal = $form->getData();
            try {
                $em->persist($personal);
                $em->flush();
                if ($personal->getStandard()) {
                    $_personals = $this->getDoctrine()->getRepository('ClosasUserBundle:User\Personal')->findAll();
                    foreach ($_personals as $_personal) {
                        if ($_personal->getId() != $personal->getId()) {
                            $_personal->setStandard(FALSE);
                            $em->persist($_personal);
                            $em->flush();
                        }
                    }
                }
            } catch (\Exception $e) {
                // handle exception
                $this->addFlash('error', $this->get('translator')->trans('Error code ' . $e->getCode()));
                if ($id != null) {
                    return $this->redirectToRoute('edit_personal', array('id' => $id));
                }
                return $this->redirectToRoute('personal');
            }
            return $this->redirectToRoute('user_index');
        }

        return array('form' => $form->createView());
    }

    /**
     * @Template()
     * @Route("/checkout/cart/", name="user_checkout_cart")
     */
    public function cartAction(Request $request, HelperPayment $helperPayment) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle:Cart:empty.html.twig');
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $_personal = '';
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
        $payments = $helperPayment->getPayments();
        return array(
            'payments' => $payments,
            'personal' => $_personal,
            'user' => $_user
        );
    }

}
