<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity
 */
class Event
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var boolean
     * @ORM\Column(name="sticky", type="boolean", nullable=false)
     */
    private $sticky;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Event
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * @return boolean
     */
    public function getSticky()
    {
        return $this->sticky;
    }

    /**
     * Set sticky
     *
     * @param boolean $sticky
     *
     * @return Event
     */
    public function setSticky($sticky)
    {
        $this->sticky = $sticky;
        return $this;
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Returns true when enrollment on this Event is possible
     * @return boolean
     */
    public function enrollmentPossible()
    {
        return $this->date > new \DateTime();
    }

    /**
     * Returns true when event may be edited
     * @return boolean
     */
    public function mayEdit()
    {
        return $this->date > new \DateTime();
    }

    /**
     * Returns true when event may be deleted
     * @return boolean
     */
    public function mayDelete()
    {
        return $this->date > new \DateTime();
    }

    /**
     * Gets a string representing the department
     * @return string
     */
    public function __toString()
    {
        return strval($this->name);
    }
}
