<?php

namespace App\Entity;

class  BulkEntry
{
    /**
     * @var integer
     */
    private $id;

    /**
     * checked
     * @var boolean
     */
    private $checked;


    /**
    * Gets the id of the entity
    * @return integer
    */
   public function getId()
   {
       return $this->id;
   }
    /**
    * Sets the id of the entity
    * @param integer  id
    * @return self
    */
   public function setId($id)
   {
       $this->id = $id;
       return $this;
   }
    /**
    * Get the value of checked
    *
    * @return boolean
    */
   public function getChecked()
   {
       return $this->checked;
   }
    /**
    * Set the value of checked
    * @param boolean checked
    * @return self
    */
   public function setChecked($checked)
   {
       $this->checked = $checked;
       return $this;
   }
}
