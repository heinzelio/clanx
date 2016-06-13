<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @var string
     * @ORM\Column(name="forename", type="string", length=200, nullable=true)
     */
    private $forename;

    /**
     * @var string
     * @ORM\Column(name="surname", type="string", length=200, nullable=true)
     */
    private $surname;

    /**
     * @var string
     * @ORM\Column(name="gender", type="string", length=1, nullable=false)
     */
    private $gender='M';

    /**
     * @var \DateTime
     * @ORM\Column(name="date_of_birth", type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @var string
     * @ORM\Column(name="street", type="string", length=200, nullable=true)
     */
    private $street;

    /**
     * @var string
     * @ORM\Column(name="zip", type="string", length=10, nullable=true)
     */
    private $zip;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=200, nullable=true)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", length=200, nullable=true)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="occupation", type="string", length=200, nullable=true)
     */
    private $occupation;


    /**
     * Set forename
     * @param string $forename
     * @return User
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * Get forename
     * @return string
     */
    public function getForename()
    {
        return $this->forename;
    }

    /**
     * Set surname
     * @param string $surname
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    public function getFullname()
    {
        return $this->forename." ".$this->surname;
    }

    /**
     * Set gender
     * @param string $gender
     * @return User
     */
    public function setGender($gender){
        $this->gender=$gender;
        return $this;
    }

    /**
     * Get gender
     * @return string
     */
    public function getGender(){
        return $this->gender;
    }

    /**
     * Set dateOfBirth
     * @param \DateTime $dateOfBirth
     * @return User
     */
    public function setDateOfBirth($dateOfBirth){
        $this->dateOfBirth=$dateOfBirth;
        return $this;
    }

    /**
     * Get dateOfBirth
     * @return \DateTime
     */
    public function getDateOfBirth(){
        return $this->dateOfBirth;
    }

    /**
     * Set street
     * @param string $street
     * @return User
     */
    public function setStreet($street){
        $this->street=$street;
        return $this;
    }

    /**
     * Get street
     * @return string
     */
    public function getStreet(){
        return $this->street;
    }

    /**
     * Set zip
     * @param string $zip
     * @return User
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set city
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phone
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set occupation
     * @param string $occupation
     * @return User
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Returns true, if the user is chief of the given department.
     * @return boolean
     */
    public function isChiefOf(Department $department)
    {
        if(! $department)
        {
            return false;
        }
        if(! $department->getChiefUser())
        {
            return false;
        }
        return $this->id==$department->getChiefUser()->getId();
    }

    /**
     * Returns true, if the user is deputy of the given department.
     * @return boolean
     */
    public function isDeputyOf(Department $department)
    {
        if(! $department)
        {
            return false;
        }
        if(! $department->getDeputyUser())
        {
            return false;
        }
        return $this->id==$department->getDeputyUser()->getId();
    }

    /**
     * Gets a string representing the user
     * @return string
     */
    public function __toString()
    {
        if($this->forename && $this->surname)
        {
            return $this->forename.' '.$this->surname;
        }
        if($this->forename){
            return $this->forename.' ('.$this->username.')';
        }
        return $this->username.' ('.$this->email.')';
    }

}
