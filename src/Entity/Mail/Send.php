<?php

namespace App\Entity\Mail;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Mail;

/**
 * @ORM\Entity
 * @ORM\Table(name="mail_send")
 * @ORM\Entity(repositoryClass="App\Repository\Mail\Send")
 */
class Send {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $to_email;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $subject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mail", inversedBy="sends")
     * @ORM\JoinColumn(name="mail_id", referencedColumnName="id")
     */
    private $mail;

    /**
     * Set toEmail
     *
     * @param string $toEmail
     *
     * @return Send
     */
    public function setToEmail($toEmail) {
        $this->to_email = $toEmail;

        return $this;
    }

    /**
     * Get toEmail
     *
     * @return string
     */
    public function getToEmail() {
        return $this->to_email;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Send
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Send
     */
    public function setBody($body) {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Send
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Send
     */
    public function setUpdatedAt($updatedAt) {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * Set mail
     *
     * @param \App\Entity\Mail $mail
     *
     * @return Send
     */
    public function setMail(\App\Entity\Mail $mail = null) {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return \App\Entity\Mail
     */
    public function getMail() {
        return $this->mail;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
