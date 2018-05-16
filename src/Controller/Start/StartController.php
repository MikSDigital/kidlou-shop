<?php

namespace App\Controller\Start;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StartController extends Controller {

    /**
     * @Route("/", name="start_index_page")
     */
    public function indexAction(Request $request) {
        $uri = $request->getUri();
        $http_accept_language = $request->server->get('HTTP_ACCEPT_LANGUAGE');
        if (isset($http_accept_language)) {
            $lang = $this->parseDefaultLanguage($http_accept_language);
            $uri .= $lang;
        } else {
            //return parseDefaultLanguage(NULL);
        }

        return new RedirectResponse($uri);
        //return $this->redirectToRoute('index_page');
    }

    /**
     *
     * @param type $http_accept
     * @param type $deflang
     * @return string
     */
    private function parseDefaultLanguage($http_accept, $deflang = "en") {
        $defaultLang = 'fr';
        $langs = $this->getDoctrine()->getRepository(\App\Entity\Language::class)->findAll();
        if (isset($http_accept) && strlen($http_accept) > 1) {
            # Split possible languages into array
            $x = explode(",", $http_accept);
            foreach ($x as $val) {
                #check for q-value and create associative array. No q-value means 1 by rule
                if (preg_match("/(.*);q=([0-1]{0,1}.\d{0,4})/i", $val, $matches))
                    $lang[$matches[1]] = (float) $matches[2];
                else
                    $lang[$val] = 1.0;
            }
            #return default language (highest q-value)
            $qval = 0.0;
            foreach ($lang as $key => $value) {
                if ($value > $qval) {
                    $qval = (float) $value;
                    $deflang = $key;
                }
            }

            $deflang = explode('-', $deflang)[0];
            foreach ($langs as $lang) {
                if ($lang->getShortName() == strtolower($deflang)) {
                    return strtolower($deflang);
                }
            }
        }
        return $defaultLang;
    }

}
