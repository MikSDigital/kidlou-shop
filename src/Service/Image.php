<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManager;
use App\Entity\Image As Fileimage;
use Symfony\Component\HttpFoundation\RequestStack;

class Image {

    /**
     * @var em EntityManager
     */
    private $em;

    /**
     * @var fileExtension
     */
    private $fileExtension;

    /**
     * @var fileClientOriginalName
     */
    private $fileClientOriginalName;

    /**
     * @var fileClientMimeType
     */
    private $fileClientMimeType;

    /**
     * @var imageSizes
     */
    private $imageSizes = array();

    /**
     * @var fileNames
     */
    private $fileName;

    /**
     *
     * @var type fileimage
     */
    private $fileimage;

    /**
     * @var type ¨
     */
    private $requestStack;

    /**
     * @var type kernel
     */
    private $kernel;

    /**
     * @var type imageResizer
     */
    private $imageResizer;

    /**
     * @var type request
     */
    private $request;

    /**
     * @var type file
     */
    private $file;

    /**
     * @var type arr_file
     */
    private $arr_file;

    /**
     *
     * @var type boolean
     */
    private $is_new = false;

    /**
     * @var type string
     */
    private $strArray;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, RequestStack $requestStack, $kernel, $imageResizer) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->kernel = $kernel;
        $this->imageResizer = $imageResizer;
        $this->setCurrentRequest();
    }

    /**
     * set the request
     */
    private function setCurrentRequest() {
        $this->request = $this->requestStack->getCurrentRequest();
        $this->arr_file = $this->request->files->all();
    }

    /**
     * @return type filesrequest
     */
    private function getRequest() {
        return $this->file;
    }

    /**
     * @return type boolean
     */
    public function isFile() {
        if ($this->file) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $param
     * @return type file
     */
    public function setRequestByParams($param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '') {
        if ($param1 != '' && $param2 != '' && $param3 != '' && $param4 != '' && $param5 != '') {
            if (isset($this->arr_file[$param1][$param2][$param3][$param4][$param5])) {
                $this->file = $this->arr_file[$param1][$param2][$param3][$param4][$param5];
            }
        } else if ($param1 != '' && $param2 != '' && $param3 != '' && $param4 != '' && $param5 == '') {
            if (isset($this->arr_file[$param1][$param2][$param3][$param4])) {
                $this->file = $this->arr_file[$param1][$param2][$param3][$param4];
            }
        } else if ($param1 != '' && $param2 != '' && $param3 != '' && $param4 == '' && $param5 == '') {
            if (isset($this->arr_file[$param1][$param2][$param3])) {
                $this->file = $this->arr_file[$param1][$param2][$param3];
            }
        } else if ($param1 != '' && $param2 != '' && $param3 == '' && $param4 == '' && $param5 == '') {
            if (isset($this->arr_file[$param1][$param2])) {
                $this->file = $this->arr_file[$param1][$param2];
            }
        } else if ($param1 != '' && $param2 == '' && $param3 == '' && $param4 == '' && $param5 == '') {
            if (isset($this->arr_file[$param1])) {
                $this->file = $this->arr_file[$param1];
            }
        }
        if ($this->file) {
            $this->init();
        }
        return $this;
    }

    private function init() {
        $this->fileExtension = $this->getRequest()->guessExtension();
        $this->fileClientOriginalName = $this->getRequest()->getClientOriginalName();
        $this->fileClientMimeType = $this->getRequest()->getClientMimeType();
        $this->imageSizes = $this->em->getRepository(Image\Size::class)->findAll();
        $this->uniqueName = md5(uniqid());
        $this->fileName = $this->uniqueName . '.' . $this->getRequest()->guessExtension();
    }

    /**
     *
     * @return string filename
     */
    private function getFilename() {
        return $this->fileName;
    }

    /**
     *
     * @return string uniquename
     */
    private function getUniqueName() {
        return $this->uniqueName;
    }

    /**
     * get images paths
     */
    private function getImageSizes() {
        return $this->imageSizes;
    }

    /**
     * @return fileExtension
     */
    private function getFileExtension() {
        return $this->fileExtension;
    }

    /**
     * @return fileClientOriginalName
     */
    private function getClientOriginalName() {
        return $this->fileClientOriginalName;
    }

    /**
     * @return fileClientMimeType
     */
    private function getClientMimeType() {
        return $this->fileClientMimeType;
    }

    /**
     * @return string
     */
    private function getWebDir() {
        return $this->kernel->getRootDir() . '/../public/';
    }

    /**
     * @return string
     */
    private function getWebTmpDir() {
        return $this->kernel->getRootDir() . '/../public/tmp/';
    }

    /**
     * @return type image
     */
    public function getImage() {
        return $this->fileimage;
    }

    /**
     * @return Image
     */
    public function addImage() {
        $this->fileimage = new Fileimage();
        $this->is_new = true;
        return $this;
    }

    /**
     * @return Image
     */
    public function getImageByContent($id) {
        if ($id) {
            $content = $this->em->getRepository(Content::class)->findOneBy(array('id' => $id));
            if (is_null($content->getImage())) {
                $this->fileimage = new Fileimage();
                $this->is_new = true;
            } else {
                $this->fileimage = $content->getImage();
                $this->is_new = false;
            }
        }
        return $this;
    }

    /**
     * @return Image
     */
    public function getImageByEntity($entity, $id) {
        if ($id) {
            $_entity = $this->em->getRepository($entity . '::class')->findOneBy(array('id' => $id));
            if (is_null($_entity->getImage())) {
                $this->fileimage = new Fileimage();
                $this->is_new = true;
            } else {
                $this->fileimage = $_entity->getImage();
                $this->is_new = false;
            }
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsNew() {
        return $this->is_new;
    }

    /**
     * @return Image
     */
    public function removeImageByContent() {
        if ($this->getImage()) {
            foreach ($this->getImageSizes() as $key => $size) {
                $filename = $this->getWebDir() . $size->getPath() . $this->getImage()->getName();
                if (is_file($filename)) {
                    unlink($filename);
                }
            }
        }
        return $this;
    }

    /**
     * @return Image
     */
    public function removeImageByEntity() {
        if ($this->getImage()) {
            foreach ($this->getImageSizes() as $key => $size) {
                $filename = $this->getWebDir() . $size->getPath() . $this->getImage()->getName();
                if (is_file($filename)) {
                    unlink($filename);
                }
            }
        }
        return $this;
    }

    /**
     * save image
     */
    public function save($is_default = 0) {
        $this->saveDb($is_default);
        $this->saveFilesystem();
        return $this;
    }

    /**
     *
     * @param type $is_default
     */
    private function saveDb($is_default) {
        $this->getImage()->setName($this->getFilename());
        $this->getImage()->setOriginalName($this->getClientOriginalName());
        $this->getImage()->setMimetyp($this->getClientMimeType());
        $this->getImage()->setIsDefault($is_default);
        if ($this->getIsNew()) {
            foreach ($this->getImageSizes() as $size) {
                $this->getImage()->addSize($size);
            }
        }
        $this->em->persist($this->getImage());
        $this->em->flush();
    }

    private function saveFilesystem() {
        $targetDir = NULL;
        $fileName = NULL;
        if (!file_exists($this->getWebTmpDir())) {
            mkdir($this->getWebTmpDir(), 0755, true);
        }
        $this->getRequest()->move($this->getWebTmpDir(), $this->getFilename());
        // resize File
        foreach ($this->getImageSizes() as $size) {
            $targetDir = $this->getWebDir() . $size->getPath();
            $this->imageResizer->resizeImage($this->getWebTmpDir() . $this->getFilename(), $targetDir, $this->getUniqueName(), $size->getHeight());
        }
    }

}
