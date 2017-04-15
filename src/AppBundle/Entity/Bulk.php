<?php

namespace AppBundle\Entity;

use AppBundle\Entity\BulkEntry;

class Bulk
{
    /**
     * @var BulkEntry[]
     */
    private $bulkEntries = array();

    /**
     * bulk action
     * @var string
     */
    private $bulkAction;

    /**
     * @return BulkEntry[]
     */
    public function getBulkEntries(){ return $this->bulkEntries; }

    /**
     * @param BulkEntry[] entries
     * @return self
     */
    public function setBulkEntries($bulkEntries)
    {
        $this->bulkEntries = $bulkEntries;
        return $this;
    }

    /**
     * the currently selected action for this bulk.
     * @return string
     */
    public function getBulkAction(){ return $this->bulkAction; }

    /**
     * The action that has to be done with the selected entries of the bulk.
     * @param string bulkAction
     * @return self
     */
    public function setBulkAction($bulkAction)
    {
        $this->bulkAction = $bulkAction;
        return $this;
    }

    public function addEntry(BulkEntry $entry)
    {
        array_push($this->bulkEntries, $entry);
    }
}
