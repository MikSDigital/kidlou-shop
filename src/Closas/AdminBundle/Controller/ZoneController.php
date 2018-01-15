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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Closas\ShopBundle\Entity\Zone;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/zone")
 */
class ZoneController extends Controller {

    /**
     * @Template()
     * @Route("/index/", name="admin_zone_index")
     */
    public function indexAction(Request $request) {
        $zone = $this->getDoctrine()->getRepository('ClosasShopBundle:Zone')->findAll();
        if ($zone) {
            foreach ($zone as $_zone) {
                $zone = $_zone;
            }
        } else {
            $zone = new Zone();
        }

        $form = $this->createFormBuilder($zone)
                ->add('latitude', TextType::class, array(
                    'label' => $this->get('translator')->trans('Latitude'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Latitude'))))
                ->add('longitude', TextType::class, array(
                    'label' => $this->get('translator')->trans('Longitude'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Longitude'))))
                ->add('equatorial_radius', TextType::class, array(
                    'label' => $this->get('translator')->trans('Equatorial Radius'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Equatorial Radius'))))
                ->add('distance', TextType::class, array(
                    'label' => $this->get('translator')->trans('Distance'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Distance'))))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            return $this->redirectToRoute('admin_zone_index');
        }

        return array('form' => $form->createView());
    }

}
