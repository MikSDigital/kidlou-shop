<?php

namespace App\Controller\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/v1")
 */
class UserController extends Controller {

    /**
     * Create new user
     *
     * @Route("/users")
     * @Method("POST")
     */
    public function newAction(Request $request) {
        //$em = $this->getDoctrine()->getManager();
        return new Response('Let\'s post do this!');
    }

    /**
     * get users
     *
     * @Route("/users", name="api_get_users")
     * @Method("GET")
     */
    public function showAllAction(Request $request) {
        return new Response('Let\'s GET do this!');
    }

    /**
     * get user by id
     *
     * @Route("/users/{id}")
     * @Method("GET")
     */
    public function showAction($id, Request $request) {

    }

    /**
     * update/replace user
     *
     * @Route("/users/{id}")
     * @Method("PUT")
     */
    public function updateAction($id, Request $request) {

    }

    /**
     * update/modify user
     *
     * @Route("/users/{id}")
     * @Method("PATCH")
     */
    public function patchAction($id, Request $request) {

    }

    /**
     * delete user
     *
     * @Route("/users/{id}")
     * @Method("DELETE")
     */
    public function deleteAction($id, Request $request) {

    }

}
