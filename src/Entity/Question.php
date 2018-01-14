<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question", indexes={@ORM\Index(name="event_key", columns={"event_id"})})
 * @ORM\Entity
 */
class Question
{
    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=1000, nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="hint", type="string", length=1000, nullable=true)
     */
    private $hint;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=1, nullable=false)
     */
    private $type = 'T';

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="string", length=2000, nullable=true)
     */
    private $data;

    /**
     * @var boolean
     *
     * @ORM\Column(name="optional", type="boolean", nullable=false)
     */
    private $optional = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aggregate", type="boolean", nullable=false)
     */
    private $aggregate = true;

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
     * @var Answer[]
     *
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question")
     */
    private $answers;

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Question
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set hint
     *
     * @param string $hint
     *
     * @return Question
     */
    public function setHint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * Get hint
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Question
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Question
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set optional
     *
     * @param boolean $optional
     *
     * @return Question
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;

        return $this;
    }

    /**
     * Get optional
     *
     * @return boolean
     */
    public function getOptional()
    {
        return $this->optional;
    }

    /**
     * Set aggregate
     *
     * @param boolean $aggregate
     *
     * @return Question
     */
    public function setAggregate($aggregate)
    {
        $this->aggregate = $aggregate;

        return $this;
    }

    /**
     * Get aggregate
     *
     * @return boolean
     */
    public function getAggregate()
    {
        return $this->aggregate;
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
     * Set event
     *
     * @var \App\Entity\Event $event
     *
     * @return Question
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
     * @return Answer[]
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Gets a string representing the question
     * @return string
     */
    public function __toString()
    {
        return strval($this->type.': '.$this->text);
    }
}
