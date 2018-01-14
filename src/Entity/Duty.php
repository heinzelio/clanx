<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Duty
 *
 * @ORM\Table(name="duty", indexes={@ORM\Index(name="shift_key", columns={"shift_id"}), @ORM\Index(name="user_key", columns={"user_id"}), @ORM\Index(name="event_key", columns={"event_id"})})
 * @ORM\Entity
 */
class Duty
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \App\Entity\Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private $event;

    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \App\Entity\Shift
     *
     * @ORM\ManyToOne(targetEntity="Shift")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shift_id", referencedColumnName="id")
     * })
     */
    private $shift;



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
     * Set event
     *
     * @var \App\Entity\Event $event
     *
     * @return Duty
     */
    public function setEvent(\App\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \App\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @var \App\Entity\User $user
     *
     * @return Duty
     */
    public function setUser(\App\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set shift
     *
     * @var \App\Entity\Shift $shift
     *
     * @return Duty
     */
    public function setShift(\App\Entity\Shift $shift = null)
    {
        $this->shift = $shift;

        return $this;
    }

    /**
     * Get shift
     *
     * @return \App\Entity\Shift
     */
    public function getShift()
    {
        return $this->shift;
    }
}
