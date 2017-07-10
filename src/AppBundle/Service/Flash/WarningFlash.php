<?php
namespace AppBundle\Service\Flash;

class WarningFlash extends BaseFlash
{
    public function getKey(){return "warning";}
    public function getBootstrapClass(){ return "warning"; }
    public function getTranslatorKey(){ return "flash.warning"; }
}
