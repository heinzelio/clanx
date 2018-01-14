<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Companion
 *
 * @ORM\Table(name="companion", indexes={@ORM\Index(name="department_key", columns={"department_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanionRepository")
 */
class Companion
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_regular", type="boolean", nullable=false)
     */
    private $isRegular = false;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=1000, nullable=true)
     */
    private $remark;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Department
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Department")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private $department;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Companion
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Companion
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Companion
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isRegular
     *
     * @param boolean $isRegular
     *
     * @return Companion
     */
    public function setIsRegular($isRegular)
    {
        $this->isRegular = $isRegular;

        return $this;
    }

    /**
     * Get isRegular
     *
     * @return boolean
     */
    public function getIsRegular()
    {
        return $this->isRegular;
    }

    /**
     * Set Remark
     *
     * @param string $remark
     *
     * @return Companion
     */
    public function setRemark($remark)
    {
        $this->remark=$remark;
        return $this;
    }

    /**
     * Get Remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
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

    /**
     * Set department
     *
     * @param \AppBundle\Entity\Department $department
     *
     * @return Companion
     */
    public function setDepartment(\AppBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \AppBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Gets a string representing the companion
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
