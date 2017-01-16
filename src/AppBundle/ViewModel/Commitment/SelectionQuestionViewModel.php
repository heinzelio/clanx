<?php

namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class SelectionQuestionViewModel extends BaseQuestionViewModel
{
    /**
     * @var string
     */
    private $answer;

    /**
     * @var string[]
     */
    private $choices = array();

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
        $this->choices[$choice] = $choice;
        return $this;
    }

    /**
     * @param string[]
     * @return SelectionQuestionViewModel
     */
    public function setChoices($choices)
    {
        if ($choices==null) {
            $choices = array();
        }
        $this->choices = $choices;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getChoices()
    {
        return $this->choices;
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

        return $attributes;
    }
}
