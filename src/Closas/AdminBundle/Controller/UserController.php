<?php

namespace Closas\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Closas\AdminBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/user")
 */
class UserController extends Controller {

    /**
     * @Template()
     * @Route("/list", name="admin_user_list")
     */
    public function listAction() {
        $reposUser = $this->getDoctrine()->getRepository('ClosasAdminBundle:User');
        $users = $reposUser->findAll();

        return array(
            'users' => $users
        );
    }

    /**
     * @Template()
     * @Route("/new/", name="admin_user_new")
     * @Route("/detail/{id}/", name="admin_user_detail")
     */
    public function newAction($id = null, Request $request) {

        if ($id != null) {
            $reposUser = $this->getDoctrine()->getRepository('ClosasAdminBundle:User');
            $user = $reposUser->findOneById($id);
        } else {
            $user = new User();
        }

        $form = $this->createFormBuilder($user)
                ->add('first_name', TextType::class, array(
                    'label' => $this->get('translator')->trans('First name'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('First name'))))
                ->add('last_name', TextType::class, array(
                    'label' => $this->get('translator')->trans('Last name'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Last name'))))
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
                ->add('is_active', CheckboxType::class, array('label' => $this->get('translator')->trans('Is Active'), 'required' => false))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('ClosasAdminBundle:User:new.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

}
