<?php

namespace AppBundle\Entity;

use AppBundle\Entity\BulkEntry;

class Bulk
{
    /**
     * @var BulkEntry[]
     */
    private $entries = array();

    /**
     * @var string[]
     */
    private $actionChoices = array();

    /**
     * bulk action
     * @var string
     */
    private $action;


    /**
     * @return BulkEntry[]
     */
    public function getEntries(){ return $this->entries; }

    /**
     * @param BulkEntry[] entries
     * @return self
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
        return $this;
    }

    /**
     * the currently selected action for this bulk.
     * @return string
     */
    public function getAction(){ return $this->action; }

    /**
     * The action that has to be done with the selected entries of the bulk.
     * @param string action
     * @return self
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Adds an entry to the existing arrays of entries
     * @param BulkEntry $entry
     */
    public function addEntry(BulkEntry $entry)
    {
        array_push($this->entries, $entry);
    }

    /**
     * Adds an action choice to the collection.
     * Not that key must be unique, and the value is shown to the user.
     * So maybe you want to make it localizable or so.
     * @param string $key
     * @param string $value
     */
    public function addActionChoice($key,$value)
    {
        $this->actionChoices[$value] = $key;
        // Yes, I know. This looks wrong.
        // But this ist the way how selection widges work in symfony.
    }

    /**
     * Get the value of Action Choices
     *
     * @return string[]
     */
    public function getActionChoices()
    {
        return $this->actionChoices;
    }

}
