<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/exception")
 */
class ExceptionController extends Controller {

    /**
     * @Template()
     * @Route("/error404")
     */
    public function error404Action() {

        echo "Error!!";
        exit;
        return array(
        );
    }

}
