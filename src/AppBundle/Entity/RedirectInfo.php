<?php

namespace AppBundle\Entity;

/**
 * Entity used to control the redirection function
 * from a generically usable page back to a specific page the user came from.
 * (example 1: department_show --> mail_edit --> mail_send --> department_show)
 * (example 2: event_show      --> mail_edit --> mail_send --> event_show)
 */
class RedirectInfo
{
    const SESSION_KEY = 'RedirectInfo';

    private $routeName;
    private $arguments;

    public function getRouteName(){return $this->routeName;}
    public function setRouteName($n){$this->routeName=$n;return $this;}

    public function getArguments(){return $this->arguments;}
    public function setArguments($a){$this->arguments = $a;return $this;}
    public function addArgument($name,$value)
    {
        if(!$this->arguments){
            $this->arguments=array($name,$value);
        }
        else {
            $this->arguments[$name] = $value;
        }
        return $this;
    }
}
