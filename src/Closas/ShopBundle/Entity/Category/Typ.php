<?php

namespace Closas\ShopBundle\Entity\Category;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Entity\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="category_typ")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Category\Typ")
 */
class Typ {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $short_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Category", mappedBy="typ")
     */
    private $categories;

    public function __construct() {
        $this->categories = new ArrayCollection();
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
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Typ
     */
    public function setShortName($shortName) {
        $this->short_name = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName() {
        return $this->short_name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Typ
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add category
     *
     * @param \Closas\ShopBundle\Entity\Category $category
     *
     * @return Typ
     */
    public function addCategory(\Closas\ShopBundle\Entity\Category $category) {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Closas\ShopBundle\Entity\Category $category
     */
    public function removeCategory(\Closas\ShopBundle\Entity\Category $category) {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Add content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     *
     * @return Typ
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

}
