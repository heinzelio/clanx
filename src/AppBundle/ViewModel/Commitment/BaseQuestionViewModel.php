<?php
namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Base class for all questions
 */
abstract class BaseQuestionViewModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * The text of the question.
     * @var string
     */
    private $text;

    /**
     * The description of the question.
     * This is basically just a bit more text if this is needed.
     * @var string
     */
    private $hint;

    /**
     * @var boolean
     */
    private $required;

    /**
     * @param integer $id
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     * @return BaseQuestionViewModel
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this; // for setter chains
    }

    /**
     * Gets the text.
     * @return string
     */
    public function getText(){return $this->text;}

    /**
     * Sets the text.
     * @param string $value
     * @return BaseQuestionViewModel
     */
    public function setText($value='')
    {
        $this->text = $value;
        return $this; // for setter chains
    }

    /**
     * Gets the hint
     * @return string
     */
    public function getHint(){return $this->hint;}

    /**
     * Sets the hint
     * @param string $value
     * @return BaseQuestionViewModel
     */
    public function setHint($value='')
    {
        $this->hint = $value;
        return $this; // for setter chains
    }

    /**
     * @return boolean
     */
    public function getRequired(){return $this->required;}

    /**
     * @param boolean $value
     * @return BaseQuestionViewModel
     */
    public function setRequired($value)
    {
        $this->required = $value;
        return $this; // for setter chains
    }

    /**
     * @param  string $prefix
     * @param  string $postfix
     * @return string
     */
    public function getFormFieldName($prefix='',$postfix='')
    {
        return $prefix . 'answer' . $this->id . $postfix;
    }

    /**
     * @return object This may be a bool, string, array, you name it.
     */
    abstract public function getAnswer();

    /**
     * @param object $value This may be a bool, string, array, you name it.
     */
    abstract public function setAnswer($value);

    /**
     *
     * @return string fully qualified class name.
     */
    abstract public function getFormType();

    /**
     * @param  array $attributes The attributes array for the question field.
     * This is an out parameter, it will be changed (filled) by this method
     */
    abstract public function fillAttributes($attributes);
}
