<?php
namespace AppBundle\ViewModel\Commitment;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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
     * @return string The answer text
     */
    public function getAnswer(){
        return $this->answer;
    }

    /**
     * @param string $value the answer text
     * @return TextQuestionViewModel
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
            if ($this->hasDefault()) {
                $attributes['data'] = $this->getDefaultAnswer();
            }
        } else {
            $attributes['data'] = $this->getAnswer();
        }

        $attributes['label'] = $this->getText();
        $attributes['attr'] = array('data-hint' => $this->getHint(), ); // TODO: does not work yet
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

    /**
     * Validation callback method (defined in base class)
     * @param  ExecutionContextInterface $context
     */
    public function validateAnswer(ExecutionContextInterface $context)
    {
        // TODO: localization
        $message = "Antwort erforderlich";
        if ($this->getRequired() && !$this->getAnswer()) {
            $context->buildViolation($message)
                ->atPath($this->getPropertyPath())
                ->addViolation();
        }
    }
}
