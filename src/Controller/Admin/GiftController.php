<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Gift;
use App\Entity\Gift\Text;
use App\Entity\Gift\Coupon;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/gift")
 */
class GiftController extends Controller {

    /**
     * @Template()
     * @Route("/list", name="admin_gift_list")
     */
    public function listAction() {
        $gifts = $this->getDoctrine()->getRepository(Gift::class)->findAll();
        return array(
            'gifts' => $gifts
        );
    }

    /**
     * @Template()
     * @Route("/new/", name="admin_gift_new")
     * @Route("/detail/{id}/", name="admin_gift_detail")
     */
    public function newAction($id = null, Request $request) {
        if ($id != null) {
            $gift = $this->getDoctrine()->getRepository(Gift::class)->findOneById($id);
        } else {
            $gift = new Gift();
        }

        $arr_lang = array();
        $langs = $this->getDoctrine()->getRepository(Language::class)->findAll();
        foreach ($langs as $lang) {
            if ($this->getDoctrine()->getRepository(Gift\Text::class)->findOneBy(array('gift' => $gift, 'lang' => $this->getDoctrine()->getRepository(Language::class)->findOneBy(array('short_name' => $lang->getShortName()))))) {
                $arr_lang[$lang->getShortName()] = $this->getDoctrine()->getRepository(Gift\Text::class)->findOneBy(array('gift' => $gift, 'lang' => $this->getDoctrine()->getRepository(Language::class)->findOneBy(array('short_name' => $lang->getShortName()))))->getDescription();
            } else {
                $arr_lang[$lang->getShortName()] = '';
            }
        }

        $form = $this->createFormBuilder($gift)
                ->add('name', TextType::class, array(
                    'label' => $this->get('translator')->trans('Name'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Name'))))
                ->add('text_fr', TextType::class, array(
                    'mapped' => false,
                    'data' => $arr_lang['fr'],
                    'label' => $this->get('translator')->trans('Text Français'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Text Français'))))
                ->add('text_de', TextType::class, array(
                    'mapped' => false,
                    'data' => $arr_lang['de'],
                    'label' => $this->get('translator')->trans('Text Deutsch'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Text Deutsch'))))
                ->add('text_en', TextType::class, array(
                    'mapped' => false,
                    'data' => $arr_lang['en'],
                    'label' => $this->get('translator')->trans('Text English'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Text English'))))
                ->add('text_es', TextType::class, array(
                    'mapped' => false,
                    'data' => $arr_lang['es'],
                    'label' => $this->get('translator')->trans('Text Spanish'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Text Spanish'))))
                ->add('percent', TextType::class, array(
                    'label' => $this->get('translator')->trans('Percent'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Percent'))))
                ->add('length_code', TextType::class, array(
                    'data' => 10,
                    'label' => $this->get('translator')->trans('Number length'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Number length'))))
                ->add('number_codes', TextType::class, array(
                    'label' => $this->get('translator')->trans('Number Codes'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Number Codes'))))
                ->add('date_from', DateType::class, array(
                    'label' => $this->get('translator')->trans('Date from'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Date from'))))
                ->add('date_to', DateType::class, array(
                    'label' => $this->get('translator')->trans('Date to'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Date to'))))
                ->add('max_uses', TextType::class, array(
                    'label' => $this->get('translator')->trans('Max uses'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Max uses'))))
                ->add('is_active', CheckboxType::class, array('label' => $this->get('translator')->trans('Is Active'), 'required' => false))
                ->add('codes', EntityType::class, array(
                    'label' => 'Codes',
                    'mapped' => false,
                    'required' => false,
                    'class' => 'App\Entity\Gift\Coupon',
                    'choice_label' => function ($coupon) {
                        return sprintf('%s (%s)', $coupon->getCode(), $coupon->getCounter());
                    },
                    //'choice_label' => 'code',
                    'choice_value' => 'id',
                    'expanded' => true,
                    'multiple' => true,
                    'placeholder' => 'Codes',
                    'constraints' => array(
                        new NotBlank(array("message" => 'Codes')),
                    ),
                ))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gift = $form->getData();
            $gift->setCreatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($gift);
            $em->flush();
            $this->setTexts($gift, $request);
            $this->setCodes($gift);
            return $this->redirectToRoute('admin_gift_detail', array('id' => $gift->getId()));
        }

        return $this->render('admin/gift/new.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     *
     * @param type $gift
     * @param type $request
     */
    private function setTexts($gift, $request) {
        $em = $this->getDoctrine()->getManager();
        $texts = $request->request->get('form');
        $langs = $this->getDoctrine()->getRepository(Language::class)->findAll();
        foreach ($langs as $lang) {
            $text = $this->getDoctrine()->getRepository(Gift\Text::class)->findOneBy(array('lang' => $lang, 'gift' => $gift));
            if (!$text) {
                $text = new Text();
            }
            $text->setLang($lang);
            $text->setGift($gift);
            $text->setDescription($texts['text_' . $lang->getShortName()]);
            $em->persist($text);
            $em->flush();
        }
    }

    /**
     *
     * @param type $gift
     */
    private function setCodes($gift) {
        $em = $this->getDoctrine()->getManager();
        $coupons = $this->getDoctrine()->getRepository(Gift\Coupon::class)->findBy(array('gift' => $gift));
        $count_codes = $gift->getNumberCodes() - count($coupons);
        for ($i = 0; $i < $count_codes; $i++) {
            $isCode = FALSE;
            while (!$isCode) {
                $code = $this->createCodes($gift->getLengthCode());
                $coupon = $this->getDoctrine()->getRepository(Gift\Coupon::class)->findOneBy(array('code' => $code));
                if (!$coupon) {
                    $isCode = TRUE;
                }
            }
            $coupon = new Coupon();
            $coupon->setCode($code);
            $coupon->setCounter(0);
            $coupon->setGift($gift);
            $coupon->setOrder(NULL);
            $coupon->setQuote(NULL);
            $em->persist($coupon);
            $em->flush();
        }
    }

    /**
     *
     * @param type $length
     * @return string
     */
    private function createCodes($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
