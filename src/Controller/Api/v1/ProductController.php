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
use App\Entity\Product;
use App\Entity\Product\Description;

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
        $lang = $request->query->get('lang');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                "SELECT p.id, p.sku, pd.name, p.status
                    FROM App\Entity\Product p
                    INNER JOIN App\Entity\Product\Typ pt WITH p.typ = pt.id AND pt.short_name = 'SIP'
                    INNER JOIN App\Entity\Product\Description pd WITH p.id = pd.product
                    INNER JOIN App\Entity\Language l WITH pd.lang = l.id AND l.short_name = '" . $lang . "'
                "
        );
        $products = $query->getArrayResult();
        $response = new JsonResponse();
        $response->setData($products);
        return $response;
    }

    /**
     * get user by id
     *
     * @Route("/products/{id}")
     * @Method("GET")
     */
    public function showAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                "SELECT p.id, p.sku, p.status, pd.name, pd.short_text, pd.long_text, pd.indicies, pd.accessoires, pr.value, l.short_name, l.name AS lang_name
                    FROM App\Entity\Product p
                    INNER JOIN App\Entity\Product\Typ pt WITH p.typ = pt.id AND pt.short_name = 'SIP' AND p.id = {$id}
                    INNER JOIN App\Entity\Product\Description pd WITH p.id = pd.product AND pd.lang IS NOT NULL
                    INNER JOIN App\Entity\Language l WITH pd.lang = l.id
                    INNER JOIN App\Entity\Price pr WITH p.id = pr.product
                    ORDER BY l.id ASC
                "
        );
        $data = array();
        $products = $query->getArrayResult();
        $response = new JsonResponse();
        $response->setData($products);
        return $response;
    }

    /**
     * update/replace user
     *
     * @Route("/products/{id}")
     * @Method("PUT")
     */
    public function updateAction($id, Request $request) {
//        $response = new JsonResponse();

        $datas = $request->getContent();
        $datas = json_decode($datas);
        $arr_test = array();

        $em = $this->getDoctrine()->getManager();
        foreach ($datas as $data) {
            $productDescriptions = $this->getDoctrine()->getRepository(\App\Entity\Product\Description::class)
                    ->findBy(array('id' => $data->id), array('lang' => 'ASC'));
            foreach ($productDescriptions as $productDescription) {
                $productDescription->setName($data->name);
                $productDescription->setLongText($data->long_text);
                $productDescription->setShortText($data->short_text);
                $productDescription->setIndicies($data->indicies);
                $productDescription->setAccessoires($data->accessoires);
                $em->persist($productDescription);
                $em->flush();

                // noch price
            }
            $arr_test[] = $data->name;
        }

        $response = new JsonResponse();
        $response->setData($arr_test);


        return $response;
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
