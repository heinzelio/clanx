<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity
 */
class Setting
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(name="can_register", type="boolean", nullable=false)
     */
    private $canRegister;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set canRegister
     *
     * @param boolean $canRegister
     *
     * @return Setting
     */
    public function setCanRegister($canRegister)
    {
        $this->canRegister = $canRegister;

        return $this;
    }

    /**
     * Get canRegister
     *
     * @return bool
     */
    public function getCanRegister()
    {
        return $this->canRegister;
    }
}
