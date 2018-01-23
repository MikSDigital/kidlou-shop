<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Upload;
use App\Form\UploadType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * @Route("/upload")
 */
class UploadController extends Controller {

    /**
     * @Template()
     * @Route("/index", name="admin_upload_index")
     */
    public function indexAction(Request $request) {
        $upload = new Upload();
        $form = $this->createForm(UploadType::class, $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $upload->getProducts();

            // Generate a unique name for the file before saving it
            // $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $fileName = md5(uniqid()) . '.csv';
            // Move the file to the directory where brochures are stored
            $file->move($this->getParameter('products_directory'), $fileName);
            // Update the 'products' property to store the CSV file name
            // instead of its contents
            // set all other to inactive
            $em = $this->getDoctrine()->getManager();
            $upload->setProducts($fileName);
            $upload->setCreatedAt(new \DateTime());
            $upload->setIsActive(TRUE);
            $em->persist($upload);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_upload_list'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Template()
     * @Route("/list", name="admin_upload_list")
     */
    public function listAction() {
        $uploads = $this->getDoctrine()->getRepository('ClosasAdminBundle:Upload')->findAll();
        return array(
            'uploads' => $uploads
        );
    }

    /**
     * @Route("/delete/{id}/", name="admin_upload_delete")
     */
    public function deleteAction($id, Request $request) {
        $fs = new Filesystem();
        $em = $this->getDoctrine()->getManager();
        $upload = $this->getDoctrine()->getRepository('ClosasAdminBundle:Upload')->findOneById($id);
        $fs->remove($this->get('kernel')->getRootDir() . '/../web/media/import/csv/' . $upload->getProducts());
        $em->remove($upload);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_upload_list'));
    }

}
