<?php
namespace AppBundle\Service\Flash;

class SuccessFlash extends BaseFlash
{
    public function getKey(){return "success";}
    public function getBootstrapClass(){ return "success"; }
    public function getTranslatorKey(){ return "flash.success"; }
}
