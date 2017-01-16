<?php
namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * yes/no question data to show on the commitment form
 */
class TextQuestionViewModel extends BaseQuestionViewModel
{
    /**
     * @var string
     */
    private $answer;

    /**
     * @return boolean True, if the answer is 'yes'
     */
    public function getAnswer(){
        return $this->answer;
    }

    /**
     * @param boolean $value True, if the answer is 'yes'
     * @return YesNoQuestionViewModel
     */
    public function setAnswer($value)
    {
        $this->answer=$value;
        return $this; // for setter chains
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return TextareaType::class;
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

        return $attributes;
    }
}
