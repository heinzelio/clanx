<?php
namespace AppBundle\Service\Flash;

abstract class BaseFlash
{
    public abstract function getKey();
    public abstract function getBootstrapClass();
    public abstract function getTranslatorKey();
}
