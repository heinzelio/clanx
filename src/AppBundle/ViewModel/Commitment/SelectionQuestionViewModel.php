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
        $attributes['label'] = $this->getText();
        $attributes['attr'] = array('data-hint' => $this->getHint(), ); // TODO: does not work yet
        $attributes['required'] = $this->getRequired();
        $attributes['property_path'] = 'questions[' . $this->getId() . '].answer';
        $attributes['choices'] = $this->getChoices();
        $attributes['data'] = $this->getDefault();
        $attributes['required'] = $this->getRequired();

        // todo:
        // if not required
        //      'placeholder' => 'Choose your gender',
        //     'required'    => false,
        //         'empty_data'  => null

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
     * Gets the default selection.
     * @return string
     */
    public function getDefault()
    {
        $arr = $this->getData();
        if(isset($arr["default"]))
        {
            return $arr["default"];
        }
        return "";
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
