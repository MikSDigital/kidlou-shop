<?php

namespace App\Controller\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;

/**
 * @Route("/v1")
 */
class ProductController extends Controller {

    /**
     * Create new product
     *
     * @Route("/products")
     * @Method("POST")
     */
    public function newAction(Request $request) {
        //$em = $this->getDoctrine()->getManager();
        return new Response('Let\'s post do this!');
    }

    /**
     * get users
     *
     * @Route("/products", name="api_get_products")
     * @Method("GET")
     */
    public function showAllProducts(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                'SELECT p.id, p.sku, p.url_key, p.status
                    FROM App\Entity\Product p'
        );
        $products = $query->getArrayResult();
        $response = new Response();
        $response->setContent(json_encode($products));
        $response->headers->set('Content-Type', 'application/json');
        // Allow all websites
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
        //return new JsonResponse($products);
    }

    /**
     * get user by id
     *
     * @Route("/products/{id}")
     * @Method("GET")
     */
    public function showAction($id, Request $request) {

    }

    /**
     * update/replace user
     *
     * @Route("/products/{id}")
     * @Method("PUT")
     */
    public function updateAction($id, Request $request) {

    }

    /**
     * update/modify user
     *
     * @Route("/products/{id}")
     * @Method("PATCH")
     */
    public function patchAction($id, Request $request) {

    }

    /**
     * delete user
     *
     * @Route("/products/{id}")
     * @Method("DELETE")
     */
    public function deleteAction($id, Request $request) {

    }

}
