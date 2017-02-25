<?php
namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;

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
        if (!$this->getAnswer()) {
            $attributes['data'] = $this->getDefaultAnswer();
        } else {
            $attributes['data'] = $this->getAnswer();
        }

        $attributes['label'] = $this->getText();
        $attributes['attr'] = array('data-hint' => $this->getHint(), ); // TODO: does not work yet
        $attributes['required'] = $this->getRequired();
        $attributes['property_path'] = 'questions[' . $this->getId() . '].answer';
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
     * returns the default answer that is used when 'default' is not defined
     * in the data field.
     * @return boolean
     */
    protected  function getUndefiniedDefaultAnswer()
    {
        return "";
    }
}
