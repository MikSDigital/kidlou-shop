<?php

namespace Closas\ShopBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_group")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Content\Group")
 */
class Group {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $status = false;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $url_key = NULL;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Content", mappedBy="group")
     */
    private $contents;

    public function __construct() {
        $this->contents = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Group
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Add content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     *
     * @return Group
     */
    public function addContent(\Closas\ShopBundle\Entity\Content $content) {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     */
    public function removeContent(\Closas\ShopBundle\Entity\Content $content) {
        $this->contents->removeElement($content);
    }

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContents() {
        return $this->contents;
    }

    /**
     * Set urlKey
     *
     * @param $urlKey
     *
     * @return Group
     */
    public function setUrlKey($urlKey) {
        $this->url_key = $urlKey;

        return $this;
    }

    /**
     * Get urlKey
     *
     * @return \varchar
     */
    public function getUrlKey() {
        return $this->url_key;
    }

}
