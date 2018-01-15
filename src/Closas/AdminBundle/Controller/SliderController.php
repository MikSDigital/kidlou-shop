<?php

namespace Closas\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Closas\ShopBundle\Entity\Nivoslider;
use Closas\ShopBundle\Entity\Nivoslider\Item;
use Closas\ShopBundle\Entity\Nivoslider\Configuration;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/slider")
 */
class SliderController extends Controller {

    /**
     * @Template()
     * @Route("/index", name="admin_slider_index")
     * @Route("/index/{typ}/", name="admin_slider_index_typ")
     */
    public function indexAction($typ = '') {
        // configuraion nur ein Eintrag, wenn keiner neu sons immer nur einer
        $nivoslider = $this->getDoctrine()->getRepository('ClosasShopBundle:Nivoslider\Configuration')->findFirst();
        if ($nivoslider) {
            $confid = $nivoslider->getId();
        } else {
            $confid = '';
        }

        return array(
            'typ' => $typ,
            'confid' => $confid
        );
    }

    /**
     * @Template()
     * @Route("/configuration/nivo", name="admin_slider_conf_nivo")
     * @Route("/configuration/nivo/{id}/", name="admin_slider_conf_nivo_id")
     */
    public function confnivoAction($id = null, Request $request) {

        if ($id != null) {
            $reposNivoslider = $this->getDoctrine()->getRepository('ClosasShopBundle:Nivoslider\Configuration');
            $nivoslider = $reposNivoslider->findOneById($id);
        } else {
            $nivoslider = new Configuration();
        }

        $array_animation = array('random' => 'random',
            'sliceDown' => 'sliceDown',
            'sliceDownLeft' => 'sliceDownLeft',
            'sliceUp' => 'sliceUp',
            'sliceUpLeft' => 'sliceUpLeft',
            'sliceUpDown' => 'sliceUpDown',
            'sliceUpDownLeft' => 'sliceUpDownLeft',
            'fold' => 'fold',
            'slideInRight' => 'slideInRight',
            'slideInLeft' => 'slideInLeft',
            'boxRandom' => 'boxRandom',
            'boxRain' => 'boxRain',
            'boxRainReverse' => 'boxRainReverse',
            'boxRainGrow' => 'boxRainGrow',
            'boxRainGrowReverse' => 'boxRainGrowReverse'
        );
        $form = $this->createFormBuilder($nivoslider)
                ->add('status', ChoiceType::class, array(
                    'choices' => array('Enable' => 1, 'Disable' => 0),
                    'label' => $this->get('translator')->trans('Status')))
                ->add('animation', ChoiceType::class, array(
                    'choices' => $array_animation,
                    'label' => $this->get('translator')->trans('Animation Type')))
                ->add('speed', TextType::class, array(
                    'label' => $this->get('translator')->trans('Pause Time'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Pause Time'))))
                ->add('interval', TextType::class, array(
                    'label' => $this->get('translator')->trans('Animation Speed'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Animation Speed'))))
                ->add('qty_item', TextType::class, array(
                    'label' => $this->get('translator')->trans('Qty of items'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Qty of items'))))
                ->add('description', ChoiceType::class, array(
                    'choices' => array('Yes' => 1, 'No' => 0),
                    'label' => $this->get('translator')->trans('Show Caption')))
                ->add('next_back', ChoiceType::class, array(
                    'choices' => array('Yes' => 1, 'No' => 0),
                    'label' => $this->get('translator')->trans('Show Next/Back control')))
                ->add('nav_ctrl', ChoiceType::class, array(
                    'choices' => array('Yes' => 1, 'No' => 0),
                    'label' => $this->get('translator')->trans('Show navigation control')))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nivoslider = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($nivoslider);
            $em->flush();
            return $this->redirectToRoute('admin_slider_index_typ', array('typ' => 'nivo'));
        }

        return $this->render('ClosasAdminBundle:Slider:confnivo.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Template()
     * @Route("/item/nivo", name="admin_slider_item_nivo")
     * @Route("/item/nivo/{id}/", name="admin_slider_item_nivo_id")
     */
    public function itemnivoAction($id = null, Request $request) {

        if ($id != null) {
            $reposNivosliderItem = $this->getDoctrine()->getRepository('ClosasShopBundle:Nivoslider\Item');
            $nivosliderItem = $reposNivosliderItem->findOneById($id);
        } else {
            $nivosliderItem = new Item();
        }

        $langs = $this->getDoctrine()->getRepository('ClosasShopBundle:Language')->findAll();
        $arr_lang = array();
        if ($id != null) {
            $arr_lang[$nivosliderItem->getLang()->getName()] = $nivosliderItem->getLang();
        } else {
            foreach ($langs as $lang) {
                $arr_lang[$lang->getName()] = $lang;
            }
        }
        $form = $this->createFormBuilder($nivosliderItem)
                ->add('lang', ChoiceType::class, array(
                    'choices' => $arr_lang,
                    'label' => $this->get('translator')->trans('Lang')))
                ->add('title', TextType::class, array(
                    'label' => $this->get('translator')->trans('Title'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Title'))))
                ->add('title2', TextType::class, array(
                    'label' => $this->get('translator')->trans('Title2'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Title2')),
                    'required' => false))
                ->add('title3', TextType::class, array(
                    'label' => $this->get('translator')->trans('Title3'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Title3')),
                    'required' => false))
                ->add('link', TextType::class, array(
                    'label' => $this->get('translator')->trans('Link'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Link')),
                    'required' => false))
                ->add('image', FileType::class, array(
                    'label' => $this->get('translator')->trans('Image'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Image')),
                    'data_class' => null))
                ->add('image2', FileType::class, array(
                    'label' => $this->get('translator')->trans('Image2'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Image2')),
                    'required' => false,
                    'data_class' => null))
                ->add('image3', FileType::class, array(
                    'label' => $this->get('translator')->trans('Image3'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Image3')),
                    'required' => false,
                    'data_class' => null))
                ->add('order', TextType::class, array(
                    'label' => $this->get('translator')->trans('Order'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Order')),
                    'data' => '0',
                    'required' => false))
                ->add('description', TextareaType::class, array(
                    'label' => $this->get('translator')->trans('Description'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Description')),
                    'required' => false))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nivosliderItem = $form->getData();
            for ($i = 1; $i <= 3; $i++) {
                $getFunctionName = "getImage";
                $setFunctionName = "setImage";
                if ($i > 1) {
                    $getFunctionName = $getFunctionName . $i;
                    $setFunctionName = $setFunctionName . $i;
                }
                $image = $nivosliderItem->$getFunctionName();
                if ($image) {
                    $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                    $image->move($this->getParameter('upload_directory'), $imageName);
                    $nivosliderItem->$setFunctionName($imageName);
                } else {
                    if ($i > 1) {
                        if (!isset($request->request->get('form')['image' . $i])) {
                            continue;
                        }
                        $image = $request->request->get('form')['image' . $i];
                    } else {
                        $image = $request->request->get('form')['image'];
                    }
                    if (strpos($image, 'delete_') !== false) {
                        $arr_files = explode('delete_', $image);
                        $image = $arr_files[1];
                        $file_path = $this->getParameter('upload_directory') . '/' . $image;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    } else {
                        $nivosliderItem->setImage($image);
                    }
                }
            }

            $em = $this->getDoctrine()->getManager();
            $nivosliderItem = $form->getData();
            $datetime = new \DateTime();
            if ($id == null) {

                // bevor save nivoslider save
                $nivoslider = new Nivoslider();
                $nivoslider->setStatus(false);
                $em->persist($nivoslider);
                $em->flush();

                $langs = $this->getDoctrine()->getRepository('ClosasShopBundle:Language')->findAll();
                $i = 0;
                foreach ($langs as $lang) {
                    if ($i > 0) {
                        $nivosliderItemLang = new Item();
                        $nivosliderItemLang->setTitle($nivosliderItem->getTitle());
                        $nivosliderItemLang->setTitle2($nivosliderItem->getTitle2());
                        $nivosliderItemLang->setTitle3($nivosliderItem->getTitle3());
                        $nivosliderItemLang->setLink($nivosliderItem->getLink());
                        $nivosliderItemLang->setImage($nivosliderItem->getImage());
                        $nivosliderItemLang->setImage2($nivosliderItem->getImage2());
                        $nivosliderItemLang->setOrder($nivosliderItem->getOrder());
                        $nivosliderItemLang->setDescription($nivosliderItem->getDescription());
                        $nivosliderItemLang->setCreatedAt($datetime);
                        $nivosliderItemLang->setUpdatetAt($datetime);
                        $nivosliderItemLang->setLang($lang);
                        $nivosliderItemLang->setNivoslider($nivoslider);
                        $em->persist($nivosliderItemLang);
                        $em->flush();
                    } else {
                        $nivosliderItem->setCreatedAt($datetime);
                        $nivosliderItem->setUpdatetAt($datetime);
                        $nivosliderItem->setLang($lang);
                        $nivosliderItem->setNivoslider($nivoslider);
                        $em->persist($nivosliderItem);
                        $em->flush();
                    }
                    $i++;
                }
            } else {
                $nivosliderItem->setUpdatetAt($datetime);
                $em->persist($nivosliderItem);
                $em->flush();
            }
            return $this->redirectToRoute('admin_slider_item_nivo_list_lang', array('id' => $nivosliderItem->getNivoslider()->getId()));
        }


        return $this->render('ClosasAdminBundle:Slider:itemnivo.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Template()
     * @Route("/item/nivo/status/{id}/", name="admin_slider_item_nivo_status")
     */
    public function itemnivostatusAction($id = null, Request $request) {
        $nivoslider = $this->getDoctrine()->getRepository('ClosasShopBundle:Nivoslider')->findOneById($id);
        if (!$nivoslider->getStatus()) {
            $nivoslider->setStatus(true);
        } else {
            $nivoslider->setStatus(false);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($nivoslider);
        $em->flush();
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Template()
     * @Route("/item/list/lang/nivo/{id}/", name="admin_slider_item_nivo_list_lang")
     */
    public function itemlistlangnivoAction($id = null) {
        $nivoslider = $this->getDoctrine()->getRepository('ClosasShopBundle:Nivoslider')->findOneById($id);
        return array('nivosliders' => $nivoslider->getItems());
    }

    /**
     * @Template()
     * @Route("/item/list/nivo", name="admin_slider_item_nivo_list")
     */
    public function itemlistnivoAction() {
        $nivosliders = $this->getDoctrine()->getRepository('ClosasShopBundle:Nivoslider')->findAll();
        return array('nivosliders' => $nivosliders);
    }

}
