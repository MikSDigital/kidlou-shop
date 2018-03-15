<?php

namespace App\Controller\Test;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller {

    /**
     * @Route("/index/", name="index_test")
     */
    public function indexAction(Request $request) {
        $datas = $this->getDoctrine()->getRepository(Quote::class)->getBasketItems(37, 'image80', 'image80', $request->getLocale());
        print_r($datas);
        exit;
        return array();
    }

    /**
     * @Route("/token/", name="token_test")
     */
    public function tokenAction() {
        $api_endpoint = "https://api.sandbox.paypal.com/v1/oauth2/token";
        $clientId = "AbWC1ORqKB2pksz3s5gEDASVugIzv0MuF6m3fySR4ZZFOkPZRwGDNBx0l8zR31tAaMWoX0fdElZKKWPE";
        $secretKey = "EKL4JF0xTutP3Ldrdc3C86juGBLLyqRY79P4zZ4fO7D3Mtf3Oppv6-28CcrHCDYDIu7s1fYDhN65OlEa";
        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secretKey);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        // Get response from the server.
        $result = curl_exec($ch);

        if (!$result) {
            exit("_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }
        $json = json_decode($result);
        print_r($json->access_token);
        exit;
    }

}
