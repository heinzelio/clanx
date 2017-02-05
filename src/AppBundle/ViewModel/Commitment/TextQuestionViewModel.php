<?php
namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Entity\Question;

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
    public function getTypeString(){return "T";}

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
        $attributes['data'] = $this->getDefaultText();
        $attributes['required'] = $this->getRequired();

        return $attributes;
    }

    /**
     * Gets an array of selection possibilities
     * @return array
     */
    public function getSelection()
    {
        return array();
    }

    /**
     * Gets the default text
     * @return string
     */
    public function getDefaultText()
    {
        $arr = $this->getData();
        if(isset($arr["default"]))
        {
            return $arr["default"];
        }
        return "";
    }
}
