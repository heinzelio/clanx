<?php

namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;



class SelectionQuestionViewModel extends BaseQuestionViewModel
{
    /**
     * @var string
     */
    private $answer;

    /**
     * @param Question $q
     * @param Anser $a
     */
    function __construct(Question $q, Answer $a=null)
    {
        parent::__construct($q);
        if ($a) {
            $this->answer = $a->getAnswer();
        }
    }

    /**
     * Gets the string that identifies this question type in the database.
     * @return [type] [description]
     */
    public function getTypeString(){return "S";}

    /**
     * @param string $answer
     * @return SelectionQuestionViewModel
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param string $choice
     * @return SelectionQuestionViewModel Returns self reference for chaining calls.
     */
    public function addChoice($choice='')
    {
        $choices = $this->getChoices();
        $choices[$choice] = $choice;
        $this->setChoices($choices);
        return $this;
    }

    /**
     * @param string[]
     * @return SelectionQuestionViewModel
     */
    public function setChoices($choices)
    {
        $arr = $this->getData();
        if(isset($arr["choices"]))
        {
            $arr["choices"]=$choices;
            $this->setData($arr);
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getChoices()
    {
        $arr = $this->getData();
        if(isset($arr["choices"]))
        {
            return $arr["choices"];
        }
        return array();
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return ChoiceType::class;
    }

    /**
     * @param  array $attributes
     * @return array
     */
    public function fillAttributes($attributes)
    {
        if (!$this->getAnswer()) {
            $attributes['data'] = $this->getDefaultAnswer();
            $attributes['placeholder'] = 'Bitte wählen'; // TODO: translate!
            if (!$this->getRequired()) {
                # code...
                $attributes['empty_data'] = null;
            }
        } else {
            $attributes['data'] = $this->getAnswer();
        }

        $attributes['label'] = $this->getText();
        $attributes['attr'] = array('data-hint' => $this->getHint(), ); // TODO: does not work yet
        $attributes['required'] = $this->getRequired();
        $attributes['property_path'] = $this->getPropertyPath();
        $attributes['choices'] = $this->getChoices();
        $attributes['required'] = $this->getRequired();

        return $attributes;
    }

    /**
     * Gets an array of selection possibilities
     * @return array
     */
    public function getSelection()
    {
        return $this->getFlatChoices();
    }

    /**
     * returns the default answer that is used when 'default' is not defined
     * in the data field.
     * @return string
     */
    protected  function getUndefiniedDefaultAnswer()
    {
        return null;
    }

    private function getFlatChoices()
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($this->getChoices())
        );

        $arr=array();
        foreach($iterator as $choice) {
          $arr[$choice] = 0;
        }

        return $arr;
    }

    /**
     * Validation callback method (defined in base class)
     * @param  ExecutionContextInterface $context
     */
    public function validateAnswer(ExecutionContextInterface $context)
    {
        // TODO: localization
        $message = "Antwort erforderlich";
        if ($this->getRequired() && !$this->getAnswer()) {
            $context->buildViolation($message)
                ->atPath($this->getPropertyPath())
                ->addViolation();
        }
    }
}
