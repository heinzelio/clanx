<?php

namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Question;



class SelectionQuestionViewModel extends BaseQuestionViewModel
{
    /**
     * @var string
     */
    private $answer;

    /**
     * @param Question $q
     */
    function __construct(Question $q)
    {
        parent::__construct($q);
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
            // I know, this looks silly, right?
            $attributes['data'] = $this->getAnswer()->getAnswer();
        }

        $attributes['label'] = $this->getText();
        $attributes['attr'] = array('data-hint' => $this->getHint(), ); // TODO: does not work yet
        $attributes['required'] = $this->getRequired();
        $attributes['property_path'] = 'questions[' . $this->getId() . '].answer';
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
}
