<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
use App\Entity\Mail;
use App\Entity\Mail\Send;
use App\Entity\Inbox;
use App\Service\Common As ServiceCommon;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DomCrawler\Crawler;

//use PhpImap\Mailbox as ImapMailbox;
//use PhpImap\IncomingMail;
//use PhpImap\IncomingMailAttachment;

/**
 * @Route("/mail")
 */
class MailController extends Controller {

    /**
     * @Template()
     * @Route("/index/", name="admin_mail_index")
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Template()
     * @Route("/list/", name="admin_mail_list")
     */
    public function listAction() {
        $mails = $this->getDoctrine()->getRepository(Mail::class)->findAll();
        return array(
            'mails' => $mails
        );
    }

    /**
     * @Template()
     * @Route("/detail/", name="admin_mail_detail_new")
     * @Route("/detail/{id}/", name="admin_mail_detail")
     */
    public function detailAction($id = null, Request $request) {
        if ($id != null) {
            $reposMail = $this->getDoctrine()->getRepository(Mail::class);
            $mail = $reposMail->findOneById($id);
        } else {
            $mail = new Mail();
        }

        $form = $this->createFormBuilder($mail)
                ->add('type', ChoiceType::class, array(
                    'choices' => array('Production Mail' => 'product', 'Common Mail' => 'common'),
                    'label' => $this->get('translator')->trans('Mail Type')))
                ->add('from_email', TextType::class, array(
                    'label' => $this->get('translator')->trans('From email'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('From email'))))
                ->add('from_name', TextType::class, array(
                    'label' => $this->get('translator')->trans('From name'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('From name')),
                    'required' => false))
                ->add('bcc_email', TextType::class, array(
                    'label' => $this->get('translator')->trans('Bcc email'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Bcc email')),
                    'required' => false))
                ->add('status', ChoiceType::class, array(
                    'choices' => array('Disable' => FALSE, 'Enable' => TRUE),
                    'label' => $this->get('translator')->trans('Status')))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            return $this->redirectToRoute('admin_mail_list');
        }

        return array('form' => $form->createView());
    }

    /**
     * @Template()
     * @Route("/send/", name="admin_mail_send_detail_new")
     * @Route("/send/{id}/", name="admin_mail_send_detail")
     */
    public function sendAction($id = null, Request $request, ServiceCommon $serviceCommon) {
        if ($id != null) {
            $reposMail = $this->getDoctrine()->getRepository(Mail\Send::class);
            $mail = $reposMail->findOneById($id);
        } else {
            $mail = new Send();
        }

        $form = $this->createFormBuilder($mail)
                ->add('to_email', TextType::class, array(
                    'label' => $this->get('translator')->trans('To email'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('To email'))))
                ->add('subject', TextType::class, array(
                    'label' => $this->get('translator')->trans('Subject'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Subject'))))
                ->add('body', TextareaType::class, array(
                    'label' => $this->get('translator')->trans('Text'),
                    'attr' => array('placeholder' => $this->get('translator')->trans('Text'))))
                ->add('save', SubmitType::class, array('label' => $this->get('translator')->trans('Save')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data->getCreatedAt() == '') {
                $data->setCreatedAt(new \Datetime());
            }
            $data->setUpdatedAt(new \Datetime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            $serviceCommon->sendCommonEmailMessage($data->getSubject(), $data->getBody(), $data->getToEmail());
            return $this->redirectToRoute('admin_mail_send_list');
        }

        return array('form' => $form->createView());
    }

    /**
     * @Template()
     * @Route("/sendlist/", name="admin_mail_send_list")
     */
    public function sendlistAction() {
        $sends = $this->getDoctrine()->getRepository(Mail\Send::class)->findAll();
        return array(
            'sends' => $sends
        );
    }

    /**
     * @Template()
     * @Route("/inboxlist/", name="admin_in_box_list")
     */
    public function inboxlistAction() {
        ini_set('max_execution_time', 600);

        $mailbox = $this->_getMailbox();
        // Read all messaged into an array:
        $mailsIds = $mailbox->searchMailbox('ALL');
        if (!$mailsIds) {
            die('Mailbox is empty');
        }
        $mailsIds = $mailbox->sortMails();
        $mailboxInfos = $mailbox->getMailsInfo($mailsIds);
        foreach ($mailboxInfos as $mailboxInfo) {
            $inbox = $this->getDoctrine()->getRepository(Inbox::class)->findOneBy(array('email_id' => $mailboxInfo->uid));
            if (!$inbox) {
                $inbox = new Inbox();
            }
            $inbox->setEmailId($mailboxInfo->uid);
            if ($mailboxInfo->subject != '') {
                $inbox->setSubject($mailboxInfo->subject);
            } else {
                $inbox->setSubject($this->get('translator')->trans('Kein Betreff'));
            }
            $inbox->setParentId(0);
            $inbox->setDate(new \DateTime($mailboxInfo->date));
            $inbox->setFromName($mailboxInfo->from);
            $inbox->setToName($mailboxInfo->to);
            $em = $this->getDoctrine()->getManager();
            $em->persist($inbox);
            $em->flush();
        }
        $inboxs = $this->getDoctrine()->getRepository(Inbox::class)->findBy(array(), array('date' => 'DESC'));
        return array('inboxs' => $inboxs);
    }

    /**
     * @Template()
     * @Route("/inbox/{id}/", name="admin_in_box")
     */
    public function inboxAction($id) {
        $inbox = $this->getDoctrine()->getRepository(Inbox::class)->findOneById($id);
        if (!$inbox) {
            $inbox = new Inbox();
        }
        $mailbox = $this->_getMailbox();
        $mail = $mailbox->getMail($inbox->getEmailId());
        $inbox->setParentId(0);
        $inbox->setFromAddress($mail->fromAddress);
        $inbox->setToAddress($this->_getAddresses($mail->to));
        $inbox->setToName($this->_getName($mail->to));
        $inbox->setCcAddress($this->_getAddresses($mail->cc));
        $inbox->setCcName($this->_getName($mail->cc));
        $inbox->setBccName($this->_getName($mail->bcc));
        $inbox->setBccAddress($this->_getName($mail->bcc));
        $inbox->setTextPlain($mail->textPlain);
        $inbox->setTextHtml($mail->textHtml);
        $inbox->setFilePaths($this->_getFilePath($mail));
        $inbox->setFileNames($this->_getFileName($mail));
        $em = $this->getDoctrine()->getManager();
        $em->persist($inbox);
        $em->flush();

        $inbox = $this->getDoctrine()->getRepository(Inbox::class)->findOneById($id);
        $crawler = '';
        if ($inbox->getTextHtml() != '') {
            $crawler = new Crawler($inbox->getTextHtml());
            $inbox->setTextHtml($crawler->text());
        }
        return array('inbox' => $inbox);
    }

    /**
     * @Template()
     * @Route("/inboxdelete/", name="admin_in_box_delete")
     */
    public function inboxdeleteAction(Request $request) {
        $datas = $request->request->get('inboxdelete');
        $inboxs = $this->getDoctrine()->getRepository(Inbox::class)->findBy(array('id' => $datas));
        $mailbox = $this->_getMailbox();
        $em = $this->getDoctrine()->getManager();
        foreach ($inboxs as $inbox) {
            $mailbox->deleteMail($inbox->getEmailId());
            $em->remove($inbox);
            $em->flush();
        }
        return $this->redirectToRoute('admin_in_box_list');
    }

    /**
     * @Template()
     * @Route("/inboxresponse/", name="admin_in_box_response")
     */
    public function inboxresponseAction(Request $request, ServiceCommon $serviceCommon) {
        $data = $request->request->get('mail');
        $serviceCommon->sendCommonEmailMessage($data['subject'], $data['body'], $data['to_address']);
        return $this->redirectToRoute('admin_in_box_list', $data);
    }

    /**
     *
     * @param mail $mail
     * @return date format
     */
    private function _getDate($mail) {
        $arr_date = explode(' ', $mail->headers->date);
        if (isset($arr_date[5])) {
            $hour = substr($arr_date[5], 2, 1);
        } else {
            $hour = 0;
        }
        $date = new \DateTime($mail->date);
        $date->add(new \DateInterval('PT' . $hour . 'H'));
        return $date;
    }

    /**
     *
     * @param datas $datas
     * @return string addresses
     */
    private function _getAddresses($datas) {
        $str = '';
        foreach ($datas as $address => $name) {
            if ($str != '') {
                $str .= ',' . $address;
            } else {
                $str = $address;
            }
        }
        return $str;
    }

    /**
     *
     * @param datas $datas
     * @return string $name
     */
    private function _getName($datas) {
        $str = '';
        foreach ($datas as $address => $name) {
            if ($name != '') {
                if ($str != '') {
                    $str .= ',<' . $name . '>';
                } else {
                    $str = '<' . $name . '>';
                }
            }
        }
        return $str;
    }

    /**
     *
     * @param mail $mail
     * @return string $string
     */
    private function _getFilePath($mail) {
        $str = '';
        foreach ($mail->getAttachments() as $attachement) {
            if ($str != '') {
                $str .= ',' . $attachement->filePath;
            } else {
                $str = $attachement->filePath;
            }
        }
        return $str;
    }

    private function _getFileName($mail) {
        $str = '';
        foreach ($mail->getAttachments() as $attachement) {
            if ($str != '') {
                $str .= ',' . $attachement->name;
            } else {
                $str = $attachement->name;
            }
        }
        return $str;
    }

    private function _getMailbox() {
        $path = $this->get('kernel')->getRootDir() . '/../public/media/mail/inbox/';
        $fs = new Filesystem();
        if (!$fs->exists($path)) {
            $fs->mkdir($path, 0755);
        }
        //return new ImapMailbox('{imap.mail.hostpoint.ch:993/imap/ssl}INBOX', 'miguel@closas.ch', 'anne+noe+lio+$#', $path);
    }

}
