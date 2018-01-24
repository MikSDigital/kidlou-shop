<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/download")
 */
class DownloadController extends Controller {

    /**
     * @Route("/productpdf/{id}", name="pdf_product")
     * @Route("/productpdf/{id}/{product_id}/{lang}/", name="pdf_product_admin", defaults={"id" = "0"})
     */
    public function productPdfAction($id = '0', $product_id = '', $lang = '') {
        if ($id) {
            $product = $this->get('helper.product')->getProduct($id);
            foreach ($product->getDescriptions() as $description) {
                if ($description->getLang() == $this->get('helper.common')->getLanguage()) {
                    $_absoluteFilename = $this->get('kernel')->getRootDir() . "/../public/media/import/images/" . $product->getSku() . "/" . $description->getAccessoires() . '.pdf';
                }
            }
        }
        if ($product_id != '') {
            $reposProduct = $this->getDoctrine()->getRepository(Product::class);
            $product = $reposProduct->findOneById($product_id);
            foreach ($product->getDescriptions() as $description) {
                if (!is_null($description->getLang())) {
                    if ($description->getLang()->getShortName() == $lang) {
                        $_absoluteFilename = $this->get('kernel')->getRootDir() . "/../public/media/import/images/" . $product->getSku() . "/" . $description->getAccessoires() . '.pdf';
                    }
                }
            }
        }

        $str_filename = $this->get('translator')->trans('Notice_utilisation') . '.pdf';
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . urlencode($str_filename));
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize($_absoluteFilename));
        flush(); // this doesn't really matter.
        $fp = fopen($_absoluteFilename, "r");
        while (!feof($fp)) {
            echo fread($fp, 65536);
            flush(); // this is essential for large downloads
        }
        fclose($fp);
    }

}

?>
