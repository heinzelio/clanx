<?php
namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;

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
     * true, if the question has been answered.
     * @var boolean
     */
    private $answered = false;

    /**
     * @param Question $q
     * @param Anser $a
     */
    function __construct(Question $q, Answer $a=null)
    {
        parent::__construct($q);
        if ($a) {
            $this->yes = ($a->getAnswer()=='1');
            $this->answered = true;
        }
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
        $this->answered = true;
        return $this; // for setter chains
    }

    /**
     * Gets the string that identifies this question type in the database.
     * @return [type] [description]
     */
    public function getTypeString(){return "F";}

    /**
     * @return string '1', if the answer is 'yes', otherwise '0'
     */
    public function getAnswer(){
        if ($this->getYes()) {
            return '1';
        } else {
            return '0';
        }
    }

    /**
     * @param string $value '1', if the answer is 'yes', otherwise '0'
     * @return YesNoQuestionViewModel
     */
    public function setAnswer($value)
    {
        $this->setYes($value);
        $this->answered = true;
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
        if ($this->answered) {
            $data = $this->getYes();
        } else {
            $data = $this->getDefaultAnswer();
        }
        $attributes['label'] = $this->getText();
        $attributes['data'] = $data;
        $attributes['attr']['checked'] = $data;
        $attributes['attr']['data-hint'] = $this->getHint(); // TODO: does not work yet
        $attributes['required'] = $this->getRequired();
        $attributes['property_path'] = $this->getPropertyPath();
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

    /**
     * Validation callback method (defined in base class)
     * @param  ExecutionContextInterface $context
     */
    public function validateAnswer(ExecutionContextInterface $context)
    {
        //does not do anything. when yes/no always have a default.
    }
}
