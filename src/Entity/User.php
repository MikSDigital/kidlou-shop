<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_user")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\User")
 */
class User implements AdvancedUserInterface, \Serializable {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 6,
     *      max = 8,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean", options={"default":true})
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\Personal", mappedBy="user")
     */
    private $personals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order\Address", mappedBy="user")
     */
    private $addresses;

    public function __construct() {
        $this->personals = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->setUpdatedAt(new \DateTime());
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    public function getSalt() {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        return array('ROLE_USER');
    }

    public function eraseCredentials() {

    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->isActive;
    }

    public function getUsername() {
        return $this->username;
    }

    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
                // see section on salt below
                // $this->salt,
        ));
    }

    public function unserialize($serialized) {
        list (
                $this->id,
                $this->username,
                $this->password,
                $this->isActive,
                // see section on salt below
                // $this->salt
                ) = unserialize($serialized);
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
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
     * Set updatetAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get updatetAt
     *
     * @return $updated
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * Set updatetAt
     *
     * @param \DateTime $updatetAt
     *
     * @return User
     */
    public function setUpdatedAt($updatetAt) {
        $this->updated_at = $updatetAt;

        return $this;
    }

    /**
     * Add Personal
     *
     * @param \App\Entity\User\Personal $personal
     *
     * @return Personal
     */
    public function addPersonal(\App\Entity\User\Personal $personal) {
        $this->personals[] = $personal;

        return $this;
    }

    /**
     * Remove Personal
     *
     * @param \App\Entity\User\Personal $personal
     */
    public function removeCategory(\App\Entity\User\Personal $personal) {
        $this->personals->removeElement($personal);
    }

    /**
     * Get personals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersonals() {
        return $this->personals;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Add address
     *
     * @param \App\Entity\Order\Address $address
     *
     * @return Order
     */
    public function addAddress(\App\Entity\Order\Address $address) {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \App\Entity\Order\Address $address
     */
    public function removeAddress(\App\Entity\Order\Address $address) {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses() {
        return $this->addresses;
    }

}
