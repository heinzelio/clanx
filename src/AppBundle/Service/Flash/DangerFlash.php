<?php
namespace AppBundle\Service\Flash;

class DangerFlash extends BaseFlash
{
    public function getKey(){return "danger";}
    public function getBootstrapClass(){ return "danger"; }
    public function getTranslatorKey(){ return "flash.danger"; }
}
