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
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Order As ServiceOrder;

/**
 * @Route("/order")
 */
class OrderController extends Controller {

    /**
     * @Template()
     * @Route("/list", name="admin_order_list")
     */
    public function listAction() {
        $orders = $this->getDoctrine()->getRepository(Quote::class)->getAllOrderDatas();
        return array(
            'orders' => $orders
        );
    }

    /**
     * @Template()
     * @Route("/detail/{id}/", name="admin_order_detail")
     * @Route("/detail/{id}/{success}/{message}/", name="admin_order_detail_message")
     */
    public function detailAction($id = null, $success = false, $message = false, Request $request, ServiceOrder $serviceOrder) {
        $arr_order = array();
        $arr_order = $serviceOrder->getOrderDataById($id);
        if ($message) {
            if ($success) {
                $message = $this->get('translator')->trans('Email is send successfull');
            } else {
                $message = $this->get('translator')->trans('Email is not send successfull');
            }
        }

        return array(
            'order' => $arr_order,
            'message' => $message,
            'success' => $success
        );
    }

    /**
     * @Route("/sendmail/{id}/", name="admin_send_mail")
     */
    public function sendMail($id = null, ServiceOrder $serviceOrder, \Swift_Mailer $mailer) {
        $order = $this->getDoctrine()->getRepository(Quote::class)->findOneById($id);
        $result = $serviceOrder->sendEmailMessage($order, $mailer);
        return $this->redirect($this->generateUrl('admin_order_detail_message', array('id' => $order->getId(), 'success' => $result, 'message' => true)));
    }

}
