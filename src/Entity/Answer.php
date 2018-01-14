<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="answer", indexes={@ORM\Index(name="question_key", columns={"question_id"}), @ORM\Index(name="commitment_key", columns={"commitment_id"})})
 * @ORM\Entity
 */
class Answer
{
    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="string", length=1000, nullable=true)
     */
    private $answer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \App\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    private $question;

    /**
     * @var \App\Entity\Commitment
     *
     * @ORM\ManyToOne(targetEntity="Commitment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="commitment_id", referencedColumnName="id")
     * })
     */
    private $commitment;



    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return Answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
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
     * Set question
     *
     * @var \App\Entity\Question $question
     *
     * @return Answer
     */
    public function setQuestion(\App\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \App\Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set commitment
     *
     * @var \App\Entity\Commitment $commitment
     *
     * @return Answer
     */
    public function setCommitment(\App\Entity\Commitment $commitment = null)
    {
        $this->commitment = $commitment;

        return $this;
    }

    /**
     * Get commitment
     *
     * @return \App\Entity\Commitment
     */
    public function getCommitment()
    {
        return $this->commitment;
    }
}
