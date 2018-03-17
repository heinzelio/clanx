<?php
namespace App\Service\Flash;

class InfoFlash extends BaseFlash
{
    public function getKey(){return "info";}
    public function getBootstrapClass(){ return "info"; }
    public function getTranslatorKey(){ return "flash.info"; }
}
