<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Entity\Mail\Send;

/**
 * @ORM\Entity
 * @ORM\Table(name="mail")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Mail")
 */
class Mail {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $from_email;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $from_name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $bcc_email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Mail\Send", mappedBy="mail")
     */
    private $sends;

    /**
     * Constructor
     */
    public function __construct() {
        $this->sends = new ArrayCollection();
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
     * Set fromEmail
     *
     * @param string $fromEmail
     *
     * @return Mail
     */
    public function setFromEmail($fromEmail) {
        $this->from_email = $fromEmail;

        return $this;
    }

    /**
     * Get fromEmail
     *
     * @return string
     */
    public function getFromEmail() {
        return $this->from_email;
    }

    /**
     * Set fromName
     *
     * @param string $fromName
     *
     * @return Mail
     */
    public function setFromName($fromName) {
        $this->from_name = $fromName;

        return $this;
    }

    /**
     * Get fromName
     *
     * @return string
     */
    public function getFromName() {
        return $this->from_name;
    }

    /**
     * Set bccEmail
     *
     * @param string $bccEmail
     *
     * @return Mail
     */
    public function setBccEmail($bccEmail) {
        $this->bcc_email = $bccEmail;

        return $this;
    }

    /**
     * Get bccEmail
     *
     * @return string
     */
    public function getBccEmail() {
        return $this->bcc_email;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Mail
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Add send
     *
     * @param \Closas\ShopBundle\Entity\Mail\Send $send
     *
     * @return Mail
     */
    public function addSend(\Closas\ShopBundle\Entity\Mail\Send $send) {
        $this->sends[] = $send;

        return $this;
    }

    /**
     * Remove send
     *
     * @param \Closas\ShopBundle\Entity\Mail\Send $send
     */
    public function removeSend(\Closas\ShopBundle\Entity\Mail\Send $send) {
        $this->sends->removeElement($send);
    }

    /**
     * Get sends
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSends() {
        return $this->sends;
    }


    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Mail
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }
}
