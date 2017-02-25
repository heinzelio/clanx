<?php
namespace AppBundle\ViewModel\Event;

class EventStatisticsViewModel
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var array
     */
    private $values;

    /**
     * @param string $value
     */
    public function setText($value='')
    {
        $this->text=$value;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
