<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Category;
use App\Entity\Category\Label;
use App\Entity\Image;
use App\Entity\Image\Size;
use App\Entity\Content;
use App\Entity\Content\Group;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use App\Service\Image As ServiceImage;
use App\Service\Common As ServiceCommon;
use App\Entity\AdminUser;

/**
 * @Route("/cms")
 */
class CmsController extends Controller {

    /**
     * @Template()
     * @Route("/", name="admin_cms_index")
     */
    public function indexAction(ServiceCommon $serviceCommon) {

        $navigation = $serviceCommon->getNavigation('category', false);
        return array(
            'navigation' => $navigation
        );
    }

    /**
     * @Template()
     * @Route("/detail/{id}/", defaults={"id" = ""}, name="admin_cms_detail")
     * @Route("/detail/{id}/{lang}/", defaults={"lang" = ""}, name="admin_cms_detail_lang")
     */
    public function detailAction($id = "", $lang = "") {

        if (empty($lang)) {
            $lang = $this->container->getParameter('locale');
        }
        $reposLanguage = $this->getDoctrine()->getRepository(Language::class);
        $lang = $reposLanguage->findOneBy(array('short_name' => $lang));
        $reposCategory = $this->getDoctrine()->getRepository(Category::class);
        $category = $reposCategory->findOneById($id);

        // get label name
        $arr_cat_label = array();
        foreach ($category->getLabels() as $label) {
            if ($label->getLang()->getId() == $lang->getId()) {
                $arr_cat_label['name'] = $label->getName();
                $arr_cat_label['id'] = $category->getId();
            }
        }

        // response to admin/cms.js
        return new JsonResponse(array(
            'html' => $this->getContent($category, $lang),
            'catlabel' => $arr_cat_label)
        );
    }

    /**
     * @Template()
     * @Route("/savesite/", name="admin_cms_site_save")
     */
    public function saveSiteAction(Request $request) {
        $id = $request->request->get('id');
        $cat_typ_id = $request->request->get('cat_typ_id');
        $is_active = $request->request->get('is_active');
        $url_key = $request->request->get('url_key');
        $labels = $request->request->get('labels');
        $file = $request->files->get("image");

        $delete_image = $request->request->get('delete_image');

        $reposCatLabel = $this->getDoctrine()->getRepository(Category\Label::class);
        $em = $this->getDoctrine()->getManager();
        foreach ($labels as $labelid => $name) {
            $catLabel = $reposCatLabel->findOneById($labelid);
            $catLabel->setName($name);
            $em->persist($catLabel);
            $em->flush();
        }

        $reposCatTyp = $this->getDoctrine()->getRepository(Category\Typ::class);
        $catTyp = $reposCatTyp->findOneById($cat_typ_id);

        $reposCat = $this->getDoctrine()->getRepository(Category::class);
        $cat = $reposCat->findOneById($id);

        if ($delete_image) {
            $this->deleteImagesFile('category', $cat);
            $this->deleteImagesDb('category', $cat);
        } else {
            if ($file) {
                // check if image exist delete the files
                $this->deleteImagesFile('category', $cat);
                // add the new file
                $fileExtension = $file->guessExtension();
                $fileClientOriginalName = $file->getClientOriginalName();
                $fileClientMimeType = $file->getClientMimeType();
                $reposCatImage = $this->getDoctrine()->getRepository(Image::class);
                $reposCatImageSize = $this->getDoctrine()->getRepository(Image\Size::class);
                foreach ($reposCatImageSize->findAll() as $key => $size) {
                    $_targetDir = $this->get('kernel')->getRootDir() . '/../public/' . $size->getPath();
                    $fileName = md5(uniqid()) . '.' . $fileExtension;
                    $_targetFile = $_targetDir . $fileName;
                    if ($key == 0) {
                        $file->move($_targetDir, $fileName);
                        $source_file = $_targetDir . $fileName;
                    } else {
                        $fs = new Filesystem();
                        $fs->copy($source_file, $_targetFile);
                    }
                    $catImage = $reposCatImage->findOneBy(array('category' => $cat, 'size' => $size));
                    if (!$catImage) {
                        $catImage = new Image();
                    }
                    $catImage->setName($fileName);
                    $catImage->setOriginalName($fileClientOriginalName);
                    $catImage->setSize($size);
                    $catImage->setCategory($cat);
                    $catImage->setMimetyp($fileClientMimeType);
                    $catImage->setIsDefault(true);
                    $em->persist($catImage);
                    $em->flush();
                }
                // resize file
                foreach ($reposCatImageSize->findAll() as $key => $size) {
                    $catImage = $reposCatImage->findOneBy(array('category' => $cat, 'size' => $size));
                    $_targetDir = $this->get('kernel')->getRootDir() . '/../public/' . $size->getPath();
                    $_targetFile = $_targetDir . $catImage->getName();
                    $arr_file_name = explode('.', $catImage->getName());
                    $this->get('helper.imageresizer')->resizeImage($_targetFile, $_targetDir, $arr_file_name[0], $height = $size->getHeight());
                }
            }
        }

        $cat->setUrlKey($url_key);
        $cat->setTyp($catTyp);
        $cat->setStatus($is_active);
        $em->persist($catTyp);
        $em->flush();

        $response = $this->forward('App\Controller\Admin\CmsController::detail', array(
            'id' => $id,
        ));

        return $response;
    }

    /**
     *
     * @param type $cat
     */
    private function deleteImagesFile($field, $obj) {
        $reposImage = $this->getDoctrine()->getRepository(Image::class);
        $reposImageSize = $this->getDoctrine()->getRepository(Image\Size::class);
        foreach ($reposImageSize->findAll() as $size) {
            $image = $reposImage->findOneBy(array($field => $obj, 'size' => $size));
            if ($image) {
                $fs = new Filesystem();
                $fileName = $this->get('kernel')->getRootDir() . '/../public/' . $size->getPath() . $image->getName();
                if ($fs->exists($fileName)) {
                    $fs->remove($fileName);
                }
            }
        }
    }

    /**
     *
     * @param type $cat
     */
    private function deleteImagesDb($field, $obj) {
        $em = $this->getDoctrine()->getManager();
        $reposImage = $this->getDoctrine()->getRepository(Image::class);
        $reposImageSize = $this->getDoctrine()->getRepository(Image\Size::class);
        foreach ($reposImageSize->findAll() as $size) {
            $image = $reposImage->findOneBy(array($field => $obj, 'size' => $size));
            if ($image) {
                $em->remove($image);
                $em->flush();
            }
        }
    }

    /**
     * @Template()
     * @Route("/addnewcontent/{id}/{lang}/", defaults={"id" = "", "lang" = ""}, name="admin_add_new_content")
     */
    public function addNewContentAction($id = '', $lang = '') {

        $reposCat = $this->getDoctrine()->getRepository(Category::class);
        $category = $reposCat->findOneById($id);
        $reposLanguage = $this->getDoctrine()->getRepository(Language::class);
        $lang = $reposLanguage->findOneBy(array('short_name' => $lang));
        return new JsonResponse(array('html' => $this->getContent($category, $lang, true)));
    }

    /**
     * @Template()
     * @Route("/savecontent/", name="admin_save_content")
     */
    public function saveContentAction(Request $request, ServiceImage $serviceImage, ServiceCommon $serviceCommon) {
        $catid = $request->request->get('catid');
        $reposCategory = $this->getDoctrine()->getRepository(Category::class);
        $reposLanguage = $this->getDoctrine()->getRepository(Language::class);
        $category = $reposCategory->findOneById($catid);
        $arr_content = $request->request->get('content');
        $arr_file = $request->files->get('image');
//        $arr_file1 = $request->files->all();

        $em = $this->getDoctrine()->getManager();
        $arr_obj_contents = array();
        foreach ($arr_content as $id => $data_content) {
            $newImage = false;
            $newCategory = false;
            $newContent = false;
            $arr_obj_contents = array();
            $lang = $reposLanguage->findOneBy(array('short_name' => $request->request->get('langid')));
            $reposContent = $this->getDoctrine()->getRepository(Content::class);
            $content = $reposContent->findOneBy(array('id' => $id));
            if (isset($arr_file[$id])) {
                $image = $serviceImage->setRequestByParams('image', $id, 'image');
            }
            if (!$content) {
                $newContent = true;
                foreach ($serviceCommon->getLanguages() as $lang) {
                    $arr_obj_contents[$lang->getId()] = new Content();
                }

                if (isset($arr_file[$id])) {
                    if ($image->isFile()) {
                        $image = $image->addImage()->save(1);
                        $newImage = $image->getIsNew();
                    }
                }

                $group = new Group();
                $em->persist($group);
                $em->flush();
            } else {
                if (isset($arr_file[$id])) {
                    if ($image->isFile()) {
                        $image = $image->getImageByContent($id)->removeImageByContent()->save(1);
                        $newImage = $image->getIsNew();
                    }
                }

                $arr_obj_contents[$lang->getId()] = $content;
                $order = $content->getOrder();
            }
            // wenn new dann in allen vier sprachen default schreiben
            foreach ($arr_obj_contents as $langid => $content) {
                $content->setOrder(0);
                foreach ($data_content as $field => $value) {
                    // build funcname
                    $arr_funcname = explode('_', $field);
                    $funcname = 'set';
                    foreach ($arr_funcname as $name) {
                        // bei user brauche ich objekt
                        $funcname .= ucfirst($name);
                    }
                    if ($field == 'user') {
                        $reposUser = $this->getDoctrine()->getRepository(AdminUser::class);
                        $value = $reposUser->findOneById($value);
                        // bei long text body herauscrawlern
                    } else if ($field == 'long_text') {
                        $crawler = new Crawler($value);
                        $crawler = $crawler->filter('body');
                        $value = trim($crawler->html());
                    } else if ($field == 'title') {
                        if ($langid == 1) {
                            $blog_url_key = str_replace(' ', '', strtolower(preg_replace('/[^a-z0-9 ]/i', '', $value)));
                        }
                    }
                    // bei status alle sprachen
                    $content->$funcname($value);
                    //$string = preg_replace ( '/[^a-z0-9 ]/i', '', $string );
                }

                if ($newCategory) {
                    $content->addCategory($category);
                }

                if ($newContent) {
                    $content->setGroup($group);
                }

                if ($newImage && $newContent) {
                    if ($category->getTyp()->getShortName() == 'BLOG') {
                        $content->addImage($image->getImage());
                    }
                }

                if ($newImage && !$newContent) {
                    if ($category->getTyp()->getShortName() == 'BLOG') {
                        $contents = $this->getDoctrine()->getRepository(Content::class)->findBy(array('group' => $content->getGroup()));
                        foreach ($contents as $imgcont) {
                            $imgcont->addImage($image->getImage());
                        }
                    }
                }

                $content->setUpdatetAt(new \DateTime());
                $content->setLang($reposLanguage->findOneById($langid));
                $em->persist($content);
                $em->flush();

                if (!$newCategory && $newContent) {
                    $category->addContent($content);
                    $em->persist($content);
                    $em->flush();
                }

                if ($langid == 1) {
                    $this->setContentUrlKey($category, $content, $em, $blog_url_key);
                }
            }
        }

        $langid = $request->request->get('langid');
        $lang = $reposLanguage->findOneBy(array('short_name' => $langid));

        return new JsonResponse(array('html' => $this->getContent($category, $lang)));
    }

    /**
     * @Template()
     * @Route("/deletecontent/", name="admin_delete_content")
     */
    public function deleteContentAction(Request $request) {
        $id = $request->request->get('id');
        $catid = $request->request->get('catid');
        $langid = $request->request->get('langid');
        $content = $this->getDoctrine()->getRepository(Content::class)->findOneBy(array('id' => $id));

        $group = $content->getGroup();
        $contentGroup = $this->getDoctrine()->getRepository(Content\Group::class)->findOneBy(array('id' => $group->getId()));
        $contents = $this->getDoctrine()->getRepository(Content::class)->findBy(array('group' => $content->getGroup()));
        $em = $this->getDoctrine()->getManager();
        foreach ($contents as $content) {
            foreach ($content->getImages() as $image) {
                foreach ($image->getSizes() as $size) {
                    $filename = $this->get("kernel")->getRootDir() . '/../public/' . $size->getPath() . $image->getName();
                    if (is_file($filename)) {
                        unlink($filename);
                    }
                }
            }
            $em->remove($content);
            $em->flush();
        }
        // content Group
        $em->remove($contentGroup);
        $em->flush();

        $category = $this->getDoctrine()->getRepository(Category::class)->findOneById($catid);
        $lang = $this->getDoctrine()->getRepository(Language::class)->findOneBy(array('short_name' => $langid));

        return new JsonResponse(array('html' => $this->getContent($category, $lang)));
    }

    /**
     * @Template()
     * @Route("/deleteimagecontent/", name="admin_delete_content_image")
     */
    public function deleteImageContentAction(Request $request) {
        $id = $request->request->get('id');
        $catid = $request->request->get('catid');
        $langid = $request->request->get('langid');
        $content = $this->getDoctrine()->getRepository(Content::class)->findOneBy(array('id' => $id));
        $group = $content->getGroup();
        $contentGroup = $this->getDoctrine()->getRepository(Content\Group::class)->findOneBy(array('id' => $group->getId()));
        $contents = $this->getDoctrine()->getRepository(Content::class)->findBy(array('group' => $content->getGroup()));
        $em = $this->getDoctrine()->getManager();
        foreach ($contents as $content) {
            foreach ($content->getImages() as $image) {
                foreach ($image->getSizes() as $size) {
                    $filename = $this->get("kernel")->getRootDir() . '/../public/' . $size->getPath() . $image->getName();
                    if (is_file($filename)) {
                        unlink($filename);
                    }
                }
                $em->remove($image);
                $em->flush();
            }
        }

        $category = $this->getDoctrine()->getRepository(Category::class)->findOneById($catid);
        $lang = $this->getDoctrine()->getRepository(Language::class)->findOneBy(array('short_name' => $langid));

        return new JsonResponse(array('html' => $this->getContent($category, $lang)));
    }

    /**
     * @Template()
     * @Route("/addnewsite", name="admin_add_new_site")
     */
    public function addNewSiteAction(Request $request) {
        $parent_level = $request->request->get('parent_level');
        $parent_catid = $request->request->get('parent_catid');
        $parent_cattyp = $request->request->get('parent_cattyp');
        $new_catname = $request->request->get('new_catname');

        $reposCategory = $this->getDoctrine()->getRepository(Category::class);
        $url_key = strtolower($new_catname);
        $category = $reposCategory->findOneBy(array('url_key' => $url_key, 'parent_id' => $parent_catid));
        if ($category) {
            $i = 1;
            while ($category) {
                $url_key1 = $url_key . $i;
                $category = $reposCategory->findOneBy(array('url_key' => $url_key1, 'parent_id' => $parent_catid));
                $i++;
            }
            $url_key = $url_key1;
        }
        $category = $reposCategory->findOneBy(array('parent_id' => $parent_catid), array('order' => 'DESC'));
        if (!$category) {
            $order = 10;
        } else {
            $order = $category->getOrder() + 10;
        }

        $reposCatTyp = $this->getDoctrine()->getRepository(Category\Typ::class);
        $catTyp = $reposCatTyp->findOneById($parent_cattyp);

        $em = $this->getDoctrine()->getManager();
        $level = $parent_level + 1;
        $category = new Category();
        $category->setUrlKey($url_key);
        $category->setStatus(false);
        $category->setLevel($level);
        $category->setOrder($order);
        $category->setParentId($parent_catid);
        $category->setTyp($catTyp);
        $em->persist($category);
        $em->flush();

        $reposLanguage = $this->getDoctrine()->getRepository(Language::class);
        $languages = $reposLanguage->findAll();
        foreach ($languages as $language) {
            $label = new Label();
            $label->setLang($language);
            $label->setCategory($category);
            $label->setName($new_catname);
            $em->persist($label);
            $em->flush();
        }

        if ($category->getTyp() != NULL) {
            $cattyp = $category->getTyp()->getId();
        } else {
            $cattyp = 0;
        }


        $url_key = $this->generateUrl('admin_cms_detail', array('id' => $category->getId()), true);
        return new JsonResponse(array('name' => $new_catname, 'url_key' => $url_key, 'level' => $level, 'catid' => $category->getId(), 'cattyp' => $cattyp));
    }

    /**
     * @Template()
     * @Route("/ordermenu", name="admin_order_menu")
     */
    public function orderMenuAction(Request $request) {
        $catids = $request->request->get('catids');
        $reposCategory = $this->getDoctrine()->getRepository(Category::class);
        $em = $this->getDoctrine()->getManager();
        $countOrder = 10;
        foreach ($catids as $catid) {
            $category = $reposCategory->findOneById($catid);
            $category->setOrder($countOrder);
            $em->persist($category);
            $em->flush();
            $countOrder = $countOrder + 10;
        }
        return new JsonResponse(array('status' => TRUE));
    }

    /**
     * @Template()
     * @Route("/editsite", name="admin_edit_site")
     */
    public function editSiteAction(Request $request) {
        $catid = $request->request->get('catid');
        $catname = $request->request->get('catname');
        $em = $this->getDoctrine()->getManager();
        $reposCategory = $this->getDoctrine()->getRepository(Category::class);
        $url_key = strtolower($catname);
        $category = $reposCategory->findOneById($catid);
        $parent_id = $category->getParentId();
        $category = $reposCategory->findOneBy(array('url_key' => $url_key, 'parent_id' => $parent_id));

        if ($category) {
            $i = 1;
            while ($category) {
                $url_key1 = $url_key . $i;
                $category = $reposCategory->findOneBy(array('url_key' => $url_key1, 'parent_id' => $parent_id));
                $i++;
            }
            $url_key = $url_key1;
        }

        $category = $reposCategory->findOneById($catid);
        $category->setUrlKey($url_key);
        $em->persist($category);
        $em->flush();

        $reposLanguage = $this->getDoctrine()->getRepository(Language::class);
        $languages = $reposLanguage->findAll();
        $reposCategoryLabel = $this->getDoctrine()->getRepository(Category\Label::class);
        foreach ($languages as $language) {
            $categoryLabel = $reposCategoryLabel->findOneBy(array('lang' => $language, 'category' => $category));
            $categoryLabel->setName($catname);
            $em->persist($categoryLabel);
            $em->flush();
        }
//

        if ($category->getTyp() != NULL) {
            $cattyp = $category->getTyp()->getId();
        } else {
            $cattyp = 0;
        }

        $url_key = $this->generateUrl('admin_cms_detail', array('id' => $category->getId()), true);
        return new JsonResponse(array('name' => $categoryLabel->getName(), 'url_key' => $category->getUrlKey(), 'level' => $category->getLevel(), 'catid' => $category->getId(), 'cattyp' => $cattyp));
    }

    /**
     * @Template()
     * @Route("/deletesite", name="admin_delete_site")
     */
    public function deleteSiteAction(Request $request, ServiceCommon $serviceCommon) {
        $catid = $request->request->get('catid');
        $level = 0;
        $navigation = $serviceCommon->getNavigation('category', false);
        $found = FALSE;
        $arr_catids = array();
        foreach ($navigation as $navi) {
            if ($catid == $navi['catid']) {
                $found = TRUE;
                $level = $navi['level'];
                $arr_catids[] = $navi['catid'];
                continue;
            }

            if ($found && $navi['level'] > $level) {
                $arr_catids[] = $navi['catid'];
            }

            if ($found && $navi['level'] <= $level) {
                $found = FALSE;
            }
        }

        $em = $this->getDoctrine()->getManager();
        $reposCategory = $this->getDoctrine()->getRepository(Category::class);
        $reposCategoryLabel = $this->getDoctrine()->getRepository(Category\Label::class);

        foreach ($arr_catids as $catid) {
            $category = $reposCategory->findOneById($catid);
            $categoryLabels = $reposCategoryLabel->findBy(array('category' => $category));
            foreach ($categoryLabels as $categoryLabel) {
                $em->remove($categoryLabel);
                $em->flush();
            }
            $em->remove($category);
            $em->flush();
        }
        return new JsonResponse(array());
    }

    /**
     *
     * @param category $category
     * @param lang $lang
     * @return string
     */
    private function getContent($category, $lang, $add_content = false) {
        $arr_tinymceid = array();
        $html = '';
        $id = $category->getId();
        $category = $this->getDoctrine()->getRepository(Category::class)->getAdminOrderContents($id);
        if (is_null($category)) {
            $category = $this->getDoctrine()->getRepository(Category::class)->findOneById($id);
        }
        foreach ($category->getContents() as $content) {
            if ($content->getLang()->getId() == $lang->getId()) {
                $html .= $this->renderView('admin/cms/content.html.twig', array(
                    'uniqid' => $content->getId(),
                    'cat' => $category,
                    'lang' => $lang->getShortName(),
                    'status_checked' => $content->getStatus() ? 'checked = "checked"' : '',
                    'title' => $content->getTitle(),
                    'long_text' => $content->getLongText(),
                    'created_at' => $content->getCreatedAt(),
                    'image' => $content->getImage(),
                        )
                );
                $arr_tinymceid[] = '#content-' . $content->getId();
            }
        }

        if ($add_content) {
            $uniqid = uniqid();
            $html = $this->addContent($uniqid, $category, $lang) . $html;
            $arr_tinymceid[] = '#content-' . $uniqid;
        }

        $html .= $this->renderView('admin/cms/jstinymce.html.twig', array(
            'tinymceids' => implode(',', $arr_tinymceid)
                )
        );

        // all url keys
        $arr_url_keys = array();
        $cats = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($cats as $cat) {
            $arr_url_keys[$cat->getid()] = $cat->getUrlKey();
        }


        $html = $this->renderView('admin/cms/head.html.twig', array(
            'lang' => $lang->getShortName(),
            'cat' => $category,
            'content' => $html,
            'urlkeys' => $arr_url_keys
                )
        );
        return $html;
    }

    private function addContent($uniqid, $category, $lang) {
        $html = $this->renderView('admin/cms/content.html.twig', array(
            'uniqid' => $uniqid,
            'cat' => $category,
            'lang' => $lang->getShortName(),
            'status_checked' => '',
            'title' => '',
            'long_text' => '',
            'created_at' => '',
            'image' => ''
                )
        );
        return $html;
    }

    private function setContentUrlKey($category, $content, $em, $blog_url_key) {

        // prüfe ob bereits diese eine group hat
        $groupid = $content->getGroup()->getId();
        $group = $this->getDoctrine()->getRepository(Content\Group::class)->findOneById($groupid);
        if (!is_null($group->getUrlKey())) {
            return;
        }
        // set urlkey cor content if blog
        if ($category->getTyp()->getShortName() == 'BLOG') {
            $groups = $this->getDoctrine()->getRepository(Content\Group::class)->findAll();
            $isNotFound = FALSE;
            while (!$isNotFound) {
                $i = 0;
                $url_key_found = FALSE;
                foreach ($groups as $group) {
                    if ($group->getUrlKey() == $blog_url_key) {
                        $blog_url_key = $blog_url_key . $i++;
                        $url_key_found = TRUE;
                        break;
                    }
                }
                if (!$url_key_found) {
                    $isNotFound = TRUE;
                }
            }
            $groupid = $content->getGroup()->getId();
            $group = $this->getDoctrine()->getRepository(Content\Group::class)->findOneById($groupid);
            $group->setUrlKey($blog_url_key);
            $em->persist($group);
            $em->flush();
        }
    }

}
