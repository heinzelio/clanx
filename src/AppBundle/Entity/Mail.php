<?php

namespace AppBundle\Entity;

/**
 * Entity used to initialize the sendMail controller
 */
class Mail
{
    private $subject;
    private $text;
    private $sender;
    private $recipient;
    private $ccs;
    private $bccs;

    public function getSubject(){return $this->subject;}
    public function setSubject($s){$this->subject=$s; return $this;}

    public function getText(){return $this->text;}
    public function setText($t){$this->text=$t; return $this;}

    public function getSender(){return $this->sender;}
    public function setSender($s){$this->sender=$s; return $this;}

    public function getRecipient(){return $this->recipient;}
    public function setRecipient($r){$this->recipient=$r; return $this;}

    public function getCcs(){return $this->ccs;}
    // no setter.
    public function addCc($mail, $name = null)
    {
        if(!$this->ccs){
            $this->ccs = array($mail=>$name);
        }
        else {
            $this->ccs[$mail]=$name;
        }
        return $this;
    }

    public function getBccs(){
        return $this->bccs;
    }
    // no setter.
    public function addBcc($mail, $name = null)
    {
        if(!$this->bccs){
            $this->bccs = array($mail=>$name);
        }
        else {
            $this->bccs[$mail]=$name;
        }
        return $this;
    }
}
