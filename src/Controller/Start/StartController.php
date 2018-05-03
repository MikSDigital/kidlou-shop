<?php

namespace App\Controller\Start;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StartController extends Controller {

    /**
     * @Route("/", name="start_index_page")
     */
    public function indexAction() {
        return $this->redirectToRoute('index_page');
    }

}
