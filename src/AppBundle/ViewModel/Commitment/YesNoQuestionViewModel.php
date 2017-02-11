<?php
namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Entity\Question;

/**
 * yes/no question data to show on the commitment form
 */
class YesNoQuestionViewModel extends BaseQuestionViewModel
{

    /**
     * Is the answer to the question `yes` ($yes=true) or `no`
     * @var boolean
     */
    private $yes;

    /**
     * @param Question $q
     */
    function __construct(Question $q)
    {
        parent::__construct($q);
    }

    /**
     * Is the answer `yes`
     * @return boolean Returns true if the answer is yes
     */
    public function getYes(){return $this->yes;}

    /**
     * Sets the Answer
     * @param boolean $value
     * @return YesNoQuestionViewModel
     */
    public function setYes($value)
    {
        $this->yes = $value;
        return $this; // for setter chains
    }

    /**
     * Gets the string that identifies this question type in the database.
     * @return [type] [description]
     */
    public function getTypeString(){return "F";}

    /**
     * @return boolean True, if the answer is 'yes'
     */
    public function getAnswer(){return $this->getYes();}

    /**
     * @param boolean $value True, if the answer is 'yes'
     * @return YesNoQuestionViewModel
     */
    public function setAnswer($value)
    {
        $this->setYes($value);
        return $this; // for setter chains
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return CheckboxType::class;
    }

    /**
     * @param  array $attributes
     * @return array
     */
    public function fillAttributes($attributes)
    {
        if (!$this->getAnswer()) {
            $attributes['attr']['checked'] = $this->getDefaultAnswer();
        } else {
            // I know, this looks silly, right?
            $attributes['attr']['checked'] = $this->getAnswer()->getAnswer();
        }

        $attributes['label'] = $this->getText();
        $attributes['attr']['data-hint'] = $this->getHint(); // TODO: does not work yet
        $attributes['required'] = $this->getRequired();
        $attributes['property_path'] = 'questions[' . $this->getId() . '].answer';
        $attributes['data'] = $this->getDefaultAnswer();
        $attributes['required'] = $this->getRequired();

        return $attributes;
    }

    /**
     * Gets an array of selection possibilities
     * @return array
     */
    public function getSelection()
    {
        return array(true => true, false => false);
    }

    /**
     * returns the default answer that is used when 'default' is not defined
     * in the data field.
     * @return boolean
     */
    protected  function getUndefiniedDefaultAnswer()
    {
        return false;
    }
}
