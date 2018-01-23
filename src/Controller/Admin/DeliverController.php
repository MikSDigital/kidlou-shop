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
use App\Entity\Deliver\Standard;
use App\Entity\Deliver;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/deliver")
 */
class DeliverController extends Controller {

    /**
     * @Template()
     * @Route("/index/", name="admin_deliver_index")
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Template()
     * @Route("/standard/index/",  name="admin_deliver_standard_index")
     */
    public function standardindexAction(Request $request) {

        $reposStandard = $this->getDoctrine()->getRepository(Deliver\Standard::class);
        $standard = $reposStandard->findOneBy([]);

        if (!$standard) {
            $standard = new Standard();
        }

        $form = $this->createFormBuilder($standard)
                ->add('monday', CheckboxType::class, array(
                    'label' => $this->get('translator')->trans('Monday'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Monday')),
                    'required' => false))
                ->add('tuesday', CheckboxType::class, array(
                    'label' => $this->get('translator')->trans('Tuesday'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Tuesday')),
                    'required' => false))
                ->add('wednesday', CheckboxType::class, array(
                    'label' => $this->get('translator')->trans('Wednesday'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Wednesday')),
                    'required' => false))
                ->add('thursday', CheckboxType::class, array(
                    'label' => $this->get('translator')->trans('Thursday'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Thursday')),
                    'required' => false))
                ->add('friday', CheckboxType::class, array(
                    'label' => $this->get('translator')->trans('Friday'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Friday')),
                    'required' => false))
                ->add('saturday', CheckboxType::class, array(
                    'label' => $this->get('translator')->trans('Saturday'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Saturday')),
                    'required' => false))
                ->add('sunday', CheckboxType::class, array(
                    'label' => $this->get('translator')->trans('Sunday'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Sunday')),
                    'required' => false))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $standard = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($standard);
            $em->flush();
            return $this->redirectToRoute('admin_deliver_standard_index');
        }

        return $this->render('ClosasAdminBundle:Deliver\Standard:index.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/exception/index/", name="admin_deliver_exception_index")
     */
    public function exceptionindexAction() {
        return $this->render('ClosasAdminBundle:Deliver\Exception:index.html.twig');
    }

    /**
     * @Route("/exception/update/", name="admin_deliver_exception_update")
     * @Route("/exception/update/{datum}/{isdeliver}/")
     */
    public function exceptionupdateAction($datum, $isdeliver) {
        $em = $this->getDoctrine()->getManager();
        $deliver = $this->getDoctrine()->getRepository(Deliver::class)->findOneBy(array('date' => new \DateTime($datum)));
        if (!$deliver) {
            $deliver = new Deliver();
            $deliver->setDate(new \DateTime($datum));
            $deliver->setIsDeliver($isdeliver);
            $em->persist($deliver);
        } else {
            $em->remove($deliver);
        }
        $em->flush();
        return new JsonResponse();
    }

}
