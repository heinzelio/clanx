<?php
namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
        $attributes['label'] = $this->getText();
        $attributes['attr'] = array('data-hint' => $this->getHint(), ); // TODO: does not work yet
        $attributes['required'] = $this->getRequired();
        $attributes['property_path'] = 'questions[' . $this->getId() . '].answer';

        return $attributes;
    }
}
